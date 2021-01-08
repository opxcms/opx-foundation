<?php

namespace Core\Foundation\Auth;

use Illuminate\Contracts\Auth\UserProvider as BaseUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

use Core\Foundation\Auth\Contracts\UserRepositoryContract;

class UserProvider implements BaseUserProvider
{
    /**
     * config.
     *
     * @var array
     */
    protected $config;

    /**
     * Users repository.
     *
     * @var UserRepositoryContract
     */
    protected $userRepository;

    /**
     * Create a new file user provider.
     *
     * @param array $config
     * @param UserRepositoryContract $userRepository
     *
     */
    public function __construct(array $config, UserRepositoryContract $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->config = $config;
    }


    /**
     * Retrieve a user by their unique identifier.
     *
     * @param mixed $identifier
     *
     * @return UserContract|null
     */
    public function retrieveById($identifier): ?UserContract
    {
        return $this->userRepository->retrieveById($identifier);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param array $credentials
     *
     * @return UserContract|null
     */
    public function retrieveByCredentials(array $credentials): ?UserContract
    {
        return $this->userRepository->retrieveByCredentials($credentials);
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param UserContract $user
     * @param array $credentials
     *
     * @return bool
     */
    public function validateCredentials(UserContract $user, array $credentials): bool
    {
        return $this->userRepository->validateCredentials($user, $credentials);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param mixed $identifier
     * @param string $token
     *
     * @return UserContract|null
     */
    public function retrieveByToken($identifier, $token): ?UserContract
    {
        return $this->userRepository->retrieveByToken($identifier, $token);
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param UserContract $user
     * @param string $token
     *
     * @return void
     */
    public function updateRememberToken(UserContract $user, $token): void
    {
        $this->userRepository->updateRememberToken($user, $token);
    }

}