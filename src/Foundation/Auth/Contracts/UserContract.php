<?php

namespace Core\Foundation\Auth\Contracts;

interface UserContract
{
    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName();

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier();

    /**
     * Get the name of the password for the user.
     *
     * @return string
     */
    public function getPasswordName();

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword();

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName();

    /**
     * Get the "remember me" token value.
     *
     * @return string
     */
    public function getRememberToken();

    /**
     * Set the "remember me" token value.
     *
     * @param  string $value
     *
     * @return void
     */
    public function setRememberToken($value);

    /**
     * Check if user has permission.
     *
     * @param  string $permission
     *
     * @return bool
     */
    public function can($permission);

}