<?php

use Dehasoft\PanelAgent\Laravel\Http\Controllers\HealthCheckController;
use Illuminate\Support\Facades\Route;

Route::get(config('panel-agent.health_path', '/api/health'), HealthCheckController::class)
    ->name('panel-agent.health');
