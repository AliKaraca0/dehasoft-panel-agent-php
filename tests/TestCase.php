<?php

namespace Dehasoft\PanelAgent\Tests;

use Dehasoft\PanelAgent\Laravel\PanelAgentServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [PanelAgentServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('panel-agent.token', 'test-token');
    }
}
