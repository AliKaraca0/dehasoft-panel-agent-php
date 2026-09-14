<?php

namespace Dehasoft\PanelAgent\Laravel\Facades;

use Dehasoft\PanelAgent\PanelAgent as PanelAgentClient;
use Dehasoft\PanelAgent\PanelAgentResponse;
use Illuminate\Support\Facades\Facade;

/**
 * @method static PanelAgentResponse contactMessage(array $payload)
 *
 * @see PanelAgentClient
 */
class PanelAgent extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return PanelAgentClient::class;
    }
}
