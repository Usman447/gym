<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Compilers\BladeCompiler;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Register @permission Blade directive for compatibility with old Entrust-style syntax
        // Supports both @permission('permission') and @permission(['permission1', 'permission2'])
        $this->callAfterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {
            $bladeCompiler->directive('permission', function ($expression) {
                // The expression is already parsed by Blade, so we can use it directly
                // For @permission(['permission1', 'permission2']), $expression will be the array
                // For @permission('permission'), $expression will be the string
                return "<?php if (Auth::check() && Auth::user()->can({$expression})): ?>";
            });

            $bladeCompiler->directive('endpermission', function () {
                return "<?php endif; ?>";
            });
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
