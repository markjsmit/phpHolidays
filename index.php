<?php
use MJSHolidays\Holidays;
include_once("src/Holidays.php");
$holidays = new Holidays("netherlands");
print_r($holidays->getHolidaysPeriod(new DateTime("2018-01-01"), new DateTime("2018-12-31")));
?>