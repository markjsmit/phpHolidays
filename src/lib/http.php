<?php

namespace MJSHolidays\lib;


/**
 * @copyright Mark Smit 2018
 * @author Mark Smit
 * @link https://github.com/maxpower89/phpHolidays
 */
class Http
{
    function get($url, $headers = false)
    {
        global $is_in_debug;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");

        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        };


        $server_output = curl_exec($ch);
        curl_close($ch);
        return $server_output;
    }


}