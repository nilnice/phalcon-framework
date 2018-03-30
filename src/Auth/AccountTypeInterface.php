<?php

namespace Nilnice\Phalcon\Auth;

use Phalcon\Mvc\Model;

interface AccountTypeInterface
{
    /**
     * User login.
     *
     * @param array $data
     *
     * @return null|string
     */
    public function login(array $data): Model;

    /**
     * User authentication.
     *
     * @param string $identity
     *
     * @return bool
     */
    public function authenticate(string $identity): bool;
}
