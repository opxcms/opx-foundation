<?php

namespace Core\Foundation\Auth\Contracts;

interface TokenRepositoryContract
{
    /**
     * Find token for user.
     *
     * @param  UserContract  $user
     * @return Token
     */
    public function getToken(UserContract $user);

    /**
     * Store the given token instance.
     *
     * @param  UserContract  $user
     * @param  Token  $value
     * @return void
     */
    public function setToken(UserContract $user, $value);
}