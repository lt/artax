<?php

namespace WebApp\Resources;

use Artax\Http\Request,
    Artax\Http\Response;


class Index {
    
    private $request;
    private $response;
    
    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
    }
    
    public function get() {
        $body = 'Index::get';
        $this->response->setBody($body);
        $this->response->setStatusCode(200);
        $this->response->setStatusDescription('OK');
        $this->response->send();
        
        return $this->response;
    }

}