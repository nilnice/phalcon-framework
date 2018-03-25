<?php

namespace Nilnice\Phalcon\Http;

class Request extends \Phalcon\Http\Request
{
    public const GET = 'GET';
    public const POST = 'POST';
    public const PUT = 'PUT';
    public const DELETE = 'DELETE';
    public const HEAD = 'HEAD';
    public const OPTIONS = 'OPTIONS';
    public const PATCH = 'PATCH';
    public const ALL_METHODS
        = [
            self::GET,
            self::POST,
            self::PUT,
            self::DELETE,
            self::HEAD,
            self::OPTIONS,
            self::PATCH,
        ];

    /**
     * Get authentication username.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->getServer('PHP_AUTH_USER');
    }

    /**
     * Get authentication password.
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->getServer('PHP_AUTH_PW');
    }

    /**
     * Get authentication token.
     *
     * @return null|string
     */
    public function getToken(): ? string
    {
        $headerToken = $this->getHeader('AUTHORIZATION');
        $queryToken = $this->getQuery('token');
        $token = $queryToken ?: $this->parseBearerValue($headerToken);

        return $token;
    }

    /**
     * Parse `Bearer` value.
     *
     * @param string $string
     *
     * @return null|string|string[]
     */
    protected function parseBearerValue($string)
    {
        if (0 !== strpos(trim($string), 'Bearer')) {
            return null;
        }

        return preg_replace('/.*\s/', '', $string);
    }
}
