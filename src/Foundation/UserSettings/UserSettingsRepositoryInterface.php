<?php

namespace Core\Foundation\UserSettings;


interface UserSettingsRepositoryInterface
{
    /**
     * Get settings for given user and guard.
     *
     * @param mixed $userId
     * @param mixed $key
     *
     * @return  array
     */
    public function getSettings($userId, $key = null): array;

    /**
     * Set settings for given user and guard.
     *
     * @param mixed $userId
     * @param array $settings
     * @param mixed $key
     *
     * @return  void
     */
    public function setSettings($userId, array $settings, $key = null): void;
}