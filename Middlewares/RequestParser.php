<?php

namespace ATWENTYFIVE\Middlewares;

use ATWENTYFIVE\ResponseBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestParser implements MiddlewareInterface {

    /** @var ResponseBuilder */
    private $ResponseBuilder;

    public function __construct (
        ResponseBuilder $responseBuilder
    ) {
        $this->ResponseBuilder = $responseBuilder;
    }

    public function process (
            ServerRequestInterface $request,
            RequestHandlerInterface $handler
        ):  ResponseInterface
    {
        if (
            $request->getHeaderLine('Content-Type') === 'application/json'
        ) {
            $body = (string)$request->getBody();

            if (
                !is_string($body) ||
                strlen($body) > 2000
            ) {
                return $this->ResponseBuilder
                    ->error()
                    ->code(0)
                    ->message('Request is too long')
                    ->build();
            }

            $json = json_decode (
                $body,
                true
            );

            if (!$json) {
                return $this->ResponseBuilder
                    ->error()
                    ->code(0)
                    ->message('Invalid data')
                    ->build();
            }

            $request = $request->withAttribute('json-data', $json);
        }

        return $handler->handle($request);
    }
}