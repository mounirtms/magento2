<?php

namespace Mustafa\YalidineDelivery\Model\Api;

use Mustafa\YalidineDelivery\Helper\Data;
use Mustafa\YalidineDelivery\Logger\DebugLogger;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use Mustafa\YalidineDelivery\Model\Cache\Api as ApiCache;

class Connector
{
    protected $client;
    protected $helper;
    protected $debugLogger;
    protected $url = 'https://api.yalidine.app/v1/';
    protected $accessToken;
    protected $apiCache;
    protected $failedAuthentication = false;
    public $isError = false;
    public $errorId = null;
    public $errorCode = null;
    public $errorMessage = null;

    public function __construct(
        ApiCache $apiCache,
        Client $client,
        Data $helper,
        DebugLogger $debugLogger
    ) {
        $this->apiCache = $apiCache;
        $this->client = $client;
        $this->helper = $helper;
        $this->debugLogger = $debugLogger;

    }
      
    public function testAuthenticate($userId, $apiKey, $storeId)
    {
        if (!isset($userId, $apiKey)) {
            return false;
        }

        $response = $this->request('GET','wilayas',[
            'userId' => trim($userId),
            'apiKey' => trim($apiKey)
        ]);
        
        if (!$response) {
            return false;
        }else {
            $this->helper->updateConfigData('user',$userId, 'stores', $storeId);
            $this->helper->updateConfigData('key',$apiKey, 'stores', $storeId);
        
            $data = json_decode($response->getBody()->getContents(), true);
            $this->debugLogger->info('CONNECTOR API response decoded', ['response' => $data]);
            return $data;
        }
    }
    
    public function request($method, $endpoint, array $params)
    {
        // Assume there's always an error, until this method manages to return correctly and set the boolean to true.
        $this->isError = true;
        $this->errorId = null;
        $this->errorCode = null;
        $this->errorMessage = null;
        $options = [
            RequestOptions::HEADERS => [
                'X-API-ID' => $params['userId'],
                'X-API-TOKEN' => $params['apiKey'],
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ];


        try {
            $this->debugLogger->info('CONNECTOR API request', ['method' => $method, 'url' => $this->url, $endpoint, $options]);
            /** @var Response $response */
            $response = $this->client->{$method}($this->url . $endpoint, $options);
            $this->debugLogger->info('CONNECTOR API response raw', ['response' => $response->getBody()->getContents()]);
            $response->getBody()->rewind();
        } catch (ClientException $e) {
            $this->isError = true;
            $this->errorCode = $e->getCode();
            $this->errorMessage = $e->getResponse()->getBody()->getContents();
            $this->debugLogger->info('CONNECTOR API request failed, client exception', ['code' => $this->errorCode, 'message' => $this->errorMessage]);
            return false;
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            $this->isError = true;
            $this->errorCode = $e->getCode();
            $this->errorMessage = $e->getResponse()->getBody()->getContents();
            $this->debugLogger->info('CONNECTOR API request failed, server exception', ['code' => $this->errorCode, 'message' => $this->errorMessage]);

            return false;
        } catch (\Exception $e) {
            $this->isError = true;
            $this->errorCode = $e->getCode();
            $this->errorMessage = __('Connection to API failed.');
            $this->debugLogger->info('CONNECTOR API Connection to API failed.', ['code' => $this->errorCode, 'message' => $this->errorMessage]);

            return false;
        }

        if (isset($response) && !is_bool($response) && $response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $this->debugLogger->info('CONNECTOR API request successful');
            $this->isError = false;
            return $response;
        }

        $this->debugLogger->info('CONNECTOR API request failed');
        return false;
    }
}