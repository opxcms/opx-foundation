<?php

namespace Core\Foundation\Bootstrap;

use Exception;
use Illuminate\Support\Arr;
use RuntimeException;
use SplFileInfo;
use Illuminate\Config\Repository;
use Symfony\Component\Finder\Finder;
use Core\Foundation\Application;
use Illuminate\Contracts\Config\Repository as RepositoryContract;

class LoadConfiguration
{
    /** @var  bool  Enable profile resolving. */
    protected $resolveProfile = true;

    /**
     * Bootstrap the given application.
     *
     * @param Application $app
     *
     * @return void
     *
     * @throws Exception
     */
    public function bootstrap(Application $app): void
    {
        $items = [];

        // First we will see if we have a cache configuration file. If we do, we'll load
        // the configuration items from that file so that it is very quick. Otherwise
        // we will need to spin through every configuration file and load them all.
        if (file_exists($cached = $app->getCachedConfigPath())) {
            $items = require $cached;

            $loadedFromCache = true;
        }

        // Next we will spin through all of the configuration files in the configuration
        // directory and load each one into the repository. This will make all of the
        // options available to the developer for use in various parts of this app.
        $app->instance('config', $config = new Repository($items));

        if (!isset($loadedFromCache)) {
            $this->loadConfigurationFiles($app, $config);
        }

        // Resolve profile name and apply profile configuration
        if ($this->resolveProfile) {
            $profile = $this->resolveProfile($config);

            $app->instance('opx.profile', $profile);

            $config = $this->applyProfile($config, $profile);
        } else {
            $app->instance('opx.profile', 'default');
        }

        // Finally, we will set the application's environment based on the configuration
        // values that were loaded. We will pass a callback which will be used to get
        // the environment in a web context where an "--env" switch is not present.
        $app->detectEnvironment(function () use ($config) {
            return $config->get('app.env', 'production');
        });

        date_default_timezone_set($config->get('app.timezone', 'UTC'));

        mb_internal_encoding('UTF-8');
    }

    /**
     * Load the configuration items from all of the files.
     *
     * @param Application $app
     * @param RepositoryContract $repository
     *
     * @return void
     * @throws RuntimeException
     */
    protected function loadConfigurationFiles(Application $app, RepositoryContract $repository): void
    {
        $mainFiles = $this->getConfigurationFiles($app->configPath());

        if (!isset($mainFiles['app'])) {
            throw new RuntimeException('Unable to load the "app" configuration file.');
        }

        $localFiles = $this->getConfigurationFiles($app->localConfigPath());

        $files = array_merge_recursive($mainFiles, $localFiles);

        foreach ($files as $key => $path) {
            if (is_array($path)) {
                $config = [[]];
                foreach ($path as $pathVariant) {
                    $config[] = require $pathVariant;
                }
                $config = array_replace_recursive(...$config);
            } else {
                $config = require $path;
            }

            $repository->set($key, $config);
        }
    }

    /**
     * Get all of the configuration files for the application in given location.
     *
     * @param string $configPath
     *
     * @return array
     */
    protected function getConfigurationFiles(string $configPath): array
    {
        $files = [];

        $configPath = realpath($configPath);

        foreach (Finder::create()->files()->name('*.php')->in($configPath) as $file) {
            /** @var SplFileInfo $file */

            $directory = $this->getNestedDirectory($file, $configPath);

            $realPath = $file->getRealPath();

            $files[$directory . basename($realPath, '.php')] = $realPath;
        }

        ksort($files, SORT_NATURAL);

        return $files;
    }

    /**
     * Get the configuration file nesting path.
     *
     * @param SplFileInfo $file
     * @param string $configPath
     *
     * @return string
     */
    protected function getNestedDirectory(SplFileInfo $file, string $configPath): string
    {
        $directory = $file->getPath();

        if ($nested = trim(str_replace($configPath, '', $directory), DIRECTORY_SEPARATOR)) {
            $nested = str_replace(DIRECTORY_SEPARATOR, '.', $nested) . '.';
        }

        return $nested;
    }

    /**
     * Resolve profile name for current request.
     *
     * @param RepositoryContract $repository
     *
     * @return  string
     */
    protected function resolveProfile(RepositoryContract $repository): string
    {
        $host = request()->server('SERVER_NAME');

        if (null !== $profiles = $repository->get('profile')) {
            foreach ($profiles as $profileKey => $profileOptions) {
                if ($profileOptions['domain']) {
                    if (is_string($profileOptions['domain'])) {
                        $profileOptions['domain'] = [$profileOptions['domain']];
                    }
                    if (in_array($host, $profileOptions['domain'], true)) {
                        return $profileKey;
                    }
                }
            }
        }

        return 'default';
    }

    /**
     * Apply selected profile to config.
     * Note: 'default' profile don't apply anything.
     *
     * @param RepositoryContract $repository
     * @param string $profile
     *
     * @return  RepositoryContract
     */
    protected function applyProfile(RepositoryContract $repository, string $profile): RepositoryContract
    {
        if ('default' === $profile) {
            return $repository;
        }

        // Process profile applying
        if ($profileConfigs = $repository->get("profiles.{$profile}")) {
            $profileConfigs = Arr::dot($profileConfigs);
            $repository->set($profileConfigs);
        }

        return $repository;
    }
}
