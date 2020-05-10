<?php

declare(strict_types=1);

use Doctrine\Common\Annotations\AnnotationRegistry;
use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

// Delegate static file requests back to the PHP built-in webserver
if (PHP_SAPI === 'cli-server' && $_SERVER['SCRIPT_FILENAME'] !== __FILE__) {
    return false;
}

chdir(__DIR__ . '/..');

require __DIR__ . '/../vendor/autoload.php';

// Remove!
AnnotationRegistry::registerFile(
    __DIR__ . '/../vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php'
);


// Self-called anonymous function that creates its own scope and keeps the global namespace clean.
(function () {

    /** @var ContainerInterface $container */
    $container = require __DIR__ . '/../config/container.php';
    /** @var Application $app */
    $app = $container->get(Application::class);
    /** @var MiddlewareFactory $factory */
    $factory = $container->get(MiddlewareFactory::class);

    // Execute programmatic/declarative middleware pipeline and routing configuration statements
    (require __DIR__ . '/../config/pipeline.php')($app, $factory, $container);
    (require __DIR__ . '/../config/routes.php')($app, $factory, $container);

    //var_dump($app->getRoutes());

    $app->run();
})();
