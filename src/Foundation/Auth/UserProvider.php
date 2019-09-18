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
     * @var \Core\Foundation\Auth\Contracts\UserRepositoryContract
     */
    protected $userRepository;

    /**
     * Create a new file user provider.
     *
     * @param  array $config
     * @param  \Core\Foundation\Auth\Contracts\UserRepositoryContract  $users
     * @return void
     */
    public function __construct($config, UserRepositoryContract $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->config = $config;
    }


    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        return $this->userRepository->retrieveById($identifier);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        return $this->userRepository->retrieveByCredentials($credentials);
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(UserContract $user, array $credentials)
    {
        return $this->userRepository->validateCredentials($user, $credentials);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        return $this->userRepository->retrieveByToken($identifier, $token);
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(UserContract $user, $token)
    {
        $this->userRepository->updateRememberToken($user, $token);
    }

}