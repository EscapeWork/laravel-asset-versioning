<?php

namespace EscapeWork\Assets\Middleware;

use Closure;
use EscapeWork\Assets\Facades\Asset;
use Illuminate\Http\Request;

class HTTP2ServerPush
{
    /**
     * @var [type]
     */
    protected $response;

    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $this->response = $next($request);

        if (! $request->ajax() && ! $request->wantsJson()) {
            $this->addHeaders();
        }

        return $this->response;
    }

    protected function addHeaders()
    {
        if (config('assets.http2-server-push') && Asset::hasHTTP2Links()) {
            $this->response->headers->set('Link', Asset::generateHTTP2Links(), false);
        }
    }
}
