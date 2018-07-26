<?php

namespace MJSHolidays\lib;


/**
 * @copyright Mark Smit 2018
 * @author Mark Smit
 * @link https://github.com/maxpower89/phpHolidays
 */
class ProviderLoader
{
    function load($name)
    {
        include_once(__DIR__ . "/../providers/baseProvider.php");
        include_once(__DIR__ . "/../providers/" . $name . ".php");
        $className = "MJSHolidays\\provider\\".ucfirst($name);
        return new $className();
    }
}