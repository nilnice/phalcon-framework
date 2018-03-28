<?php

namespace Nilnice\Phalcon\Auth;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use Nilnice\Phalcon\Exception\InvalidAccountTypeException;
use Nilnice\Phalcon\Exception\InvalidTokenException;

class JWTAuth
{
    public const LOGIN_PHONE = 'phone';
    public const LOGIN_EMAIL = 'email';
    public const LOGIN_USERNAME = 'username';
    public const LOGIN_PASSWORD = 'password';

    /**
     * @var int
     */
    protected $duration;

    /**
     * @var \Lcobucci\JWT\Token
     */
    protected $token;

    /**
     * @var string
     */
    protected $accountType;

    /**
     * @var array
     */
    protected $accountTypes;

    /**
     * JWTAuth constructor.
     *
     * @param int $duration
     */
    public function __construct(int $duration = 86400)
    {
        $this->duration = $duration;
        $this->accountTypes = [];
    }

    /**
     * Login with user username and password.
     *
     * @param string $type
     * @param string $username
     * @param string $password
     *
     * @return array
     *
     * @throws \Nilnice\Phalcon\Exception\InvalidAccountTypeException
     */
    public function loginWithUsernamePassword(
        string $type,
        string $username,
        string $password
    ): array {
        $array = [
            self::LOGIN_USERNAME => $username,
            self::LOGIN_PASSWORD => $password,
        ];

        return $this->login($type, $array);
    }

    /**
     * User login.
     *
     * @param string $type
     * @param array  $array
     *
     * @return array
     *
     * @throws \Nilnice\Phalcon\Exception\InvalidAccountTypeException
     */
    public function login(string $type, array $array): array
    {
        $accountType = $this->getAccountType($type);

        if (! $accountType) {
            throw new InvalidAccountTypeException('Invalid account type');
        }

        if (! $accountType instanceof AccountTypeInterface) {
            throw new InvalidAccountTypeException('The account type must be an instance of AccountTypeInterface');
        }

        $identity = $this->getIdentity($accountType, $array);
        $issueAt = time();
        $expireAt = $this->duration + $issueAt;
        $array = [
            'isa' => time(),
            'exp' => $expireAt,
            'uid' => $identity,
        ];

        $token = $this->createToken($array);
        $this->token = $token;
        $result = [
            'token' => $token->__toString(),
            'uid'   => $identity,
        ];

        return $result;
    }

    /**
     * @return \Lcobucci\JWT\Token|null
     */
    public function getToken(): ?Token
    {
        return $this->token;
    }

    /**
     * Authentication token.
     *
     * @param string $token
     *
     * @return bool
     *
     * @throws \Nilnice\Phalcon\Exception\InvalidTokenException
     */
    public function authenticateToken(string $token): bool
    {
        $signer = new Sha256();
        $token = (new Parser())->parse($token);
        $this->token = $token;
        $key = config('app.key');

        if (! $token->verify($signer, $key)) {
            throw new InvalidTokenException('The token signature error', 400);
        }

        $data = new ValidationData();
        $data->setIssuer('http://example.com');
        $data->setAudience('http://example.org');
        $data->setId('4f1g23a12aa');

        if (! $token->validate($data)) {
            throw new InvalidTokenException('The token has expired', 400);
        }

        return true;
    }

    /**
     * Register account type.
     *
     * @param string                                     $name
     * @param \Nilnice\Phalcon\Auth\AccountTypeInterface $accountType
     *
     * @return \Nilnice\Phalcon\Auth\JWTAuth
     */
    public function registerAccountType(
        string $name,
        AccountTypeInterface $accountType
    ): self {
        $this->accountTypes[$name] = $accountType;

        return $this;
    }

    /**
     * Get account type.
     *
     * @param string $type
     *
     * @return mixed|null
     */
    public function getAccountType(string $type)
    {
        if (array_key_exists($type, $this->accountTypes)) {
            return $this->accountTypes[$type];
        }

        return null;
    }

    /**
     * Get all account types.
     *
     * @return array
     */
    public function getAccountTypes(): array
    {
        return $this->accountTypes;
    }

    /**
     * Create user token.
     *
     * @param array $array
     *
     * @return \Lcobucci\JWT\Token
     */
    private function createToken(array $array): Token
    {
        [
            'isa' => $issueAt,
            'exp' => $expireAt,
            'uid' => $uid,
        ]
            = $array;
        $signer = new Sha256();
        $key = config('app.key');
        $token = (new Builder())->setIssuer('http://example.com')
            ->setAudience('http://example.org')
            ->setId('4f1g23a12aa', true)
            ->setIssuedAt($issueAt)
            ->setNotBefore($issueAt)
            ->setExpiration($expireAt)
            ->set('uid', $uid)
            ->sign($signer, $key)
            ->getToken();

        return $token;
    }

    /**
     * Get identify.
     *
     * @param \Nilnice\Phalcon\Auth\AccountTypeInterface $accountType
     * @param array                                      $array
     *
     * @return string
     */
    private function getIdentity(
        AccountTypeInterface $accountType,
        array $array
    ): string {
        return $accountType->login($array);
    }
}
