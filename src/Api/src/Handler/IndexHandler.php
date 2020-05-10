<?php

declare(strict_types=1);

namespace eCamp\Api\Handler;

use eCamp\Core\Handler\AbstractHandler;
use Mezzio\Hal\HalResource;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class IndexHandler extends AbstractHandler {

    public function handle(ServerRequestInterface $request): ResponseInterface {
        $resource = new HalResource([
            'title' => 'eCamp3 API'
        ], [
            $this->getLinkGenerator()->fromRoute('self', $request, 'index'),
            $this->getLinkGenerator()->fromRoute('api', $request, 'api')
        ]);

        return $this->ok(
            $this->getJsonRenderer()->render($resource),
            ['Content-Type' => 'application/hal+json']
        );
    }
}
