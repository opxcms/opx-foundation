<?php

namespace Core\Facades;

use Core\Providers\Site\SiteServiceProvider;
use Detection\MobileDetect;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string getServerName()
 * @method static SiteServiceProvider addAssetScript($scripts, int $priority = 1)
 * @method static SiteServiceProvider setAssetScripts(array $scripts = null)
 * @method static array|null getAssetScripts($priority = null)
 * @method static SiteServiceProvider addAssetStyle($styles, $priority = 1)
 * @method static SiteServiceProvider setAssetStyles(array $styles = null)
 * @method static array|null getAssetStyles($priority = null)
 * @method static string scripts()
 * @method static string styles()
 * @method static string stylesAsync()
 * @method static SiteServiceProvider addInjectStyle($styles): self
 * @method static string injectStyles()
 * @method static string getLocale()
 * @method static array installedLocales()
 * @method static SiteServiceProvider setLocale($locale, $skipCheck = false)
 * @method static string localeHtml()
 * @method static string localeSelectorHtml($url = null)
 * @method static MobileDetect mobileDetect()
 * @method static SiteServiceProvider addMetaTag($name, $content)
 * @method static string getTitle()
 * @method static SiteServiceProvider setMetaTitle($metaTitle)
 * @method static SiteServiceProvider setMetaDescription($metaDescription)
 * @method static SiteServiceProvider setMetaKeywords($metaKeywords)
 * @method static SiteServiceProvider setMetaIndex($metaIndex)
 * @method static SiteServiceProvider setMetaFollow($metaFollow)
 * @method static SiteServiceProvider setMetaCanonical($metaCanonical)
 * @method static SiteServiceProvider setMetaPrev($metaPrev)
 * @method static SiteServiceProvider setMetaNext($metaNext)
 * @method static SiteServiceProvider setMetaTags($metaTags)
 * @method static string metadata()
 * @method static SiteServiceProvider setSite($site = null)
 * @method static string getSite()
 * @method static string profile()
 *
 *
 * @see \Core\Providers\Site\SiteServiceProvider
 */
class Site extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'opx.site';
    }
}
