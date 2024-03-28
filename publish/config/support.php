<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use OnixSystemsPHP\HyperfSupport\Transport\Comment\CommentSlackTransport;
use OnixSystemsPHP\HyperfSupport\Transport\Comment\CommentTrelloTransport;
use OnixSystemsPHP\HyperfSupport\Transport\Ticket\TicketSlackTransport;
use OnixSystemsPHP\HyperfSupport\Transport\Ticket\TicketTrelloTransport;

return [
    'app' => [
        // 'domain' => '',
        // 'icon' => '',
        // 'name' => '',
        // 'team_name' => ''
    ],
    'transports' => [
        'ticket' => [
            TicketSlackTransport::class,
            TicketTrelloTransport::class,
        ],
        'comment' => [
            CommentSlackTransport::class,
            CommentTrelloTransport::class,
        ],
    ],
    'slack' => [
        // 'token' => '',
        // 'channel_id' => '',
        // 'trello_icon' => '',
        // 'app_icon' => '',
        'mentions' => [
            // 'Tweak' => [],
            // 'Feature Request' => [''],
            //'Bug' => [
            //    1 => [''],
            //    2 => [''],
            //    3 => [''],
            //    4 => [''],
            //],
            'default' => [],
        ],
        'custom_fields' => ['type', 'status']
    ],
    'trello' => [
        // Fulfill with your API keys and board name.
        // 'key' => '',
        // 'token' => '',
        // 'boardName' => '',
        'members' => [
            // Fulfill with trello usernames
            // 'Bug' => [''],
            // 'Tweak' => [''],
            // 'Feature Request' => [''],
            // 'default' => [''],
        ],
        'lists' => [
            // Fulfill with trello lists
            // 'New' => '',
            // 'In Progress' => '',
            // 'Done' => '',
            // 'default' => '',
        ],
        'custom_fields' => ['type', 'priority'],
        'trigger_lists' => [
            // Fulfill with alert lists, lists in which you move card and all users in Slack will be notified.
            // 'In Progress',
            // 'Done',
        ],
    ],
    'ticket' => [
        'done_status' => 'Done',
        'custom_fields' => [
            'type' => ['Bug', 'Tweak', 'Feature Request'],
            'status' => ['New', 'In Progress', 'Done'],
            'level' => [1, 2, 3, 4],
            'priority' => ['Low', 'Medium', 'High', 'Highest'],
        ],
    ],
    'integrations' => [
        'trello' => [
            // Fulfill with Trello Api account username, from what username account Trello API will send requests.
            // 'username' => 'local'
        ],
        'slack' => [
            // Fulfill with channel id.
            // 'C123123' => 'local'
        ],
    ],
];
