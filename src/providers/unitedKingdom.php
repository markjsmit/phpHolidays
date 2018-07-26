<?php namespace MJSHolidays\provider;

use MJSHolidays\lib\HolidayInfo;
use MJSHolidays\lib\HTML;
use MJSHolidays\lib\Http;


/**
 * @copyright Mark Smit 2018
 * @author Mark Smit
 * @link https://github.com/maxpower89/phpHolidays
 */
class UnitedKingdom extends baseProvider
{
    private $url = "https://www.officeholidays.com/countries/united_kingdom/{year}.php";
    private $monthNames=["January","February","March","April","May","June","July","August","September","October","November","December"];
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
        return $this->parseContent($content,$year);
    }

    function parseContent($content,$year)
    {
        $result=[];
        $html = new HTML($content);
        $table = $html->getByTagName("tbody")[0];
        $rows = $table->getByTagName("tr");
        foreach ($rows as $key => $row) {
            $columns = $row->getByTagName("td");
            $resultRow=[];
            foreach ($columns as $column) {
                $resultRow[] = $column;
            }
            if(count($resultRow)){
                $result[]=$this->parseRow($resultRow,$row,$year);
            }
        }
        return $result;
    }



    /**
     * @param HTML[] $rowInfo
     * @param HTML $rowHtml
     */
    function parseRow($rowInfo,$rowHtml,$year){
        $infoObject=new HolidayInfo();
        $cls=$rowHtml->getAttribute("class");
        $infoObject->name=$rowInfo[2]->getCleanText();
        $infoObject->isOfficial=$cls=="holiday"||$cls=="regional";
        $infoObject->isFreeDay=$infoObject->isOfficial;
        $infoObject->isNational=$cls=="holiday";

        $infoObject->date=$this->toDateTime($rowInfo[1]->getCleanText(),$year);

        return $infoObject;
    }

    function toDateTime($string,$year){
        $string=substr($string,0,10);
        $split=explode(" ",$string);
        $split[0]=array_search($split[0],$this->monthNames)+1;

        return new \DateTime($year."-".$split[0]."-".$split[1]);
    }
}

?>