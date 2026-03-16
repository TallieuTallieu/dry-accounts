<?php

namespace Tnt\Account\Controller;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use dry\Debug;
use Oak\Contracts\Config\RepositoryInterface;
use Tnt\Account\Contracts\User\UserInterface;
use Tnt\Account\Contracts\AuthenticationInterface;
use Tnt\Account\Contracts\UserRepositoryInterface;
use Tnt\ExternalApi\Exception\ApiException;
use Tnt\ExternalApi\Http\Request;

class AuthController
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    /**
     * @var RepositoryInterface
     */
    private $config;

    /**
     * @var string JWT secret key
     */
    private string $secret;

    /**
     * AuthController constructor.
     * @param UserRepositoryInterface $userRepository
     * @param AuthenticationInterface $authentication
     * @param RepositoryInterface $config
     */
    public function __construct(UserRepositoryInterface $userRepository, AuthenticationInterface $authentication, RepositoryInterface $config)
    {
        $this->userRepository = $userRepository;
        $this->authentication = $authentication;
        $this->config = $config;

        $this->secret = $config->get('accounts.jwt_secret') ?? '';

        if (strlen($this->secret) < 32) {
            Debug::log('JWT secret is missing or too short (minimum 32 bytes required for HS256)', []);
        }
    }

    /**
     * @param Request $request
     * @return array{access_token: string, refresh_token: string, expires_at: int}
     * @throws ApiException
     */
    public function authenticate(Request $request): array
    {
        if (! $request->getHeader('USER') || ! $request->getHeader('PASSWORD')) {
            throw new ApiException('auth_failed');
        }

        if (! $this->authentication->authenticate($request->getHeader('USER'), $request->getHeader('PASSWORD'))) {
            throw new ApiException('auth_failed');
        }

        $user = $this->authentication->getUser();

        return $this->createToken($user);
    }

    /**
     * @param Request $request
     * @return UserInterface
     * @throws ApiException
     */
    public function authorize(Request $request): UserInterface
    {
        if (! $request->getHeader('AUTHORIZATION')) {
            throw new ApiException('authorize_failed');
        }

        try {
            $decodedJwt = JWT::decode(
                $request->getHeader('AUTHORIZATION'),
                new Key($this->secret, 'HS256')
            );

            $user = $this->userRepository->withIdentifier($decodedJwt->sub);

            if (! $user) {
                throw new ApiException('invalid_user');
            }

            return $user;
        }
        catch (ExpiredException $e) {
            throw new ApiException('expired_jwt', $e->getMessage());
        }
        catch (\Exception $e) {
            throw new ApiException('invalid_jwt', $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return array{access_token: string, refresh_token: string, expires_at: int}
     * @throws ApiException
     */
    public function refreshToken(Request $request): array
    {
        $user = $this->userRepository->withValidRefreshToken($request->data->json('refresh_token'));

        if (! $user) {
            throw new ApiException('invalid_user');
        }

        return $this->createToken($user);
    }

    /**
     * @param UserInterface $user
     * @return array{access_token: string, refresh_token: string, expires_at: int}
     * @throws ApiException
     */
    private function createToken(UserInterface $user): array
    {
        try {
            $now = time();
            $expiryTime = (int) ($this->config->get('accounts.token_expiry_time') ?? 3600);
            $refreshExpiryTime = (int) ($this->config->get('accounts.refresh_token_expiry_time') ?? 7200);

            $payload = [
                'exp' => $now + $expiryTime,
                'iat' => $now,
                'sub' => $user->getIdentifier(),
            ];

            $user->setRefreshToken(bin2hex(random_bytes(16)));
            $user->setRefreshTokenExpiryTime($now + $refreshExpiryTime);
            $user->save();

            return [
                'access_token' => JWT::encode($payload, $this->secret, 'HS256'),
                'refresh_token' => $user->getRefreshToken(),
                'expires_at' => $user->getRefreshTokenExpiryTime(),
            ];
        }
        catch (\Exception $e) {
            throw new ApiException('invalid_jwt', $e->getMessage());
        }
    }
}