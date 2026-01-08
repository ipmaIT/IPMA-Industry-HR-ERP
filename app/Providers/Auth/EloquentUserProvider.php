<?php

namespace App\Providers\Auth;

use Illuminate\Support\ServiceProvider;


// using this to override Illuminate\Auth\EloquentUserProvider
// what to override
use Illuminate\Auth\EloquentUserProvider as UserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

// class EloquentUserProvider extends ServiceProvider
class EloquentUserProvider extends UserProvider
{
	/**
	 * Register services.
	 */
	public function register(): void
	{
		//
	}

	/**
	 * Bootstrap services.
	 */
	public function boot(): void
	{
		//
	}

	// to prevent auto hash password by laravel when using plain or old hash driver password
	public function rehashPasswordIfRequired($user, array $credentials, $validated = true)
	{
		// Disable Laravel’s auto password rehash feature
		return;
	}

	public function validateCredentials(UserContract $user, array $credentials)
	{
		$plain = $credentials['password'];
		// dd($plain, $credentials['password']);
		// this is for plain text user password
		// dd($plain, $user->getAuthPassword());
		if ((($plain == $user->getAuthPassword()) && $user->belongstostaff->active == 1 && $user->active == 1) || ($plain == $user->getAuthPassword() && ($user->staff_id == 117 || $user->staff_id == 72))) {
			return true;
		}
		return false;
		// return $this->hasher->check($plain, $user->getAuthPassword());
	}
}
