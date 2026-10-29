<?php

namespace App\Providers;

use App\Models\Userrole;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $userId = Auth::id();

                $userRoles = UserRole::with('role', 'module')
                    ->where('userid', $userId)
                    ->where('status', 1)
                    ->where('deleted', 0)
                    ->get();

                // 1. แชร์แบบ role name รวมตาม module (ใช้ใน Blade เมนู)
                $userModuleRoles = $userRoles->mapToGroups(function ($item) {
                    return [$item->module->modulename => collect([$item->role->rolename])];
                })->toArray();
                // 2. แชร์แบบ group UserRole model (ใช้ข้อมูลเต็ม)
                $userModules = $userRoles->groupBy('module.modulename');

                // แชร์ให้ view ทุกตัว

                $view->with('userModuleRoles', $userModuleRoles)
                ->with('userModules', $userModules);
            }
        });
    }

}
