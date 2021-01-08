<?php

namespace Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RedirectIndex
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $guard
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {

	    $uri = $request->url();

	    if(Str::endsWith($uri, ['index.php', 'index.htm', 'index.html', 'index'])) {
			$parameters = $request->getQueryString();
		    $url = $request->getSchemeAndHttpHost() . (!empty($parameters) ? '?'.$parameters : '');

		    return redirect()->to($url, 301);
	    }

	    return $next($request);

    }
}
