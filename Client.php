<?php
/**
 * Description of client
 *
 * @author wds
 */
namespace wdst\miniapiclient;

use wdst\miniapiclient\Exception;

class Client extends \yii\Base\Object{

    public $config;
    public $url = null;
    
    public function __construct($url = null) 
    {
        $this->url = $url;
    }
    
    public function call($method, $arguments)
    {
        if(empty($this->url)){
            throw new \Exception('api url not valid2', Exception::INTERNAL_ERROR);
        }
        $method = str_replace( '_', '.', $method );

        $post = [
            "jsonrpc" => "2.0",
            "id" => $this->getID(),
            "method" => $method,
            "params" => empty($arguments[0])?:$arguments[0]
        ];
        
        $curl = curl_init($this->url);

        curl_setopt ($curl, CURLOPT_POST, 1);
        curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($curl, CURLOPT_POSTFIELDS, json_encode($post));
        $jsonResponse = curl_exec ($curl);

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
        return $this->call($name, $arguments);
    }
    
    public function getID()
    {
        return md5(microtime());
    }
}
