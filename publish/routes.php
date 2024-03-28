<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use Hyperf\HttpServer\Router\Router;
use OnixSystemsPHP\HyperfSupport\Controller\Api\CommentsController;
use OnixSystemsPHP\HyperfSupport\Controller\Api\TicketsController;
use OnixSystemsPHP\HyperfSupport\Controller\Webhooks\SlackController;
use OnixSystemsPHP\HyperfSupport\Controller\Webhooks\TrelloController;

require_once './vendor/onix-systems-php/hyperf-file-upload/publish/routes.php';

Router::addGroup('/v1/support/', function () {
    Router::addGroup('tickets', function () {
        Router::get('', [TicketsController::class, 'index']);
        Router::post('', [TicketsController::class, 'store']);
        Router::get('/{id}', [TicketsController::class, 'show']);
        Router::put('/{id}', [TicketsController::class, 'update']);
        Router::delete('/{id}', [TicketsController::class, 'destroy']);
    });
    Router::addGroup('comments', function(){
        Router::get('', [CommentsController::class, 'index']);
        Router::post('', [CommentsController::class, 'store']);
        Router::get('/{id}', [CommentsController::class, 'show']);
        Router::put('/{id}', [CommentsController::class, 'update']);
        Router::delete('/{id}', [CommentsController::class, 'destroy']);
    });
    Router::addGroup('webhooks', function(){
        Router::get('/trello', [TrelloController::class, 'init']);
        Router::post('/trello', [TrelloController::class, 'webhook']);
        Router::post('/slack', [SlackController::class, 'webhook']);
    });
});
