<?php

namespace ATWENTYFIVE;

use Zend\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

class ResponseBuilder
{
    private $Headers = [];
    private $Status = 200;
    private $Data = [];

    /**
     * Constructor's decorator
     * @return ResponseInterface
     */
    public function build() : ResponseInterface
    {
        return new JsonResponse(
            $this->Data,
            $this->Status,
            $this->Headers,
            JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * Sets up the response's type to error
     * @return ResponseBuilder
     */
    public function error() : self
    {
        $this->Data['type'] = "error";
        return $this;
    }

    /**
     * Sets up the response's type to success
     * @return ResponseBuilder
     */
    public function success() : self
    {
        $this->Data['type'] = "success";
        return $this;
    }

    /**
     * Sets up the response's message
     * @param string $message
     * @return ResponseBuilder
     */
    public function message(string $message) : self
    {
        $this->Data['message'] = $message;
        return $this;
    }

    /**
     * Sets up the response's code
     * @param int $code
     * @return ResponseBuilder
     */
    public function code(int $code) : self
    {
        $this->Data['code'] = $code;
        return $this;
    }

    /**
     * Sets up the response's data
     * @param $data
     * @return ResponseBuilder
     */
    public function data($data) : self
    {
        $this->Data['data'] = $data;
        return $this;
    }
    
    /**
     * Sets up the http code
     * @param int $code
     * @return ResponseBuilder
     */
    public function status(int $code) : self
    {
        $this->Status = $code;
        return $this;
    }
}
