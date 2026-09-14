# dehasoft/panel-agent

Thin client that reports site events (contact form messages today, health
checks later) to the Dehasoft control panel. Never throws on a panel outage —
every call returns a `PanelAgentResponse` with `ok`, `status`, `error`.

## Install

```bash
composer require dehasoft/panel-agent
```

## Plain PHP

```php
use Dehasoft\PanelAgent\PanelAgent;

$agent = PanelAgent::make('https://panel.dehasoft.com', $token);

$response = $agent->contactMessage([
    'name' => $name,
    'email' => $email,
    'subject' => $subject,
    'message' => $message,
]);

if (! $response->ok) {
    // log $response->error, but don't fail the visitor's form submission
}
```

## Laravel

The service provider auto-registers. Set env vars per site:

```env
PANEL_AGENT_URL=https://panel.dehasoft.com
PANEL_AGENT_TOKEN=the-site-specific-token-from-the-panel
```

```php
use Dehasoft\PanelAgent\Laravel\Facades\PanelAgent;

PanelAgent::contactMessage($validated);
```

To keep the visitor's request fast, dispatch it off the request cycle:

```php
dispatch(fn () => PanelAgent::contactMessage($validated))->afterResponse();
```

## Where does the token come from?

Generate it in the control panel on the site's detail page, under
**Webhook**. It's shown once — store it as `PANEL_AGENT_TOKEN` right away.

## Adding new event types

Each event is one method + one endpoint constant in `PanelAgent`. When the
panel adds health-check reporting, this package adds a `healthCheck()`
method the same shape as `contactMessage()` — no breaking changes to
existing calls.
