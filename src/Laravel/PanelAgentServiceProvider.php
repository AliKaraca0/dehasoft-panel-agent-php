<?php

namespace Dehasoft\PanelAgent\Laravel;

use Dehasoft\PanelAgent\PanelAgent;
use Illuminate\Support\ServiceProvider;

class PanelAgentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/panel-agent.php', 'panel-agent');

        $this->app->singleton(PanelAgent::class, function () {
            return PanelAgent::make(
                (string) config('panel-agent.url'),
                (string) config('panel-agent.token'),
                (int) config('panel-agent.timeout', 5),
            );
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/panel-agent.php' => config_path('panel-agent.php'),
            ], 'panel-agent-config');
        }

        if (config('panel-agent.token')) {
            $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
        }
    }
}
