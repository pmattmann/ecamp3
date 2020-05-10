<?php

declare(strict_types=1);

use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

return static function (Application $app, MiddlewareFactory $factory, ContainerInterface $container) : void {
    $app->get('/', eCamp\Api\Handler\IndexHandler::class, 'index');
    $app->get('/api', eCamp\Api\Handler\ApiHandler::class, 'api');

    $app->route(
        '/api/organizations[/{id}]',
        eCamp\Api\ResourceHandler\OrganizationHandler::class,
        ['get'],
        'api.organizations'
    );
    $app->route(
        '/api/camptypes[/{id}[/{collection}]]',
        eCamp\Api\ResourceHandler\CampTypeHandler::class,
        ['get', 'post', 'patch', 'delete'],
        'api.camptypes'
    );
    $app->route(
        '/api/eventtypes[/{id}]',
        eCamp\Api\ResourceHandler\EventTypeHandler::class,
        ['get', 'post', 'delete'],
        'api.eventtypes'
    );

    $app->route(
        '/api/camps[/{id}]',
        eCamp\Api\ResourceHandler\CampHandler::class,
        ['get'],
        'api.camps'
    );
};
