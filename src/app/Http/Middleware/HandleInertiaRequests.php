<?php

namespace App\Http\Middleware;

use Inertia\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'isSuperadmin' => fn() => Auth::check() ? (function () {
                $user = Auth::user();

                if ($user->hasRole('superadmin')) {
                    return true;
                }

                return false;
            })() : false,

            'isAdmin' => fn() => Auth::check() ? (function () {
                $user = Auth::user();

                if ($user->hasRole('administrator')) {
                    return true;
                }

                return false;
            })() : false,

            'isOJT' => fn() => Auth::check() ? (function () {
                $user = Auth::user();

                if ($user->hasRole('ojt')) {
                    return true;
                }

                return false;
            })() : false,

            'canAssignRole' => fn() => Auth::check() ? (function () {
                $user = Auth::user();

                if ($user->can('can assign a role')) {
                    return true;
                }

                return false;
            })() : false,
        ]);
    }
}
