<?php

declare(strict_types=1);

namespace eCamp\Api\Handler;

use eCamp\Core\Handler\AbstractHandler;
use Mezzio\Hal\HalResource;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ApiHandler extends AbstractHandler {

    public function handle(ServerRequestInterface $request): ResponseInterface {
        $resource = new HalResource([
            'title' => 'eCamp3 API'
        ], [
            $this->getLinkGenerator()->fromRoute('self', $request, 'api'),
            $this->getLinkGenerator()->templatedFromRoute('camps', $request, 'api.camps'),
        ]);

        return $this->ok(
            $this->getJsonRenderer()->render($resource),
            ['Content-Type' => 'application/hal+json']
        );
    }
}
