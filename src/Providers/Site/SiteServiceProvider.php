<?php

namespace Core\Providers\Site;

use Core\Foundation\Application;
use Core\Traits\Site\AssetsManipulation;
use Core\Traits\Site\MetaData;
use Core\Traits\Site\Locales;
use Core\Traits\Site\MobileDetector;
use Core\Traits\Site\Profile;
use Detection\MobileDetect;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;


/**
 * SiteServiceProvider is a class for managing site's global configuration, e.g. language, title, meta data, assets
 * and some other stuff
 *
 * @author lozovoyv@gmail.com
 *
 */
class SiteServiceProvider extends ServiceProvider
{
    use AssetsManipulation;
    use MetaData;
    use Locales;
    use MobileDetector;
    use Profile;

    /** @var  Application  The application instance. */
    protected $app;

    /** @var string  Name of currently running server. */
    protected $serverName;

    /** @var  array  Asset array for scripts. */
    protected $scripts;

    /** @var  array  Asset array for styles to inject. */
    protected $injectStyles;

    /** @var  array  Asset array for CSS. */
    protected $styles;

    /** @var  string */
    protected $metaTitle;

    /** @var  string */
    protected $metaDescription;

    /** @var  string */
    protected $metaKeywords;

    /** @var  boolean */
    protected $metaIndex = true;

    /** @var  boolean */
    protected $metaFollow = true;

    /** @var  string */
    protected $metaCanonical;

    /** @var  string */
    protected $metaPrev;

    /** @var  string */
    protected $metaNext;

    /** @var  array  Custom meta tags array. */
    protected $metaTags;

    /** @var  MobileDetect  Mobile detection handler. */
    protected $mobileDetector;

    /** @var  string  Template name. */
    protected $template;

    /** @var  string  Current site key. */
    protected $profile;

    /**
     * Register SiteServiceProvider and bind it to App container
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->instance('opx.site', $this);

        $this->profile = $this->profile();
    }

    /**
     * Bootstrap SiteServiceProvider and load it's config.
     *
     * @return void
     */
    public function boot(): void
    {
//        // Init configuration
//        conf('site', config('site'), true);
//        // Override lang and fallback lang. Is needed for managing via backend.
//        $this->lang($this->app->getLocale());
//        $this->fallbackLang($this->app->getL());
//
        // get server name
        $this->serverName = preg_replace('/https*:\/\/(www\.)*/', '', request()->root());

//        config(['mail.from.address' => 'no-reply@' . $serverName]);
//        config(['mail.from.name' => $this->title]);
//
        // Get CSS styles and JS scripts for current profile

        $styles = $this->app->make('config')->get('site.styles');
        $styles = $styles[$this->profile] ?? $styles['default'];

        $scripts = $this->app->make('config')->get('site.scripts');
        $scripts = $scripts[$this->profile] ?? $scripts['default'];

        $this->setAssetStyles($styles)
            ->setAssetScripts($scripts);

        $title = $this->app->make('config')->get('site.title');

        if ($title) {
            $this->setMetaTitle($title);
        }

//        $this->favIcon = conf('site.favicon');
        $this->template = $this->app->make('config')->get('site.template', 'default');

        // Register view namespaces for site

        $isAjaxRequest = request()->ajax();

        View::addNamespace('site',
            $isAjaxRequest
                ? $this->app->basePath('templates' . DIRECTORY_SEPARATOR . 'ajax')
                : $this->app->basePath('templates')
        );
    }

    /**
     * Get name of server.
     *
     * @return  string
     */
    public function getServerName(): string
    {
        return $this->serverName;
    }
}
