<?php

return [
    'url' => env('PANEL_AGENT_URL'),
    'token' => env('PANEL_AGENT_TOKEN'),
    'timeout' => env('PANEL_AGENT_TIMEOUT', 5),

    // The route this app exposes for the panel's outbound health-check poll.
    'health_path' => env('PANEL_AGENT_HEALTH_PATH', '/api/health'),
];
