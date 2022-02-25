<?php

namespace ATWENTYFIVE\Handlers\Bank;

use ATWENTYFIVE\ResponseBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetBanks implements RequestHandlerInterface
{
	/**
     * @Inject 
     * @var ResponseBuilder 
     * */
    private $ResponseBuilder;
    
    /** 
     * @Inject("Mongo")
     */
    private $Mongo;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
    	// Check request data
    	$data = $request->getAttribute('json-data');

    	// Create request for mongo
    	$response = $this->Mongo->bank->find();

    	// Display response = NULL
    	if(!$response) {
    		return $this->ResponseBuilder
    			->success()
    			->data([])
    			->build();
    	}

    	// Create array for response
    	$outputData = array_map(function($banks) {
    		$banks['_id'] = (string)$banks['_id'];
    		return $banks;
    	}, $response->toArray());

    	// Response data
    	return $this->ResponseBuilder
    		->success()
    		->data($outputData)
    		->build();
    }
}