<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use App\Models\MenuPassword;

class PasswordEditMiddleware
{
    /**
     * The response factory instance.
     *
     * @var \Illuminate\Contracts\Routing\ResponseFactory
     */
    protected $responseFactory;

    /**
     * The URL generator instance.
     *
     * @var \Illuminate\Contracts\Routing\UrlGenerator
     */
    protected $urlGenerator;

    /**
     * The password timeout.
     *
     * @var int
     */
    protected $passwordTimeout;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Routing\ResponseFactory  $responseFactory
     * @param  \Illuminate\Contracts\Routing\UrlGenerator  $urlGenerator
     * @param  int|null  $passwordTimeout
     * @return void
     */
    public function __construct(ResponseFactory $responseFactory, UrlGenerator $urlGenerator, $passwordTimeout = null)
    {
        $this->responseFactory = $responseFactory;
        $this->urlGenerator = $urlGenerator;
        $this->passwordTimeout = $passwordTimeout ?: 10800;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::user()->parent){
        // if(!Auth::user()->parent){
            $this->passwordTimeout = 60;

            // $url = $request->route()->getName();
            $url = $request->url();
            $url = str_replace('http://localhost/liatmenu_admin/public/', 'https://admin.scaneat.id/', $url);

            $menu_has_password = MenuPassword::join('menus', 'menus.id', '=', 'menu_passwords.menu_id')
                ->select('url')
                ->where('url', $url)
                ->where('using_password', 1)
                ->where('menus.active', 1)
                ->where('user_id', (Auth::user()->parent) ? Auth::user()->parent->id : Auth::id())
                ->first();

            if($menu_has_password){
                if ($this->shouldConfirmPassword($request)) {
                    if ($request->expectsJson()) {
                        return $this->responseFactory->json([
                            'message' => 'Password confirmation required.',
                        ], 423);
                    }

                    return $this->responseFactory->redirectGuest(
                        $this->urlGenerator->route('password.confirm')
                    );
                }
            }
        }

        return $next($request);
    }

    /**
     * Determine if the confirmation timeout has expired.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function shouldConfirmPassword($request)
    {
        $confirmedAt = time() - $request->session()->get('auth.password_confirmed_at', 0);

        return $confirmedAt > $this->passwordTimeout;
    }
}
