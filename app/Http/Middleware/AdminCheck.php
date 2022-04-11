<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!__c_user()  && $request->route()->getName() != 'admin-login') {

            return redirect(route('admin-login').'?redirect_to='.urlencode(route($request->route()->getName())))->with('errormsg', 'You must be logged In!');
        }
        
        return $next($request);
    }
}
