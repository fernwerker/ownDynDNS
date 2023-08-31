<?php

namespace netcup\DNS\API;

use RuntimeException;

final class Handler
{
    /**
     * @var array
     */
    private $log;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Payload
     */
    private $payload;

    /**
     * @var int
     */
    private $customerid;

    /**
     * @var string
     */
    private $apikey;

    /**
     * @var string
     */
    private $apipassword;

    public function __construct(array $config, array $payload)
    {
        $this->config = new Config($config);

        if (!$this->config->isValid()) {
            if ($this->config->isDebug()) {
                throw new RuntimeException('configuration invalid');
            } else {
                exit("configuration invalid\n");
            }
        }

        $this->payload = new Payload($payload);

        if (!$this->payload->isValid()) {
            if ($this->config->isDebug()) {
                throw new RuntimeException('payload invalid');
            } else {
                exit("payload invalid\n");
            }
        }

        if ($this->config->isAllowAnonymous()) {
            if ($this->payload->getUser() == 'anonymous') {
                if ($this->config->isDebug()) {
                    $this->doLog('anonymous login by client');
                }
            }
        } else {
            if (
                $this->config->getUsername() !== $this->payload->getUser() ||
                $this->config->getPassword() !== $this->payload->getPassword()
            ) {
                if ($this->config->isDebug()) {
                    throw new RuntimeException('credentials invalid');
                } else {
                    exit("credentials invalid\n");
                }
            }
        }

        if ($this->config->isAllowNetcupCreds()) {
            if ($this->payload->isValidNetcupCreds()) {
                if ($this->config->isDebug()) {
                    $this->doLog('received valid Netcup credentials');
                }
            }
            $this->customerid = $this->payload->getCustomerId();
            $this->apikey = $this->payload->getApiKey();
            $this->apipassword = $this->payload->getApiPassword();
        } else {
            $this->customerid = $this->config->getCustomerId();
            $this->apikey = $this->config->getApiKey();
            $this->apipassword = $this->config->getApiPassword();
        }

        if (is_readable($this->config->getLogFile())) {
            $this->log = json_decode(file_get_contents($this->config->getLogFile()), true);
        } else {
            $this->log[$this->payload->getDomain()] = [];
        }
    }

    public function __destruct()
    {
        $this->doExit();
    }

    /**
     * @param string $msg
     *
     * @return self
     */
    private function doLog($msg)
    {
        $this->log[$this->payload->getDomain()][] = sprintf('[%s] %s', date('c'), $msg);

        if ($this->config->isDebug()) {
            printf('[DEBUG] %s %s', $msg, PHP_EOL);
        }

        return $this;
    }

    private function doExit()
    {
        if (!$this->config->isLog()) {
            return;
        }

        if (!file_exists($this->config->getLogFile())) {
            if (!touch($this->config->getLogFile())) {
                printf('[ERROR] unable to create %s %s', $this->config->getLogFile(), PHP_EOL);
            }
        }

        // save only the newest 100 log entries for each domain
        $this->log[$this->payload->getDomain()] = array_reverse(array_slice(array_reverse($this->log[$this->payload->getDomain()]), 0, 100));

        if (!is_writable($this->config->getLogFile()) || !file_put_contents($this->config->getLogFile(), json_encode($this->log, JSON_PRETTY_PRINT))) {
            printf('[ERROR] unable to write %s %s', $this->config->getLogFile(), PHP_EOL);
        }
    }

