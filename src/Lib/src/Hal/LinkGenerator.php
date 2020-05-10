<?php

declare(strict_types=1);

namespace eCamp\Lib\Hal;

use Mezzio\Hal\Link;
use Mezzio\Hal\LinkGenerator as HalLinkGenerator;
use Mezzio\Hal\LinkGenerator\UrlGeneratorInterface;
use Psr\Http\Message\ServerRequestInterface;

class LinkGenerator extends HalLinkGenerator {

    /**
     * @var UrlGeneratorInterface
     */
    private $templatedUrlGenerator;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        UrlGeneratorInterface $templatedUrlGenerator
    ) {
        parent::__construct($urlGenerator);

        $this->templatedUrlGenerator = $templatedUrlGenerator;
    }

    /**
     * Creates a templated link
     */
    public function templatedFromRoute(
        string $relation,
        ServerRequestInterface $request,
        string $routeName,
        array $routeParams = [],
        array $queryParams = [],
        array $attributes = []
    ): Link {
        return new Link($relation, $this->templatedUrlGenerator->generate(
            $request,
            $routeName,
            $routeParams,
            $queryParams
        ), true, $attributes);
    }
}
