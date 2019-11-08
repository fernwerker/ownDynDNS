<?php

namespace netcup\DNS\API;

final class Payload
{
    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var string
     */
    private $mode;

    /**
     * @var string
     */
    private $ipv4;

    /**
     * @var string
     */
    private $ipv6;

    /**
     * @var bool
     */
    private $force = false;

    public function __construct(array $payload)
    {
        foreach (get_object_vars($this) as $key => $val) {
            if (isset($payload[$key])) {
                $this->$key = $payload[$key];
            }
        }
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return
            !empty($this->user) &&
            !empty($this->password) &&
            !empty($this->domain) &&
            (
                (
                    !empty($this->ipv4) && $this->isValidIpv4()
                )
                ||
                (
                    !empty($this->ipv6) && $this->isValidIpv6()
                )
            );
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
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
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return array
     */
    public function getMatcher()
    {
        switch ($this->mode) {
            case 'both':
                return ['@', '*'];

            case '*':
                return ['*'];

            default:
                return ['@'];
        }
    }

    /**
     * there is no good way to get the correct "registrable" Domain without external libs!
     *
     * @see https://github.com/jeremykendall/php-domain-parser
     *
     * this method is still tricky, because:
     *
     * works: nas.tld.com
     * works: nas.tld.de
     * works: tld.com
     * failed: nas.tld.co.uk
     * failed: nas.home.tld.de
     *
     * @return string
     */
    public function getHostname()
    {
        // hack if top level domain are used for dynDNS
        if (1 === substr_count($this->domain, '.')) {
            return $this->domain;
        }

        $domainParts = explode('.', $this->domain);
        array_shift($domainParts); // remove sub domain
        return implode('.', $domainParts);
    }

    /**
     * @return string
     */
    public function getIpv4()
    {
        return $this->ipv4;
    }

    /**
     * @return bool
     */
    public function isValidIpv4()
    {
        return (bool)filter_var($this->ipv4, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }

    /**
     * @return string
     */
    public function getIpv6()
    {
        return $this->ipv6;
    }

    /**
     * @return bool
     */
    public function isValidIpv6()
    {
        return (bool)filter_var($this->ipv6, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    /**
     * @return bool
     */
    public function isForce()
    {
        return $this->force;
    }
}