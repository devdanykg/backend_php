<?php

namespace ATWENTYFIVE\Handlers\Bank;

use ATWENTYFIVE\ResponseBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CreateBank implements RequestHandlerInterface
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
        $data = $request->getAttribute('json-data');
        $auth = $request->getAttribute('client-auth');
        if(!$auth || !is_array($auth) || $auth['success'] !== true) {
        	return $this->ResponseBuilder
        			->error()
        			->message('Unauthorized')
        			->code(0x0001)
				->status(401)
        			->build();
        }

        if(!isset($data['title']) || strlen($data['title']) < 1) {
        	return $this->ResponseBuilder
        			->error()
        			->message('Invalid data')
        			->code(0x0002)
        			->build();
        }

        $searchBank = $this->Mongo->bank->findOne([
        	'title' => $data['title']
        ]);

        if($searchBank) {
        	return $this->ResponseBuilder
        			->error()
        			->message('This bank already exists')
        			->code(0x0003)
        			->build();
        }

        $insertedBank = $this->Mongo->bank->insertOne([
        	'title' => $data['title']
        ]);

        if(!$insertedBank->isAcknowledged()) {
        	throw new \Exception('Cannot create bank, db error');
        }

        return $this->ResponseBuilder
        		->success()
        		->data(['bankId' => (string)$insertedBank->getInsertedId(),
        				'title' => $data['title']])
        		->build();
    }
}
