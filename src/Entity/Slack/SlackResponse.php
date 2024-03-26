<?php

namespace OnixSystemsPHP\HyperfSupport\Entity\Slack;

class SlackResponse
{
    public function __construct(public string $ts, public array $message) {}
}
