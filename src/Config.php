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
    private $debug;

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
            !empty($this->username) &&
            !empty($this->password) &&
            !empty($this->apiKey) &&
            !empty($this->apiPassword) &&
            !empty($this->customerId) &&
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
}