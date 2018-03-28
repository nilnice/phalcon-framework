<?php

namespace Nilnice\Phalcon\Auth;

interface AccountTypeInterface
{
    /**
     * User login.
     *
     * @param array $data
     *
     * @return null|string
     */
    public function login(array $data): ? string;

    /**
     * User authentication.
     *
     * @param string $identity
     *
     * @return bool
     */
    public function authenticate(string $identity): bool;
}
