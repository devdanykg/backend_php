<?php

namespace ATWENTYFIVE;

use function Http\Response\send;
use function FastRoute\simpleDispatcher;
use Relay\Relay;
use DI\ContainerBuilder;
use FastRoute\RouteCollector;
use FastRoute\Dispatcher;
use Zend\Diactoros\ServerRequestFactory;
use Middlewares\RequestHandler;
use Middlewares\FastRoute;
use ATWENTYFIVE\Middlewares\RequestParser;
use ATWENTYFIVE\Middlewares\Authentication;

// Methods
use ATWENTYFIVE\Handlers\Bank\GetBanks;
use ATWENTYFIVE\Handlers\Bank\CreateBank;
use ATWENTYFIVE\Handlers\User\CreateUser;

require 'vendor/autoload.php';

try {
	// Create and init the ServerRequest (PSR-7)
	$request = ServerRequestFactory::fromGlobals();

	// Set up the DIC (PSR-11)
	$containerBuilder = new ContainerBuilder();
    	$containerBuilder->useAnnotations(true);
    	$containerBuilder->addDefinitions('database.php');
	$container = $containerBuilder->build();

	// Init dispatcher for routes
	$dispatcher = simpleDispatcher(function(RouteCollector $r) use ($container) {
		// Group for api banks
		$r->addGroup('/bank', function(RouteCollector $r) use ($container) {
			$r->addRoute(['GET'], '/getBanks', $container->get(GetBanks::class)); // Works & Tested
			$r->addRoute(['POST'], '/createBank', $container->get(CreateBank::class)); // Works & Tested
		});
		// Group for api users
		$r->addGroup('/user', function(RouteCollector $r) use ($container) {
			$r->addRoute(['POST'], '/register', $container->get(CreateUser::class)); // Works & Tested
		});
	});

	// Create a middleware pipe (PSR-15)
	$pipe[] = $container->get(RequestParser::class);
	$pipe[] = $container->get(Authentication::class);
	$pipe[] = new FastRoute($dispatcher);
    	$pipe[] = new RequestHandler($container);

	// Start process the pipe
	$handler = new Relay($pipe);
	$response = $handler->handle($request);

	// For errors display only
	$uri = $_SERVER['REQUEST_URI'];
	if (false !== $pos = strpos($uri, '?')) { $uri = substr($uri, 0, $pos); }
	$uri = rawurldecode($uri);
	$routeInfo = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $uri);
	switch ($routeInfo[0]) {
		case Dispatcher::NOT_FOUND: {
			$response = (new ResponseBuilder) 
						->error()
						->code(0x0001)
						->status(404)
						->message("Method not found")
						->build();
			break;
		}
	}

	// Display error or response --
	send($response);

} catch(\Exception $e) {
	send (
        (new ResponseBuilder)
        ->error()
        ->code(0x0001)
        ->message($e->getMessage())
        ->build()
    );
}

?>
