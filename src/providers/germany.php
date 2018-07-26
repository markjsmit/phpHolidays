<?php namespace MJSHolidays\provider;

use MJSHolidays\lib\HolidayInfo;
use MJSHolidays\lib\HTML;
use MJSHolidays\lib\Http;


/**
 * @copyright Mark Smit 2018
 * @author Mark Smit
 * @link https://github.com/maxpower89/phpHolidays
 */
class Germany extends baseProvider
{
    private $url = "https://www.ferienwiki.de/feiertage/{year}/de";

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

    function parseContent($content)
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
                $result[]=$this->parseRow($resultRow);
            }
        }
        return $result;
    }



    /**
     * @param HTML[] $rowInfo
     */
    function parseRow($rowInfo){
        $infoObject=new HolidayInfo();
        $infoObject->name=$rowInfo[0]->getCleanText();
        $infoObject->isOfficial=true;
        $infoObject->isFreeDay=true;
        $infoObject->isNational=substr($rowInfo[2]->getCleanText(),0,4)=="Alle";
        $infoObject->date=$this->toDateTime($rowInfo[1]->getCleanText());

        return $infoObject;
    }

    function toDateTime($string){
        $string=substr($string,0,10);
        $split=explode(".",$string);


        return new \DateTime($split[2]."-".$split[1]."-".$split[0]);
    }
}

?>