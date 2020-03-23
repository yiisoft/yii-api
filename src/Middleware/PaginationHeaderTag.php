<?php

namespace Yiisoft\Yii\Rest\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class PaginationHeaderTag implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $links = [];
        /**
         * TODO Need to implement passing the Paginator to this class
         * $paginationLinks = $paginator->getLinks();
         */
        $paginationLinks = [];
        foreach ($paginationLinks as $rel => $url) {
            $links[] = "<$url>; rel=$rel";
        }

        return $response
            ->withHeader('Link', implode(', ', $links))
            ->withHeader('X-Pagination-Total-Count', 0)
            ->withHeader('X-Pagination-Page-Count', 0)
            ->withHeader('X-Pagination-Current-Page', 0)
            ->withHeader('X-Pagination-Per-Page', 0);
    }
}
