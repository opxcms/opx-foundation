<?php

namespace Core\Http\Controllers\Api;

use Core\Facades\Site;
use Core\Foundation\Application;
use Core\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ManageLocalizationApiController extends Controller
{
    private $system = [
        'actions',
        'filters',
        'forms',
        'messages',
        'navigation',
        'validation',
        'orders',
        'filters',
        'search',
        'sections',
        'groups',
        'fields',
    ];

    /**
     * Get initial values for localization.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function postTranslations(Request $request): JsonResponse
    {
        $currentLocale = $request->input('lang');
        $currentLocale = empty($currentLocale) ? Site::getLocale() : $currentLocale;
        $installedLocales = Site::installedLocales();

        $translations = array_merge($this->system, $this->getModulesTranslations());

        return response()->json([
            'current_locale' => $currentLocale,
            'installed_locales' => $installedLocales,
            'localizations' => $this->parseTranslations($translations),
        ]);
    }

    /**
     * Get list of all modules files with translations.
     *
     * @return  array
     */
    protected function getModulesTranslations(): array
    {
        /** @var  Application $app */
        $app = app();
        $translations = [];

        foreach (array_keys($app->getModulesList()) as $name) {
            $module = $app->getModule($name);

            if ($module) {
                $fileName = $module->path('Manage' . DIRECTORY_SEPARATOR . 'translations.php');
                if (file_exists($fileName)) {
                    $translations[$name] = require $fileName;
                }
            }
        }

        return $translations;
    }

    /**
     * Get translations for given subjects.
     *
     * @param array $translations
     *
     * @return  array
     */
    protected function parseTranslations(array $translations): array
    {
        $parsed = [];

        foreach ($translations as $module => $translation) {
            if (is_string($translation)) {
                $parsed[] = $this->flattenTranslations($translation, trans($translation));
            } else if (is_array($translation)) {
                foreach ($translation as $moduleTranslation) {
                    $parsed[] = $this->flattenTranslations("{$module}::{$moduleTranslation}", trans("{$module}::{$moduleTranslation}"));
                }
            }
        }

        $parsed = array_merge(...$parsed);

        return $parsed;
    }

    /**
     * Flatten translations array.
     *
     * @param string $key
     * @param array $translations
     *
     * @return  array
     */
    protected function flattenTranslations(string $key, array $translations): array
    {
        if(empty($translations)) {
            return [];
        }

        $flattened = [];

        foreach ($translations as $local => $value) {
            $flattened["{$key}.{$local}"] = $value;
        }

        return $flattened;
    }

    /**
     * Set new locale and return translations for given keys.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function postTranslate(Request $request): JsonResponse
    {
        $toLocale = $request->input('to_locale');
        $toTranslate = $request->input('to_translate');

        Site::setLocale($toLocale);
        $translated = [];

        if (is_array($toTranslate)) {
            foreach ($toTranslate as $key) {
                $translated[$key] = trans($key);
            }
        } elseif (is_string($toTranslate)) {
            $translated[$toTranslate] = trans($toTranslate);
        }

        return response()->json(['locale' => Site::getLocale(), 'localizations' => $translated]);
    }
}
