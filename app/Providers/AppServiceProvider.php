<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobProcessed;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            if(!Auth::guest())
                $view->with('user', Auth::user());
        });

        DB::listen(function ($query) {
            Log::channel('sql')->info($query->sql, ['data' => $query->bindings]);
        });

        Queue::before(function (JobProcessing  $event) {
            if(property_exists($event->job, 'chain_id')) {
                $name = $event->job->payload()['displayName'];
                Log::channel('chains')->info(sprintf("%s %s start", $event->job->chain_id, $name));
            }
        });

        Queue::after(function (JobProcessed $event) {
            if(property_exists($event->job, 'chain_id')) {
                $name = $event->job->payload()['displayName'];
                Log::channel('chains')->info(sprintf("%s %s success", $event->job->chain_id, $name));
            }
        });

        Queue::failing(function (JobFailed $event) {
            if(property_exists($event->job, 'chain_id')) {
                $name = $event->job->payload()['displayName'];
                Log::channel('chains')->info(sprintf("%s %s failed: %s",  $event->job->chain_id, $name, $event->exception->getMessage()));
            }
        });
    }
}
