<?php

declare(strict_types=1);

namespace eCamp\Lib\Hal\LinkGenerator;

use eCamp\Lib\Diactoros\TemplatedUri;
use Mezzio\Hal\LinkGenerator\UrlGeneratorInterface;
use Mezzio\Helper\ServerUrlHelper;
use Mezzio\Helper\UrlHelper;
use Psr\Http\Message\ServerRequestInterface;

class TemplatedUrlGenerator implements UrlGeneratorInterface {

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @var null|ServerUrlHelper
     */
    private $serverUrlHelper;

    public function __construct(UrlHelper $urlHelper, ServerUrlHelper $serverUrlHelper = null) {
        $this->urlHelper = $urlHelper;
        $this->serverUrlHelper = $serverUrlHelper;
    }

    public function generate(
        ServerRequestInterface $request,
        string $routeName,
        array $routeParams = [],
        array $queryParams = []
    ): string {
        $path = $this->urlHelper->generate($routeName, $routeParams, $queryParams);

        if (!$this->serverUrlHelper) {
            return $path;
        }

        $serverUrlHelper = clone $this->serverUrlHelper;
        $serverUrlHelper->setUri(new TemplatedUri((string)$request->getUri()));
        return $serverUrlHelper($path);
    }
}
