<?php
namespace Marcosper\Copyscape\Models;


/**
 * @property string sourceUrl
 * @property string reportUrl
 * @property string title
 * @property int minWords
 * @property boolean fullCompare
 * @property int urlWords
 * @property int wordsMatched
 * @property int percentMatched
 *
 *
 * @author     Marcos Per <marcosperg@gmail.com>
 * @copyright  Copyright (c) 2018
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
*/
class Result{

    function __construct($xml)
    {
        $this->sourceUrl = (string) $xml->url;
        $this->title = (string) $xml->title;
        $this->reportUrl = (string) $xml->viewurl;
        $this->minWords = (int) $xml->minwordsmatched;
        $this->fullCompare = false;

        // Unhandled full compare error if(isset($xml->urlerror)){}

        //¿Has full compare?
        if(isset($xml->urlwords)){
            $this->fullCompare = true;
            $this->urlWords = (int) $xml->urlwords;
            $this->wordsMatched = (int) $xml->wordsmatched;
            $this->percentMatched = (int) $xml->percentmatched;
        }
    }

    public function getSourceUrl(){
        return $this->sourceUrl;
    }

    public function getReportUrl(){
        return $this->reportUrl;
    }

    public function getTitle(){
        return $this->title;
    }

    public function getMinWords(){
        return $this->minWords;
    }

    public function isFullCompare(){
        return $this->fullCompare;
    }

    public function getUrlWords(){
        if(!$this->isFullCompare()) return null;
        return $this->urlWords;
    }

    public function getWordsMatched(){
        if(!$this->isFullCompare()) return null;
        return $this->wordsMatched;
    }

    public function getPercentMatched(){
        if(!$this->isFullCompare()) return null;
        return $this->percentMatched;
    }

}