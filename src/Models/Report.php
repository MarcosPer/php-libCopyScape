<?php
namespace Marcosper\Copyscape\Models;

/**
 * @property  string viewUrl Report view url
 * @property  int words Words of request
 * @property  float cost Credits of request
 * @property  array results Results of report
 * @property int percentage Percent matched of request
 * @author    Marcos Per <marcosperg@gmail.com>
 * @copyright Copyright (c) 2019
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 */
class  Report{

    function __construct($xml,$c = 0)
    {
        $this->results = array();
        $this->viewUrl = (string) $xml->allviewurl;
        $this->words = (int) $xml->querywords;
        $this->cost = (float) $xml->cost;
        $this->percentage = 0;

        if($xml->count > 0){
            foreach ($xml->result as $xmlResult){
                $result = new Result($xmlResult);

                //Find the highest matchpercent
                if($result->getPercentMatched() !== null && $result->getPercentMatched() > $this->percentage) $this->percentage = $result->getPercentMatched();

                array_push($this->results,$result);
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
     * Get the highest match percent
     */
    public function getMatchedPercent(){
        return $this->percentage;
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