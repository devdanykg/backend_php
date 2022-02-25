<?php

namespace ATWENTYFIVE\Handlers\User;

use ATWENTYFIVE\ResponseBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CreateUser implements RequestHandlerInterface
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

    	if(!isset($data['name']) || !isset($data['password']) ||
    		strlen($data['name']) < 1 || strlen($data['password']) < 1) {
    		return $this->ResponseBuilder
    				->error()
    				->message('Invalid data')
    				->code(0x0002)
    				->build();
    	}

    	$searchUser = $this->Mongo->user->findOne([
    		'name' => $data['name']
    	]);

    	if($searchUser) {
    		return $this->ResponseBuilder
    				->error()
    				->message('This username already exists')
    				->code(0x0003)
    				->build();
    	}

    	$insertedUser = $this->Mongo->user->insertOne([
    		'name' => $data['name'],
    		'password' => md5($data['password'])
    	]);

    	if(!$insertedUser->isAcknowledged()) {
    		throw new \Exception('Cannot create user, db error');
    	}

    	return $this->ResponseBuilder
    			->success()
    			->data(['userId' => (string)$insertedUser->getInsertedId(),
    					'name' => $data['name']])
    			->build();
    }
}