<?php

namespace Core\Foundation\UserSettings;

use RuntimeException;
use Illuminate\Support\Arr;

class UserSettingsFileRepository extends UserSettingsRepository
    {
    /**
     * Get settings for given user and guard.
     * Accepts dot notation as key.
     *
     * @param mixed $userId
     * @param mixed $key
     *
     * @return  array
     */
    public function getSettings($userId, $key = null): array
    {
        $repositoryFileName = $this->getRepositoryFileName($userId);

        if (!file_exists($repositoryFileName)) {
            return [];
        }

        $repository = unserialize(file_get_contents($repositoryFileName), ['allowed_classes' => false]);

        if ($repository === false) {
            return [];
        }

        return $key === null
            ? $repository
            : Arr::get($repository, $key);
    }

    /**
     * Set settings for given user and guard.
     * Accepts dot notation as key.
     *
     * @param mixed $userId
     * @param array $settings
     * @param mixed $key
     *
     * @return  void
     */
    public function setSettings($userId, array $settings, $key = null): void
    {
        $repositoryFileName = $this->getRepositoryFileName($userId);
        $repositoryExists = file_exists($repositoryFileName);

        $repository = $repositoryExists
            ? unserialize(file_get_contents($repositoryFileName), ['allowed_classes' => false])
            : [];
        if ($repository === false) {
            $repository = [];
        }

        if ($key === null) {
            $repository = $settings;
        } else {
            Arr::set($repository, $key, $settings);
        }

        if (!$repositoryExists) {
            $repositoryPath = pathinfo($repositoryFileName, PATHINFO_DIRNAME);
            if (!is_dir($repositoryPath) && !mkdir($repositoryPath, 0777, true) && !is_dir($repositoryPath)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $repositoryPath));
            }
        }

        file_put_contents($repositoryFileName, serialize($repository));
    }

    /**
     * Make repository file name.
     *
     * @param mixed $userId
     *
     * @return  string
     */
    protected function getRepositoryFileName($userId): string
    {

        return app()->getUsersPath('settings' . DIRECTORY_SEPARATOR . "{$this->repository}_{$userId}");
    }


}