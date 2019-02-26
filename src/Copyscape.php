<?php

namespace MarcosPer\Copyscape;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use MarcosPer\Copyscape\Exceptions\ApiException;
use MarcosPer\Copyscape\Exceptions\InvalidApiException;
use MarcosPer\Copyscape\Exceptions\NoCreditsException;
use MarcosPer\Copyscape\Models\Report;
use SimpleXMLElement;

/**
 * @property   Client client
 * @property   bool debug
 * @property   string user
 * @property   string key
 * @property   array ignoredDomains
 * @property   string encode
 * @author     Marcos Per <marcosperg@gmail.com>
 * @copyright  Copyright (c) 2019
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

 class Copyscape{

    public function __construct(){
        $this->debug = false;
        $this->encode = 'UTF-8';
        $this->client = new Client(['verify' => false, 'base_uri' => 'http://www.copyscape.com/api/']);
        $this->ignoredDomains = array();
    }

     /**
      * @param string $certificate Optional path of custom CA bundle used to verification
      */
    public function forceSSL($certificate = null){

        $this->client = new Client(['verify' => $certificate == null ? true : $certificate, 'base_uri' => 'https://www.copyscape.com/api/']);
    }

     /**
      * @param string $username CopyScape username
      * @param string $api_key CopyScape api key
      */
    public function addCredentials($username,$api_key){
        $this->user = $username;
        $this->key = $api_key;
    }

     /**
      * @param boolean $debug Set debug mode
      */
     public function setDebug($debug){
         $this->user = $debug;
     }

     /**
      * @param string $domain Add domain to ignore list when do internet search
      */
    public function ignoreDomain($domain){
        array_push($this->ignoredDomains,$domain);
    }

    /*
     * @param string $encode Change encode
     */
    public function changeEncode($encode){
        $this->encode = $encode;
    }

     /**
      * @return float The remaining credits of account
      * @throws ApiException
      * @throws InvalidApiException
      * @throws NoCreditsException
      */
     public function getCredits(){

        try{
            $response = $this->client->request('GET','',['query' => $this->getApiParams(['o' => 'balance'])]);
            $xml = simplexml_load_string($response->getBody());
            $this->checkApiErrors($xml);
            return floatval($xml->value);
        }catch (GuzzleException $exception){
            throw new ApiException($exception->getMessage());
        }

    }

     /**
      * @return int Today remaining searches
      * @throws ApiException
      * @throws InvalidApiException
      * @throws NoCreditsException
      */
     public function getRemainingSearches(){
        try{
            $response = $this->client->request('GET','',['query' => $this->getApiParams(['o' => 'balance'])]);
            $xml = simplexml_load_string($response->getBody());
            $this->checkApiErrors($xml);
            return intval($xml->today);
        }catch (GuzzleException $exception){
            throw new ApiException($exception->getMessage());
        }
    }

     /**
      * @param string $text Text to search on internet
      * @param int $fullComparisons 0-10 Full comparisons
      * @return Report Copyscape search report
      * @throws ApiException
      * @throws InvalidApiException
      * @throws NoCreditsException
      */
     public function searchInternetText($text,$fullComparisons = 0){
        try{
            $response = $this->client->request('GET','',['query' => $this->getApiParams(['o' => 'csearch', 'c' => $fullComparisons, 'e' => $this->encode]) , 'body' => $text]);
            $xml = simplexml_load_string($response->getBody());
            $this->checkApiErrors($xml);

            return new Report($xml);
        }catch (GuzzleException $exception){
            throw new ApiException($exception->getMessage());
        }
    }


     /**
      * @param null $params
      * @return array Get array with needed parameters to do api request
      */
    private function  getApiParams($params = null){
        $aux = array();

        $aux['u'] = $this->user;
        $aux['k'] = $this->key;

        $aux['f'] = 'xml';

        if(count($this->ignoredDomains) > 0) $aux['i'] = implode(',',$this->ignoredDomains);
        if($this->debug) $aux['x'] = 1;

        if($params !== null) $aux = array_merge($params,$aux);

        return $aux;
    }

     /**
      * @param SimpleXMLElement $xml
      * @throws ApiException
      * @throws InvalidApiException
      * @throws NoCreditsException
      */
    private function checkApiErrors($xml){
        if(isset($xml->error)){
            switch ($xml->error){
                case 'Username or API key not correct':
                    throw new InvalidApiException('Username or API key not correct');
                case 'No credits remaining':
                    throw new NoCreditsException('No credits remaining');
                default:
                    throw new ApiException($xml->error);
            }
        }
    }
 }