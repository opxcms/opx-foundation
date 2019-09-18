<?php

namespace Core\Foundation\Auth;

use Core\Foundation\Application;
use Core\Foundation\Auth\Contracts\TokenRepositoryContract;
use Core\Foundation\Auth\Contracts\UserContract;

class RememberTokenFileRepository implements TokenRepositoryContract
{
    protected $app;
    protected $tokens = [];
    protected $fileName;

    public function __construct(Application $app, $repository)
    {
        $this->app = $app;
        $this->fileName = $app->getUsersPath('remember_for_' . $repository);
        $this->tokens = file_exists($this->fileName) ? unserialize(file_get_contents($this->fileName), [false]) : [];
    }

    public function getToken(UserContract $user)
    {
        return $this->tokens[$user->getAuthIdentifier()] ?? null;
    }

    public function setToken(UserContract $user, $value): void
    {
        $this->tokens[$user->getAuthIdentifier()] = $value;
        file_put_contents($this->fileName, serialize($this->tokens));
    }
}