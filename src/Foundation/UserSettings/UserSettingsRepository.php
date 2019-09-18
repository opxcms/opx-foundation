<?php

namespace Core\Foundation\UserSettings;


class UserSettingsRepository implements UserSettingsRepositoryInterface
{
    /** @var  mixed */
    protected $repository;

    /**
     * UserSettingsFileRepository constructor.
     *
     * @return  void
     */
    public function __construct()
    {
        $this->repository = app()->make('user.settings.repository');
    }

    /**
     * Get settings for given user and guard.
     *
     * @param  mixed $userId
     * @param  mixed $key
     *
     * @return  array
     */
    public function getSettings($userId, $key = null)
    {
        return [];
    }

    /**
     * Set settings for given user and guard.
     *
     * @param  mixed $userId
     * @param  array $settings
     * @param  mixed $key
     *
     * @return  void
     */
    public function setSettings($userId, $settings, $key = null)
    {

    }


}