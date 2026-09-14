<?php

namespace Dehasoft\PanelAgent\Tests;

use Dehasoft\PanelAgent\PanelAgent;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class PanelAgentTest extends TestCase
{
    private function agentWithMockedResponses(array $responses): PanelAgent
    {
        $handler = new MockHandler($responses);
        $http = new Client(['handler' => HandlerStack::create($handler)]);

        return new PanelAgent('https://panel.test', 'secret-token', 5, $http);
    }

    public function test_it_reports_success_on_a_2xx_response(): void
    {
        $agent = $this->agentWithMockedResponses([new Response(201)]);

        $response = $agent->contactMessage(['message' => 'Merhaba']);

        $this->assertTrue($response->ok);
        $this->assertSame(201, $response->status);
        $this->assertNull($response->error);
    }

    public function test_it_reports_failure_on_a_non_2xx_response(): void
    {
        $agent = $this->agentWithMockedResponses([new Response(401, [], 'Unauthorized.')]);

        $response = $agent->contactMessage(['message' => 'Merhaba']);

        $this->assertFalse($response->ok);
        $this->assertSame(401, $response->status);
        $this->assertSame('Unauthorized.', $response->error);
    }

    public function test_it_never_throws_when_the_panel_is_unreachable(): void
    {
        $handler = new MockHandler([
            new \GuzzleHttp\Exception\ConnectException(
                'Connection refused',
                new \GuzzleHttp\Psr7\Request('POST', 'https://panel.test/api/webhook/contact')
            ),
        ]);
        $http = new Client(['handler' => HandlerStack::create($handler)]);
        $agent = new PanelAgent('https://panel.test', 'secret-token', 5, $http);

        $response = $agent->contactMessage(['message' => 'Merhaba']);

        $this->assertFalse($response->ok);
        $this->assertNull($response->status);
        $this->assertNotEmpty($response->error);
    }
}
