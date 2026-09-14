<?php

namespace Dehasoft\PanelAgent\Tests;

class HealthCheckEndpointTest extends TestCase
{
    public function test_it_responds_ok_with_the_correct_token(): void
    {
        $this->withHeaders(['Authorization' => 'Bearer test-token'])
            ->getJson('/api/health')
            ->assertOk()
            ->assertJson(['status' => 'ok'])
            ->assertJsonStructure(['status', 'php_version', 'app_version', 'timestamp']);
    }

    public function test_it_rejects_a_missing_or_wrong_token(): void
    {
        $this->getJson('/api/health')->assertUnauthorized();

        $this->withHeaders(['Authorization' => 'Bearer wrong-token'])
            ->getJson('/api/health')
            ->assertUnauthorized();
    }
}
