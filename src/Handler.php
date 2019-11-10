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
            throw new RuntimeException('configuration invalid');
        }

        $this->payload = new Payload($payload);

        if (!$this->payload->isValid()) {
            throw new RuntimeException('payload invalid');
        }

        if (
            $this->config->getUsername() !== $this->payload->getUser() ||
            $this->config->getPassword() !== $this->payload->getPassword()
        ) {
            throw new RuntimeException('credentials wrong');
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
            $this->payload->getHostname(),
            $this->config->getCustomerId(),
            $this->config->getApiKey(),
            $loginHandle->responsedata->apisessionid,
            $clientRequestId
        );


        $changes = false;

        foreach ($infoHandle->responsedata->dnsrecords as $key => $record) {
            $recordHostnameReal = (!in_array($record->hostname, $this->payload->getMatcher())) ? $record->hostname . '.' . $this->payload->getHostname() : $this->payload->getHostname();

            if ($recordHostnameReal === $this->payload->getDomain()) {

                // update A Record if exists and IP has changed
                if ('A' === $record->type && $this->payload->getIpv4() &&
                    (
                        $this->payload->isForce() ||
                        $record->destination !== $this->payload->getIpv4()
                    )
                ) {
                    $record->destination = $this->payload->getIpv4();
                    $this->doLog(sprintf('IPv4 for %s set to %s', $record->hostname . '.' . $this->payload->getHostname(), $this->payload->getIpv4()));
                    $changes = true;
                }

                // update AAAA Record if exists and IP has changed
                if ('AAAA' === $record->type && $this->payload->getIpv6() &&
                    (
                        $this->payload->isForce()
                        || $record->destination !== $this->payload->getIpv6()
                    )
                ) {
                    $record->destination = $this->payload->getIpv6();
                    $this->doLog(sprintf('IPv6 for %s set to %s', $record->hostname . '.' . $this->payload->getHostname(), $this->payload->getIpv6()));
                    $changes = true;
                }
            }
        }

        if (true === $changes) {
            $recordSet = new Soap\Dnsrecordset();
            $recordSet->dnsrecords = $infoHandle->responsedata->dnsrecords;

            $dnsClient->updateDnsRecords(
                $this->payload->getHostname(),
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

        return $this;
    }
}