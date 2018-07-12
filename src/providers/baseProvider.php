<?php namespace MJSHolidays\provider;

/**
 * @copyright Mark Smit 2018
 * @author Mark Smit
 * @link https://github.com/maxpower89/phpDutchHolidays
 */
abstract class BaseProvider
{
    /**
     * @param \DateTime $from
     * @param \DateTime $until
     */
    abstract function getAllForPeriod($from,$until);
}

?>