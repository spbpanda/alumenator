<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Server;
use App\Models\User;
use App\Models\Variable;
use App\Observers\CategoryObserver;
use App\Observers\ItemObserver;
use App\Observers\ServerObserver;
use App\Observers\UserObserver;
use App\Observers\VariableObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (stripos(config('app.url'), 'https') === 0) {
            \URL::forceScheme('https');
        }

        Item::observe(ItemObserver::class);
        Category::observe(CategoryObserver::class);
        Server::observe(ServerObserver::class);
        User::observe(UserObserver::class);
        Variable::observe(VariableObserver::class);
    }
}
