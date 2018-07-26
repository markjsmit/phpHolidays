<?php

namespace MJSHolidays\lib;

use DOMDocument;
use DOMXPath;


/**
 * @copyright Mark Smit 2018
 * @author Mark Smit
 * @link https://github.com/maxpower89/phpHolidays
 */
class HTML
{
    /**
     * @var bool DOMDocument
     */
    private $element;

    function __construct($html,$asXML=false)
    {
        if($asXML){
            $this->element = @DOMDocument::loadXML($html);
        }else {
            $this->element = @DOMDocument::loadHTML($html);
        }
    }


    /**
     * @param string $id
     * @return HTML
     */
    function getById($id)
    {
        $elem = $this->getByAttribute("id", $id);
        if (count($elem)) {
            return $elem[0];
        }
        return false;
    }


    /**
     * @param string $class
     * @return HTML[]
     */
    function getByClass($class)
    {
        return $this->getByAttribute("class", $class);
    }

    /**
     * @param string $tagName
     * @return HTML[]
     */
    function getByTagName($tagName)
    {
        return $this->getByFilter("//" . $tagName);
    }

    /**
     * @param string $attr
     * @param string $value
     * @return HTML[]
     */

    function getByAttribute($attr, $value)
    {
        $result = $this->getByFilter("//*[contains(concat(' ', normalize-space(@" . $attr . "), ' '), ' $value ')]");
        if (count($result)) {
            return $result;
        }
        return false;
    }

    /**
     * @param string $filter
     * @return HTML[]
     */
    function getByFilter($filter)
    {
        $finder = new DomXPath($this->element);
        $queryResult = $finder->query($filter);
        $result = [];
        if ($queryResult) {
            foreach ($queryResult as $elem) {
                $result[] = new HTML($this->element->saveHtml($elem),true);
            }
        }
        return $result;
    }

    /**
     * @param string $filter
     * @return string
     */
    function outerHTML()
    {
        if ($this->element) {
            return $this->element->saveHtml();
        }
    }

    function getText(){
        return $this->element->textContent;
    }

    function getCleanText(){
        return trim(str_replace("\n","",$this->getText()));
    }

    function getAttribute($name){
        return $this->element->childNodes[0]->getAttribute($name);

    }
}

?>