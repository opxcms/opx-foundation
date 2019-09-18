<?php

namespace Core\Foundation\Auth;

use Core\Foundation\Auth\Contracts\UserContract;
use Core\Foundation\Auth\Contracts\UserRepositoryContract;
use Illuminate\Contracts\Auth\Authenticatable as DefaultUserContract;

class GenericUser implements DefaultUserContract, UserContract
{
    /**
     * All of the user's attributes.
     *
     * @var array
     */
    protected $attributes;

    protected $auth_identifier_name = 'id';
    protected $password_name = 'password';
    protected $remember_token_name = 'remember_token';
    protected $provider;

    /**
     * Create a new generic User object.
     *
     * @param array $attributes
     * @param \Core\Foundation\Auth\Contracts\UserRepositoryContract|null $provider
     *
     * @return void
     */
    public function __construct(array $attributes, UserRepositoryContract $provider = null)
    {
        $this->attributes = $attributes;
        $this->provider = $provider;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->attributes[$this->getAuthIdentifierName()];
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName(): string
    {
        return $this->auth_identifier_name;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword(): string
    {
        return $this->attributes[$this->getPasswordName()];
    }

    /**
     * Get the name of the password for the user.
     *
     * @return string
     */
    public function getPasswordName(): string
    {
        return $this->password_name;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName(): string
    {
        return $this->remember_token_name;
    }

    /**
     * Get the "remember me" token value.
     *
     * @return string
     */
    public function getRememberToken(): ?string
    {
        return $this->provider->getRememberToken($this);
    }

    /**
     * Set the "remember me" token value.
     *
     * @param string $value
     *
     * @return void
     */
    public function setRememberToken($value): void
    {
        $this->provider->updateRememberToken($this, $value);
    }

    /**
     * Check if user has permission.
     *
     * @param string $permission
     *
     * @return bool
     */
    public function can($permission): bool
    {
        return true;
    }


    /**
     * Dynamically access the user's attributes.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->attributes[$key];
    }

    /**
     * Dynamically set an attribute on the user.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Dynamically check if a value is set on the user.
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Dynamically unset a value on the user.
     *
     * @param string $key
     *
     * @return void
     */
    public function __unset($key)
    {
        unset($this->attributes[$key]);
    }
}
