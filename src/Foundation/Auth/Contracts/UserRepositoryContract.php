<?php

namespace Core\Foundation\Auth\Contracts;

interface UserRepositoryContract
{
    public function retrieveById($identifier);
    public function retrieveByCredentials(array $credentials);
    public function retrieveByToken($identifier, $token);

    public function validateCredentials(UserContract $user, array $credentials);

    public function getRememberToken(UserContract $user);
    public function updateRememberToken(UserContract $user, $token);

    public function getApiToken(UserContract $user);
    public function updateApiToken(UserContract $user, $token);
}