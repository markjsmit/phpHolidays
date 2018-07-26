<?php namespace MJSHolidays;

use MJSHolidays\lib\ProviderLoader;
use MJSHolidays\provider\BaseProvider;

define("DH_WEBSITE", "https://www.feestdagen-nederland.nl/feestdagen-{{year}}.html");

/**
 * @copyright Mark Smit 2018
 * @author Mark Smit
 * @link https://github.com/maxpower89/phpHolidays
 */
class Holidays
{
    /**
     * @var BaseProvider
     */
    private $provider;

    private static $loaded=false;

    function __construct($provider)
    {
        $this->load();

        if(is_string($provider)) {
            $providerLoader = new ProviderLoader();
            $this->provider = $providerLoader->load($provider);
        }else{
            $this->provider=$provider;
        }
    }

    private function load(){
        if(!self::$loaded){
            $dir = __DIR__ . "/lib/";
            if ($handle = opendir($dir)) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != ".." && !is_dir($dir . "/" . $entry)) {
                        include_once($dir . $entry);
                    }
                }
                closedir($handle);
            }
        }
        self::$loaded=true;
    }


    /**
     * @param \DateTime $from
     * @param \DateTime $until
     */
    function getHolidaysPeriod($from,$until)
    {
        return $this->provider->getAllForPeriod($from,$until);
    }
}

