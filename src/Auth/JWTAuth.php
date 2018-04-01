<?php

namespace Nilnice\Phalcon\Auth;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use Nilnice\Phalcon\Exception\InvalidAccountTypeException;
use Nilnice\Phalcon\Exception\InvalidTokenException;
use Phalcon\Mvc\Model;

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
        $this->accountType = $accountType;

        /** @var \Phalcon\Mvc\Model $model */
        $model = $this->getIdentity($accountType, $array);
        $issueAt = time();
        $expireAt = $this->duration + $issueAt;
        $array = [
            'iss' => config('app.token.iss'),
            'aud' => config('app.token.aud'),
            'jti' => $model->getAppId(),
            'key' => $model->getAppSecret(),
            'isa' => time(),
            'exp' => $expireAt,
            'uid' => $model->getId(),
        ];

        $token = $this->createToken($array);
        $this->token = $token;
        $result = [
            'token' => $token->__toString(),
            'uid'   => $model->getId(),
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

        $uid = $this->token->getClaim('uid');
        $auth = config('auth.user.class');
        $auth = $auth::findFirst([
            'conditions' => 'id=:id:',
            'bind'       => ['id' => $uid],
        ]);

        if (! $auth) {
            throw new InvalidTokenException('The user does not exist', 404);
        }
        $array = $auth->toArray();
        ['appId' => $appId, 'appSecret' => $appSecret] = $array;

        if (! $token->verify($signer, $array['appSecret'])) {
            throw new InvalidTokenException('The token signature error', 400);
        }

        $data = new ValidationData();
        $data->setIssuer(config('app.token.iss'));
        $data->setAudience(config('app.token.aud'));
        $data->setId($appId);

        if ($token->isExpired()) {
            throw new InvalidTokenException('The token is expired', 400);
        }

        if (! $token->validate($data)) {
            throw new InvalidTokenException('The token is valid', 400);
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
            'iss' => $issuer,
            'aud' => $audience,
            'jti' => $appId,
            'isa' => $issueAt,
            'exp' => $expireAt,
            'uid' => $uid,
            'key' => $appSecret,
        ]
            = $array;
        $signer = new Sha256();
        $token = (new Builder())->setIssuer($issuer)
            ->setAudience($audience)
            ->setId($appId, true)
            ->setIssuedAt($issueAt)
            ->setNotBefore($issueAt)
            ->setExpiration($expireAt)
            ->set('uid', $uid)
            ->sign($signer, $appSecret)
            ->getToken();

        return $token;
    }

    /**
     * Get identify.
     *
     * @param \Nilnice\Phalcon\Auth\AccountTypeInterface $accountType
     * @param array                                      $array
     *
     * @return \Phalcon\Mvc\Model
     */
    private function getIdentity(
        AccountTypeInterface $accountType,
        array $array
    ): Model {
        return $accountType->login($array);
    }
}
