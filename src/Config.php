<?php

namespace netcup\DNS\API;

final class Config
{

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $apiPassword;

    /**
     * @var int
     */
    private $customerId;

    /**
     * @var bool
     */
    private $log = true;

    /**
     * @var string
     */
    private $logFile;

    /**
     * @var bool
     */
    private $debug = false;

    /**
     * @var bool
     */
    private $returnIp = true;

    /**
     * @var bool
     */
    private $allowCreate = false;

    /**
     * @var bool
     */
    private $allowNetcupCreds = false;

    /**
     * @var bool
     */
    private $allowAnonymous = false;

    /**
     * @var bool
     */
    private $restrictDomain = false;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var string
     */
    private $host;


    public function __construct(array $config)
    {
        foreach (get_object_vars($this) as $key => $val) {
            if (isset($config[$key])) {
                $this->$key = $config[$key];
            }
        }
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return
            (
                !empty($this->username) &&
                !empty($this->password)
            ) ||
            (
                $this->isAllowAnonymous()
            ) &&
            (
                (
                    !empty($this->apiKey) &&
                    !empty($this->apiPassword) &&
                    !empty($this->customerId)
                ) ||
                (
                    $this->isAllowNetcupCreds()
                )
            ) &&
            !empty($this->logFile);
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return string
     */
    public function getApiPassword()
    {
        return $this->apiPassword;
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return bool
     */
    public function isLog()
    {
        return $this->log;
    }

    /**
     * @return string
     */
    public function getLogFile()
    {
        return $this->logFile;
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * @return bool
     */
    public function isReturnIp()
    {
        return $this->returnIp;
    }

    /**
     * @return bool
     */
    public function isAllowCreate()
    {
        return $this->allowCreate;
    }

    /**
     * @return bool
     */
    public function isRestrictDomain()
    {
        return $this->restrictDomain;
    }

    /**
     * @return bool
     */
    public function isAllowNetcupCreds()
    {
        return $this->allowNetcupCreds;
    }

    /**
     * @return bool
     */
    public function isAllowAnonymous()
    {
        return $this->allowAnonymous;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        if (empty($this->host))
        {
            return $this->domain;
        }
        else
        {
            return $this->host . "." . $this->domain;
        }
    }

    /**
     * @return string
     */
    public function getHost()
    {
        if (!empty($this->host))
        {
            return $this->host;
        }
        else
        {
            $domainParts = explode('.', $this->domain);
            return $domainParts[0];
        }
    }

    /**
     * @return string
     */
    public function getDomainName()
    {
        // hack if top level domain are used for dynDNS
        if (1 === substr_count($this->domain, '.')) {
            return $this->domain;
        }

        $domainParts = explode('.', $this->domain);
        array_shift($domainParts); // remove sub domain
        return implode('.', $domainParts);
    }
}
