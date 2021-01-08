<?php

namespace Core\Http\Controllers\Manage;

use Core\Facades\Site;
use Core\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\StatefulGuard;
use Core\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ManageLoginController extends Controller
{
    use AuthenticatesUsers;

    /** @var  array  Scripts for manage panel */
    protected $scripts = [
        0 => [
        ],
    ];

    /** @var  array  Styles for manage panel */
    protected $styles = [
        0 => [
            'manage/assets/public/css/login.css',
        ],
    ];

    /**
     * @var string Guard name to login administrator.
     */
    protected $guard = 'admin';

    /**
     * @var string Where to redirect users after login.
     */
    protected $redirectTo = 'manage';

    /**
     * Show the manage login form.
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View|Response|View
     */
    public function showLoginForm()
    {
        Site::setAssetScripts($this->scripts)
            ->setAssetStyles($this->styles)
            ->addMetaTag('meta', ['name' => 'theme-color', 'content' => '#3053AD'])
            ->addMetaTag('meta', ['name' => 'msapplication-navbutton-color', 'content' => '#3053AD'])
            ->addMetaTag('meta', ['name' => 'apple-mobile-web-app-status-bar-style', 'content' => '#3053AD']);
        Site::setMetaIndex(false);
        Site::setMetaFollow(false);
        return view('manage.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param Request $request
     *
     * @return  RedirectResponse|Response|JsonResponse
     *
     * @throws  ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            $this->sendLockoutResponse($request);

            return null;
        }

        // Chose guard to login
        $asAdmin = (bool)$request->input('as_admin');
        $this->guard = $asAdmin ? 'admin' : 'manager';

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        $this->sendFailedLoginResponse($request);

        return null;
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param Request $request
     *
     * @return  array
     */
    protected function credentials(Request $request): array
    {
        $credentials = $request->only($this->username(), 'password');
        $credentials['blocked'] = false;
        if ($this->guard === 'admin') {
            $credentials['deleted'] = false;
        }

        return $credentials;
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param Request $request
     *
     * @return  bool
     */
    protected function attemptLogin(Request $request): bool
    {
        return $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );
    }

    /**
     * The user has been authenticated.
     *
     * @param Request $request
     * @param mixed $user
     *
     * @return  mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if (method_exists($user, 'updateLastLogin')) {
            $user->updateLastLogin();
        }

        return null;
    }

    /**
     * Log the user out of the application.
     *
     * @param Request $request
     * @return Response|JsonResponse
     */
    public function logout(Request $request)
    {
        $this->guard = Auth::guard('manager')->check() ? 'manager' : 'admin';

        $this->guard()->logout();

        $request->session()->invalidate();

        return response()->json(['redirect' => '/manage']);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return  StatefulGuard
     */
    protected function guard(): StatefulGuard
    {
        return Auth::guard($this->guard);
    }
}
