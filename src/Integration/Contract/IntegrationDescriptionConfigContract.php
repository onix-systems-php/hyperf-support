<?php

declare(strict_types=1);

namespace OnixSystemsPHP\HyperfSupport\Integration\Contract;

use OnixSystemsPHP\HyperfSupport\Model\Ticket;

interface IntegrationDescriptionConfigContract
{
    /**
     * Get mentions of available integration according to ticket's custom_field property
     *
     * @param Ticket $ticket
     * @return array
     */
    public function getMentions(Ticket $ticket): array;
}
