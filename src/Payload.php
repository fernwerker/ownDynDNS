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
     * @var string
     */
    private $ipv6prefix;

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
            (!empty($this->ipv6prefix) ? $this->isValidIpv6Prefix() : true) &&
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
     * @return string
     */
    public function getIpv6Prefix()
    {
        return $this->ipv6prefix;
    }

    /**
     * @return bool
     */
    public function isValidIpv6Prefix()
    {
        $normalized = self::normalizePrefix($this->ipv6prefix);

        return (bool)filter_var($normalized, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    /**
     * Returns the full IPv6 address, combining prefix + suffix if ipv6prefix is set.
     *
     * @return string
     */
    public function getResolvedIpv6()
    {
        if (!empty($this->ipv6prefix)) {
            return self::combineIpv6($this->ipv6prefix, $this->ipv6);
        }

        return $this->ipv6;
    }

    /**
     * @return string
     */
    private static function normalizePrefix(string $prefix): string
    {
        // Strip CIDR notation (e.g. /64) sent by FRITZ!Box
        $normalized = preg_replace('/\/\d+$/', '', $prefix);
        $normalized = rtrim($normalized, ':');
        if (strpos($normalized, '::') === false) {
            $normalized .= '::';
        }

        return $normalized;
    }

    private static function combineIpv6(string $prefix, string $suffix): string
    {
        $prefixBin = inet_pton(self::normalizePrefix($prefix));
        $suffixBin = inet_pton($suffix);

        if ($prefixBin === false || $suffixBin === false) {
            throw new \RuntimeException('Invalid IPv6 prefix or suffix');
        }

        return inet_ntop(substr($prefixBin, 0, 8) . substr($suffixBin, 8, 8));
    }

    /**
     * @return bool
     */
    public function isForce()
    {
        return $this->force;
    }
}