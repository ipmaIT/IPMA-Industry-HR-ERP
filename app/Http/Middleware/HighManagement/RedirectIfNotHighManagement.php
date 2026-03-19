<?php
namespace App\Http\Middleware\HighManagement;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotHighManagement
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle(Request $request, Closure $next, $highManagement, $dept): Response
	{
		$hm = $request->user()->isHighManagement($highManagement, $dept);
		if(!$hm){
			return abort('403');
		}
		return $next($request);
	}
}
