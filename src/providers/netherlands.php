<?php namespace MJSHolidays\provider;

use MJSHolidays\lib\HolidayInfo;
use MJSHolidays\lib\HTML;
use MJSHolidays\lib\Http;


/**
 * @copyright Mark Smit 2018
 * @author Mark Smit
 * @link https://github.com/maxpower89/phpDutchHolidays
 */
class Netherlands extends baseProvider
{
    private $url = "https://www.feestdagen-nederland.nl/feestdagen-{year}.html";
    private $monthNames=["januari","februari","maart","april","mei","juni","juli","augustus","september","oktober","november","december"];


    /**
     * @param \DateTime $from
     * @param \DateTime $until
     */
    function getAllForPeriod($from,$until)
    {
        $result = [];
        $startYear = $from->format("Y");
        $endYear = $until->format("Y");
        for ($i = $startYear; $i <= $endYear; $i++) {
            $fetched = $this->fetchForYear($i);
            if (is_array($fetched)) {
                $result += $fetched;
            }
        }
        $result=array_filter($result,function($info) use ($from,$until){
            return $info->date>=$from&& $info->date<=$until;
        });
        return $result;
    }

    function fetchForYear($year)
    {
        $url = str_replace("{year}", $year, $this->url);
        $http = new Http();
        $content = $http->get($url);
        return $this->parseContent($content);
    }

    function parseContent($content)
    {
        $result=[];
        $result+=$this->getDays($content,"feestdagen_schema",true);
        $result+=$this->getDays($content,"feestdagen_schema_overig",false);
        return $result;
    }

    function getDays($content,$tableId,$official){
        $html = new HTML($content);
        $table = $html->getById($tableId);
        $rows = $table->getByTagName("tr");
        foreach ($rows as $key => $row) {
            $columns = $row->getByTagName("td");
            $resultRow=[];
            foreach ($columns as $column) {
                $resultRow[] = $column;
            }
            if(count($resultRow)){
                $result[]=$this->parseRow($resultRow,$official);
            }
        }
        return $result;
    }

    /**
     * @param HTML[] $rowInfo
     */
    function parseRow($rowInfo,$official=true){
        $infoObject=new HolidayInfo();

        $infoObject->isOfficial=$official;
        $infoObject->name=str_replace("\n","",$rowInfo[0]->getText());
        $infoObject->date=$this->toDateTime($rowInfo[1]->getText());

        if($rowInfo[0]->getByClass("vrije_feestdag")){
            $infoObject->isFreeDay=true;
        }else{
            $infoObject->isFreeDay=false;
        }

        return $infoObject;
    }

    function toDateTime($string){
        $split=explode(" ",$string);
        $split[1]=array_search($split[1],$this->monthNames)+1;
        return new \DateTime($split[2]."-".$split[1]."-".$split[0]);
    }
}

?>