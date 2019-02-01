<?php
namespace MarcosPer\Copyscape\Models;

/**
 * @property  string viewUrl Report view url
 * @property  int words Words of request
 * @property  float cost Credits of request
 * @property  array results Results of report
 * @author    Marcos Per <marcosperg@gmail.com>
 * @copyright Copyright (c) 2019
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 */
class  Report{

    function __construct($xml)
    {
        $this->results = array();
        $this->viewUrl = (string) $xml->allviewurl;
        $this->words = (int) $xml->querywords;
        $this->cost = (float) $xml->cost;

        if($xml->count > 0){
            foreach ($xml->result as $result){
                array_push($this->results,new Result($result));
            }
        }
    }

    /*
     * Check if content is original
     */
    public function isOriginal(){
        return count($this->results) == 0;
    }

    /*
     * Get url of report
     */
    public function getResultUrl(){
        return $this->viewUrl;
    }

    /*
     * Get words of request
     */
    public function getWordsCount(){
        return $this->words;
    }

    /*
     * Get matches results
     */
    public function getResults(){
        $this->results;
    }

}