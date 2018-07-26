# phpHolidays
A simple php library which makes it easy to fetch holidays from sources based on the country.  

## The following providers are supported:
1. Netherlands
2. Germany
3. UnitedKingdom

## sample of use
```php
<?php
use MJSHolidays\Holidays;
include_once("src/Holidays.php");
$holidays = new Holidays("unitedKingdom");
print_r($holidays->getHolidaysPeriod(new DateTime("2018-01-01"), new DateTime("2018-12-31")));
?>
```
