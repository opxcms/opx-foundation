<?php

namespace Core\Http\Middleware;

use Closure;
use Core\Facades\Site;
use Illuminate\Http\Request;

class SwitchLocale
{
    /** @var  string  Name for prefix of key to store selected locale in session. */
    protected $localeSessionKeyPrefix = 'current_locale_for_';

    /** @var  string  Key to store selected locale in session. */
    protected $localeKey;

    /** @var  string  Key name to detect locale change request. */
    protected $localeRequestKey = 'locale';

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $localeKey
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $localeKey = 'site')
    {
        $this->localeKey = $localeKey;

        // If locale change request
        if ($locale = $request->input($this->localeRequestKey)) {
            $this->setLocale($request, $locale);

            return response()->redirectTo(url($request->path(), $request->except($this->localeRequestKey)));
        }

        // Try to load selected locale from session
        if($locale = $this->getStoredLocale($request)) {
            if(! Site::setLocale($locale)) {
                $this->setLocale($request, app()->getFallbackLocale());
            }

            return $next($request);
        }

        // Set default locale
        $this->setLocale($request, app()->getLocale());

        return $next($request);
    }

    /**
     * Make locale key for storing in session.
     *
     * @param string $key
     *
     * @return  string
     */
    public function makeKey(string $key): string
    {
        return $this->localeSessionKeyPrefix.$key;
    }

    /**
     * Get locale stored in session.
     *
     * @param Request $request
     *
     * @return  mixed|null
     */
    public function getStoredLocale(Request $request)
    {
        return $request->session()->get($this->makeKey($this->localeKey));
    }

    /**
     * Set new locale and store it in session.
     *
     * @param Request $request
     * @param string $locale
     *
     * @return  void
     */
    public function setLocale(Request $request, string $locale): void
    {
        Site::setLocale($locale);
        $request->session()->put($this->makeKey($this->localeKey), $locale);
    }
}
