<?php

namespace Core\Foundation\JWT\Middleware;

use Core\Foundation\JWT\JWT;
use Illuminate\Contracts\Encryption\Encrypter as EncrypterContract;
use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array
     */
    protected $except = [];

    public function __construct(EncrypterContract $encrypter)
    {
        parent::__construct($encrypter);
        $this->except = (array) JWT::cookie();
    }
}
