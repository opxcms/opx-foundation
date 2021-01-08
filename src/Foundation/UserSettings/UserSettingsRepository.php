<?php

namespace Core\Foundation\UserSettings;


use Illuminate\Contracts\Container\BindingResolutionException;

class UserSettingsRepository implements UserSettingsRepositoryInterface
{
    /** @var  mixed */
    protected $repository;

    /**
     * UserSettingsFileRepository constructor.
     *
     * @return  void
     * @throws BindingResolutionException
     */
    public function __construct()
    {
        $this->repository = app()->make('user.settings.repository');
    }

    /**
     * Get settings for given user and guard.
     *
     * @param mixed $userId
     * @param mixed $key
     *
     * @return  array
     */
    public function getSettings($userId, $key = null): array
    {
        return [];
    }

    /**
     * Set settings for given user and guard.
     *
     * @param mixed $userId
     * @param array $settings
     * @param mixed $key
     *
     * @return  void
     */
    public function setSettings($userId, array $settings, $key = null): void
    {

    }


}