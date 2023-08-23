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
     *
     * @return self
     */
    public function doRun()
    {
        $clientRequestId = md5($this->payload->getDomain() . time());

        $dnsClient = new Soap\DomainWebserviceSoapClient();

        $loginHandle = $dnsClient->login(
            $this->config->getCustomerId(),
            $this->config->getApiKey(),
            $this->config->getApiPassword(),
            $clientRequestId
        );

        if (2000 === $loginHandle->statuscode) {
            $this->doLog('api login successful');
        } else {
            $this->doLog(sprintf('api login failed, message: %s', $loginHandle->longmessage));
        }

        $infoHandle = $dnsClient->infoDnsRecords(
            $this->payload->getDomainName(),
            $this->config->getCustomerId(),
            $this->config->getApiKey(),
            $loginHandle->responsedata->apisessionid,
            $clientRequestId
        );

        // test: create new entry if it does not exist
        $createnewentry = true;
        $exists = false;
        $testing = true;

        $ipv4changes = false;
        $ipv6changes = false;
        $txtchanges = false;


        // TODO: delete, testing
        // echo "--- EXISTING ENTRIES BELOW ---", PHP_EOL;
        // $teststring = print_r($infoHandle->responsedata->dnsrecords, true);
        // echo $teststring, PHP_EOL;

        foreach ($infoHandle->responsedata->dnsrecords as $key => $record) {
            $recordHostnameReal = (!in_array($record->hostname, $this->payload->getMatcher())) ? $record->hostname . '.' . $this->payload->getDomainName() : $this->payload->getDomainName();


            if ($recordHostnameReal === $this->payload->getDomain()) {

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
                    $this->doLog(sprintf('IPv4 for %s set to %s', $record->hostname . '.' . $this->payload->getDomainName(), $this->payload->getIpv4()));
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
                    $this->doLog(sprintf('IPv6 for %s set to %s', $record->hostname . '.' . $this->payload->getDomainName(), $this->payload->getIpv6()));
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
                    $this->doLog(sprintf('TXT for %s set to %s', $record->hostname . '.' . $this->payload->getDomainName(), $this->payload->getTxt()));
                    $txtchanges = true;
                }
            }
        }

        // echo "--- Exists ---", $exists, PHP_EOL;
        // echo "--- Createnewentry ---", $createnewentry, PHP_EOL;

        // TODO: if entry does not exist and createnewentry is true:
        if ( !$exists && $this->payload->getCreate() && $this->config->isAllowCreate() )
        {
            // init new record set containing empty array
            $newRecordSet = new Soap\Dnsrecordset();
            $newRecordSet->dnsrecords = array();

            foreach ($this->payload->getTypes() as $key => $type)
            {
                $record = new Soap\Dnsrecord();
                
                // echo "getDomain: ", $this->payload->getDomain(), PHP_EOL;
                // echo "getDomainName: ", $this->payload->getDomainName(), PHP_EOL;
                // echo "getHost: ", $this->payload->getHost(), PHP_EOL;

                $record->hostname = $this->payload->getHost();
                $record->type = $type;
                $record->priority = "0"; // only for MX, can possibly be removed

                switch ($type) {
                    case 'A':
                        // echo "A record set: ", $this->payload->getIpv4(), PHP_EOL;
                        $record->destination = $this->payload->getIpv4();
                        break;
        
                    case 'AAAA':
                        $record->destination = $this->payload->getIpv6();
                        break;

                    case 'TXT':
                        $record->destination = $this->payload->getTxt();
                        break;
                }
                // echo "destination: ", $record->destination, PHP_EOL;
                // echo "--- NEW ENTRY BELOW ---", PHP_EOL;
                // $teststring = print_r($record, true);
                // echo $teststring, PHP_EOL;

                array_push($newRecordSet->dnsrecords, $record); // push new record into array
            }
            
            // echo "--- newRecordSet ---", PHP_EOL;
            // $teststring = print_r($newRecordSet, true);
            // echo $teststring, PHP_EOL;

            $dnsClient->updateDnsRecords(
                $this->payload->getDomainName(),
                $this->config->getCustomerId(),
                $this->config->getApiKey(),
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
                $this->payload->getDomainName(),
                $this->config->getCustomerId(),
                $this->config->getApiKey(),
                $loginHandle->responsedata->apisessionid,
                $clientRequestId,
                $recordSet
            );

            $this->doLog('dns recordset updated');
        } else {
            $this->doLog('dns recordset NOT updated (no changes)');
        }

        $logoutHandle = $dnsClient->logout(
            $this->config->getCustomerId(),
            $this->config->getApiKey(),
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
