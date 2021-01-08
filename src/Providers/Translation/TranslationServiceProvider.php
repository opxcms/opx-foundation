<?php

namespace Core\Providers\Translation;

use Illuminate\Translation\TranslationServiceProvider as BaseTranslationServiceProvider;
use Illuminate\Translation\Translator;

class TranslationServiceProvider extends BaseTranslationServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerLoader();

        $this->app->singleton('translator', function ($app) {
            $loader = $app['translation.loader'];

            // When registering the translator component, we'll need to set the default
            // locale as well as the fallback locale. So, we'll grab the application
            // configuration so we can easily get both of these values from there.
            $locale = $app['config']['lang.locale'];

            $trans = new Translator($loader, $locale);

            $trans->setFallback($app['config']['lang.fallback_locale']);

            return $trans;
        });
    }
}
