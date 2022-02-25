<?php

namespace ATWENTYFIVE\Middlewares;

use ATWENTYFIVE\ResponseBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Authentication implements MiddlewareInterface {

    /**
     * @Inject
     * @var ResponseBuilder
     */
    private $ResponseBuilder;

    /**
     * @Inject("Mongo")
     */
    private $Mongo;

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ) : ResponseInterface
    {
        // By default client-auth is false
        $request = $request->withAttribute('client-auth', false);
        $clientAgent = $request->getAttribute('client-agent');
        $clientIp = $request->getAttribute('client-ip');

        // Check is header present
        if (!$request->hasHeader("Authorization")) {
            return $handler->handle($request);
        }

        // Parse the header and check validity
        $auth = $request->getHeader("Authorization");
        if(!isset($auth[0])) {
        	return $this->ResponseBuilder
        			->error()
        			->code(0x0001)
        			->message('Invalid login data')
        			->build();
        }

        $explodedAuth = explode(" ", $auth[0]);
        if(count($explodedAuth) !== 2 || $explodedAuth[0] !== 'Basic') {
        	return $this->ResponseBuilder
        			->error()
        			->code(0x0001)
        			->message('Invalid login data')
        			->build();
        }

        $decodedAuth = base64_decode($explodedAuth[1]);

        $explodedInfo = explode(":", $decodedAuth);
        if(count($explodedInfo) !== 2) {
        	return $this->ResponseBuilder
        			->error()
        			->code(0x0001)
        			->message('Invalid login data')
        			->build();
        }

        $name = $explodedInfo[0];
        $password = md5($explodedInfo[1]);

        // Find user
        $user = $this->Mongo->user->findOne(['name' => $name, 'password' => $password]);

        if(!$user) {
        	return $this->ResponseBuilder
	    			->error()
	    			->code(0x0001)
	    			->message('Invalid name or password')
	    			->build();
        }

        $request = $request->withAttribute (
            'client-auth', 
            [
                'success' => true
            ]
        );

        return $handler->handle($request);
    }
}