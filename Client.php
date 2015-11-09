<?php
/**
 * Description of client
 *
 * @author wds
 */
namespace wdst\miniclientapi;

use wdst\miniclientapi\Exception;

class Client {

    public $url = null;
    
    public function __construct() 
    {
        if(empty($this->url)){
            throw new \Exception('api url not valid', Exception::INTERNAL_ERROR);
        }
    }
    
    public function call($method, $arguments, $url)
    {
        $method = str_replace( '_', '.', $method );

        $post = [
            "jsonrpc" => "2.0",
            "id" => $this->getID,
            "method" => $method,
            "params" => $arguments
        ];
        
        $curl = curl_init($url);

        curl_setopt ($curl, CURLOPT_POST, 1);
        curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($curl, CURLOPT_POSTFIELDS, json_encode($post));
        $jsonResponse = curl_exec ($curl);
        //error_log(print_r($jsonResponse,1));
        curl_close ($curl);

        $response = json_decode($jsonResponse);
        
        if (property_exists($response, 'error')) {
            throw new Exception($response->error->message, $response->error->code);
        } else if (property_exists($response, 'result')) {
            return $response->result;
        } else {
            throw new Exception('Invalid JSON-RPC response', Exception::INTERNAL_ERROR);
        }
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
