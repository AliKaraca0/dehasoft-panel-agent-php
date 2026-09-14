<?php

namespace Dehasoft\PanelAgent;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use Throwable;

/**
 * A thin, fire-and-forget client for reporting site events to the Dehasoft
 * control panel. Every call swallows transport failures by default and
 * returns a PanelAgentResponse instead of throwing — a panel outage should
 * never break the site embedding this agent.
 *
 * New event types are added as one method + one endpoint constant, e.g.
 * a future healthCheck() would call self::ENDPOINT_HEALTH the same way
 * contactMessage() calls self::ENDPOINT_CONTACT.
 */
final class PanelAgent
{
    private const ENDPOINT_CONTACT = '/api/webhook/contact';

    private HttpClient $http;

    public function __construct(
        private readonly string $panelUrl,
        private readonly string $token,
        private readonly int $timeout = 5,
        ?HttpClient $http = null,
    ) {
        $this->http = $http ?? new HttpClient;
    }

    public static function make(string $panelUrl, string $token, int $timeout = 5): self
    {
        return new self($panelUrl, $token, $timeout);
    }

    /**
     * Report a contact form submission to the panel.
     *
     * @param  array{name?: string, email?: string, subject?: string, message: string}  $payload
     */
    public function contactMessage(array $payload): PanelAgentResponse
    {
        return $this->post(self::ENDPOINT_CONTACT, $payload);
    }

    private function post(string $endpoint, array $payload): PanelAgentResponse
    {
        try {
            $response = $this->http->post(rtrim($this->panelUrl, '/').$endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->token,
                    'Accept' => 'application/json',
                ],
                'json' => $payload,
                'timeout' => $this->timeout,
                'http_errors' => false,
            ]);

            $status = $response->getStatusCode();

            if ($status >= 200 && $status < 300) {
                return PanelAgentResponse::success($status);
            }

            return PanelAgentResponse::failure($status, (string) $response->getBody());
        } catch (GuzzleException|Throwable $e) {
            return PanelAgentResponse::failure(null, $e->getMessage());
        }
    }
}
