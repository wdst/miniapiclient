<?php
/**
 * Description of client
 *
 * @author wds
 */
namespace wdst\miniapiclient;

use wdst\miniapiclient\Exception;

class Client {

    public $url = "http://jsonrpc/tests/testserver.php";
    
    public function __construct() 
    {
        if(empty($this->url)){
            throw new \Exception('api url not valid', Exception::INTERNAL_ERROR);
        }
        
        return $this;
    }
    
    public function call($method, $arguments, $url)
    {
                if(empty($this->url)){
            throw new \Exception('api url not valid', Exception::INTERNAL_ERROR);
        }
        $method = str_replace( '_', '.', $method );

        $post = [
            "jsonrpc" => "2.0",
            "id" => $this->getID(),
            "method" => $method,
            "params" => empty($arguments[0])?:$arguments[0]
        ];
        
        $curl = curl_init($url);

        curl_setopt ($curl, CURLOPT_POST, 1);
        curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($curl, CURLOPT_POSTFIELDS, json_encode($post));
        $jsonResponse = curl_exec ($curl);
        error_log(print_r($arguments,1));
        curl_close ($curl);

        $response = json_decode($jsonResponse);
        
        return $response;
        /*
        if (property_exists($response, 'error')) {
            throw new Exception($response->error->message, $response->error->code);
        } else if (property_exists($response, 'result')) {
            return $response->result;
        } else {
            throw new Exception('Invalid JSON-RPC response', Exception::INTERNAL_ERROR);
        }*/
    }
    
    public function __call($name, $arguments)
    {
        return $this->call($name, $arguments, $this->url);
    }
    
    public function getID()
    {
        return md5(microtime());
    }
}