    /**
     * @return self
     */
    public function doRun()
    {
        $clientRequestId = md5($this->payload->getDomain() . time());

        $dnsClient = new Soap\DomainWebserviceSoapClient();

        $loginHandle = $dnsClient->login(
            $this->customerid,
            $this->apikey,
            $this->apipassword,
            $clientRequestId
        );

        if (2000 === $loginHandle->statuscode) {
            $this->doLog('api login successful');
        } else {
            $this->doLog(sprintf('api login failed, message: %s', $loginHandle->longmessage));
        }

        // check if domain is restricted in config, force use of config values for domain and host
        if ($this->config->isRestrictDomain()) {
            $this->doLog('domain is restricted by .env file');
            $updateDomain = $this->config->getDomain();
            $updateDomainName = $this->config->getDomainName();
            $updateHost = $this->config->getHost();
            $this->doLog(sprintf('ignoring received domain, using configured domain: %s', $updateDomain));
        } else {
            $updateDomain = $this->payload->getDomain();
            $updateDomainName = $this->payload->getDomainName();
            $updateHost = $this->payload->getHost();
        }

        $infoHandle = $dnsClient->infoDnsRecords(
            $updateDomainName,
            $this->customerid,
            $this->apikey,
            $loginHandle->responsedata->apisessionid,
            $clientRequestId
        );

        $exists = false;
        $ipv4changes = false;
        $ipv6changes = false;
        $txtchanges = false;

        foreach ($infoHandle->responsedata->dnsrecords as $key => $record) {
            $recordHostnameReal = (!in_array($record->hostname, $this->payload->getMatcher())) ? $record->hostname . '.' . $updateDomainName : $updateDomainName;

            if ($recordHostnameReal === $updateDomain) {

                // found matching entry, no need to create one
                $exists = true;

                // update A Record if exists and IP has changed
                if ('A' === $record->type && $this->payload->getIpv4() &&
                    (
                        $this->payload->isForce() ||
                        $record->destination !== $this->payload->getIpv4()
                    )
                ) {
                    $record->destination = $this->payload->getIpv4();
                    $this->doLog(sprintf('IPv4 for %s set to %s', $record->hostname . '.' . $updateDomainName, $this->payload->getIpv4()));
                    $ipv4changes = true;
                }

                // update AAAA Record if exists and IP has changed
                if ('AAAA' === $record->type && $this->payload->getIpv6() &&
                    (
                        $this->payload->isForce()
                        || $record->destination !== $this->payload->getIpv6()
                    )
                ) {
                    $record->destination = $this->payload->getIpv6();
                    $this->doLog(sprintf('IPv6 for %s set to %s', $record->hostname . '.' . $updateDomainName, $this->payload->getIpv6()));
                    $ipv6changes = true;
                }

                // update TXT Record if exists and content has changed
                if ('TXT' === $record->type && $this->payload->getTxt() &&
                    (
                        $this->payload->isForce()
                        || $record->destination !== $this->payload->getTxt()
                    )
                ) {
                    $record->destination = $this->payload->getTxt();
                    $this->doLog(sprintf('TXT for %s set to %s', $record->hostname . '.' . $updateDomainName, $this->payload->getTxt()));
                    $txtchanges = true;
                }
            }
        }

        // if entry does not exist and createnewentry is true:
        if ( !$exists && $this->payload->getCreate() && $this->config->isAllowCreate() )
        {
            // init new record set containing empty array
            $newRecordSet = new Soap\Dnsrecordset();
            $newRecordSet->dnsrecords = array();

            foreach ($this->payload->getTypes() as $key => $type)
            {
                $record = new Soap\Dnsrecord();

                $record->hostname = $updateHost;
                $record->type = $type;
                $record->priority = "0"; // only for MX, can possibly be removed

                switch ($type) {
                    case 'A':
                        $record->destination = $this->payload->getIpv4();
                        break;
        
                    case 'AAAA':
                        $record->destination = $this->payload->getIpv6();
                        break;

                    case 'TXT':
                        $record->destination = $this->payload->getTxt();
                        break;
                }

                array_push($newRecordSet->dnsrecords, $record); // push new record into array
            }
            

            $dnsClient->updateDnsRecords(
                $updateDomainName,
                $this->customerid,
                $this->apikey,
                $loginHandle->responsedata->apisessionid,
                $clientRequestId,
                $newRecordSet
            );

            $this->doLog('dns recordset created');
        } 

        // if anything was changed, push the update and log
        if ($ipv4changes or $ipv6changes or $txtchanges) {
            $recordSet = new Soap\Dnsrecordset();
            $recordSet->dnsrecords = $infoHandle->responsedata->dnsrecords;

            $dnsClient->updateDnsRecords(
                $updateDomainName,
                $this->customerid,
                $this->apikey,
                $loginHandle->responsedata->apisessionid,
                $clientRequestId,
                $recordSet
            );

            $this->doLog('dns recordset updated');
        } else {
            $this->doLog('dns recordset NOT updated (no changes)');
        }

        $logoutHandle = $dnsClient->logout(
            $this->customerid,
            $this->apikey,
            $loginHandle->responsedata->apisessionid,
            $clientRequestId
        );

        if (2000 === $logoutHandle->statuscode) {
            $this->doLog('api logout successful');
        } else {
            $this->doLog(sprintf('api logout failed, message: %s', $loginHandle->longmessage));
        }

        if ($this->config->isReturnIp()) {
            if ($ipv4changes) {
                echo "IPv4 changed: " . $this->payload->getIpv4(), PHP_EOL;
            }
            if ($ipv6changes) {
                echo "IPv6 changed: " . $this->payload->getIpv6(), PHP_EOL;
            }
            if ($txtchanges) {
                echo "TXT changed: " . $this->payload->getTxt(), PHP_EOL;
            }
        }
        return $this;
    }
}
