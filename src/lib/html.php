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
        $this->element=new DOMDocument();
        if($asXML){
            $this->element->loadXML($html);
        }else {
            $this->element->loadHTML($html);
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
    function getByClass($class,$createHtmlObject=true)
    {
        return $this->getByAttribute("class", $class,$createHtmlObject);
    }

    /**
     * @param string $tagName
     * @return HTML[]
     */
    function getByTagName($tagName,$createHtmlObject=true)
    {
        return $this->getByFilter("//" . $tagName,$createHtmlObject);
    }

    /**
     * @param string $attr
     * @param string $value
     * @return HTML[]
     */

    function getByAttribute($attr, $value,$createHtmlObject=true)
    {
        $result = $this->getByFilter("//*[contains(concat(' ', normalize-space(@" . $attr . "), ' '), ' $value ')]",$createHtmlObject);
        if (count($result)) {
            return $result;
        }
        return false;
    }

    /**
     * @param string $filter
     * @return HTML[]
     */
    function getByFilter($filter,$createHtmlObject=true)
    {
        $finder = new DomXPath($this->element);
        $queryResult = $finder->query($filter);
        $result = [];
        if ($queryResult) {
            foreach ($queryResult as $elem) {
                if($createHtmlObject) {
                    $result[] = new HTML($this->element->saveHtml($elem), true);
                }else{
                    $result[]=$this->element->saveHtml($elem);
                }
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