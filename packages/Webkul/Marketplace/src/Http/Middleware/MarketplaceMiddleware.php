<?php

namespace Webkul\Marketplace\Http\Middleware;

use Closure;

class MarketplaceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! core()->getConfigData('marketplace.settings.general.status')) {
            session()->flash('warning', 'Marketplace is disabled.');
            
            return redirect()->back();
        }

        return $next($request);
    }
}
