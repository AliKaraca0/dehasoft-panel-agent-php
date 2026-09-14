<?php

namespace Dehasoft\PanelAgent;

final class PanelAgentResponse
{
    private function __construct(
        public readonly bool $ok,
        public readonly ?int $status,
        public readonly ?string $error,
    ) {}

    public static function success(int $status): self
    {
        return new self(true, $status, null);
    }

    public static function failure(?int $status, string $error): self
    {
        return new self(false, $status, $error);
    }
}
