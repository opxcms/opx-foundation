<?php

namespace Core\Foundation\Auth;


use Core\Foundation\Application;
use Core\Foundation\Auth\Contracts\TokenRepositoryContract;
use Core\Foundation\Auth\Contracts\UserRepositoryContract;
use Core\Foundation\Auth\Contracts\UserContract;
use Core\Foundation\Auth\GenericUser as User;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Cache\Factory as CacheContract;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class UserFileRepository implements UserRepositoryContract
{
    /**
     * Application instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $users = [];
    protected $remember_tokens;
    protected $identifier = 'email';

    /**
     * The cache used for storing connection tokens
     *
     * @var CacheContract
     */
    protected $cache;

    /**
     * The hash driver to check password
     *
     * @var HasherContract
     */
    protected $hasher;

    public function __construct(Application $app, $repositoryFile, TokenRepositoryContract $remember_tokens, HasherContract $hasher, CacheContract $cache)
    {
        $this->app = $app;
        $this->users = (new Collection(require $app->getUsersPath($repositoryFile)))->keyBy($this->identifier);
        $this->remember_tokens = $remember_tokens;
        $this->hasher = $hasher;
        $this->cache = $cache;
    }

    public function retrieveById($identifier): ?GenericUser
    {
        if ($identifier && $this->users->has($identifier)) {
            $user = $this->createUser($this->users[$identifier]);
        }

        return $user ?? null;
    }

    public function retrieveByCredentials(array $credentials): ?GenericUser
    {
        unset($credentials[(new User([]))->getPasswordName()]);

        $users = $this->users;

        foreach ($credentials as $key => $value) {
            $users = $users->filter(function ($user) use ($key, $value) {
                return $user[$key] === $value;
            });
        }

        return $this->createUser($users->first());
    }

    public function validateCredentials(UserContract $user, array $credentials, $hasher = null): bool
    {
        $plain = $credentials['password'];

        return ($plain === $user->getAuthPassword()) || ($this->hasher->check($plain, $user->getAuthPassword()));
    }

    public function retrieveByToken($identifier, $token): ?GenericUser
    {
        $user = $this->retrieveById($identifier);

        if ($user === null) {
            return null;
        }

        $rememberToken = $this->remember_tokens->getToken($user);

        return $rememberToken && hash_equals($rememberToken, $token) ? $user : null;
    }

    public function getRememberToken(UserContract $user)
    {
        return $this->remember_tokens->getToken($user);
    }


    public function updateRememberToken(UserContract $user, $token): void
    {
        $this->remember_tokens->setToken($user, $token);
    }


    public function getApiToken(UserContract $user)
    {
        $this->api_tokens->getToken($user);
    }

    public function updateApiToken(UserContract $user, $token): void
    {

    }

    public function createUser($credentials = []): ?GenericUser
    {
        if (!$credentials) {
            return null;
        }

        $user = new User($credentials, $this);

        $user->{$user->getAuthIdentifierName()} = $credentials[$this->identifier] ?? null;

        return $user->getAuthIdentifier() ? $user : null;
    }
}
