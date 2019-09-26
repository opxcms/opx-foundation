<?php

namespace Core\Http\Middleware;

use Closure;

class RedirectIndex
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

	    $uri = $request->url();

	    if(ends_with($uri, ['index.php', 'index.htm', 'index.html', 'index'])) {
			$parameters = $request->getQueryString();
		    $url = $request->getSchemeAndHttpHost() . (!empty($parameters) ? '?'.$parameters : '');

		    return redirect()->to($url, 301);
	    }

	    return $next($request);

    }
}
