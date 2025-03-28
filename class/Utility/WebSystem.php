<?php
namespace Utility;

use Requests\Request;

class WebSystem {

    public static function redirect($url, $method='header') {
        if (empty($url)) $url = '/';
        if ($method == 'header') {
            header("Location: ".$url);
            exit(0);
        } else if ($method == 'js') {
            echo "window.location = '".$url."';";
        } else {
            echo "<meta http-equiv='location' content='".$url."' />";
        }
    }

    public static function refresh($timeout, $method='header', $url="") {
        $content = $timeout;
        if (!empty($url)) $content .= ";url=".$url;
        if ($method == 'header') {
            header("Refresh: ".$content);
            exit();
        } else {
            echo "<meta http-equiv='refresh' content='".$content."' />";
        }
    }

    public static function writeLog($filename, $msg) {
        $logmsg = date("Y-m-d_H:i:s_O")." ".$msg."\r\n";
        file_put_contents($filename, $logmsg, FILE_APPEND);
    }
    
    public static function path($url, $echo = true, $slash = true) {
		$baseUrl = Request::get()->baseUrl();
		$path = $baseUrl.(($slash)?'/':'').$url;
        if ($echo) {
            echo $path;
        }
        return $path;
    }

    public static function displayDate($date, $format=\DateTime::RFC850, $timezone="", $timeZoneFrom='UTC') {
    	if (strtotime($date) <= 0) return '';
        $tz = "Asia/Hong_Kong";
        if (!empty($timezone)) {
            $tz = $timezone;
        } else if (!empty(cfg('global')['timezone'])) {
            $tz = cfg('global')['timezone'];
        }
        $time = new \DateTime($date, new \DateTimeZone($timeZoneFrom));
        $time->setTimezone(new \DateTimeZone($tz));
        return $time->format($format);
    }

    public static function displayPrice($price, $dp="2", $sign="$") {
        return $sign.number_format($price, $dp);
    }    

    public static function generateStringHash($n=8, $mode="ALL", $format="A"){       
        
        if($mode=="ALL"){
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        } elseif($mode=="HEX"){
            $characters = '0123456789abcdefABCDEF';
        } elseif($mode=="LETTER"){
            $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        } elseif($mode=="DEC"){
            $characters = '0123456789';
        }        
        
        $randomString = '';
        
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        
        if($format=="U"){
            return strtoupper($randomString);
        } else if($format=="L"){
            return strtolower($randomString);
        } else {
            return $randomString;
        }

    }
}