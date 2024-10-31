<?php

/**
 * @package MyBookingsRESPlugin
 */

require_once plugin_dir_path(__FILE__) . 'DB.php';

class MyBookingsRES_ConnectMB 
{
    static function getUrl($cmd, $additionalParameter = [])
    {
        $db = new MyBookingsRES_DB();

        // get from settings
        $apiKey = $db->getSetting("apiKey"); // "API552f59ef1dd802.55823392";
        $websiteCfg = $db->getSetting("websiteConfigId"); // "10066";

        $url = "";
        $baseUrl = "https://www.my-bookings.cc";

        switch($cmd) {
            case "getCategoriesShortInfos" :
                $url = $baseUrl . "/everest/api/category/getShortInfo/?apikey={$apiKey}&website_cfg={$websiteCfg}";
                break;
            case "getCategoryData" :
                $url = $baseUrl . "/v2/extern/availability-check/category-data.php?apikey={$apiKey}&output=JSON";
                break;
            case "checkAvailability" :
                $url = $baseUrl . "/v2/extern/availability-check/check.php?apikey={$apiKey}&website_cfg={$websiteCfg}&output=JSON";
                break;
            case "getAttributeGroups" :
                $url = $baseUrl . "/everest/api/infos/attributeGroups/?apikey={$apiKey}&website_cfg={$websiteCfg}";
                break; 
            case "getDistanceFields" :
                $url = $baseUrl . "/everest/api/infos/distanceFields/?apikey={$apiKey}&website_cfg={$websiteCfg}";
                break; 
            case "getAreaInfos" :
                $url = $baseUrl . "/everest/api/infos/areas/?apikey={$apiKey}&website_cfg={$websiteCfg}";
                break; 
            case "getLocationsInfos" :
                $url = $baseUrl . "/everest/api/infos/locations/?apikey={$apiKey}&website_cfg={$websiteCfg}";
                break; 
        }

        if (count($additionalParameter) > 0) {
            $url .= "&" . http_build_query($additionalParameter);
        }

        return $url;
    }

    static function call($serverurl)
    {
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
        );
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $serverurl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);
        $error = 0;

        if(!$response && curl_errno($curl) > 0){
            $errmsg = curl_error($curl) . '" - Code: ' . curl_errno($curl);
            $error = 1;
            $code = null;
        }
        else {
            $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $errmsg = null;
        }

        curl_close($curl);

        $res = MyBookingsRES_ConnectMB::Result($error, $code, $response, $errmsg);
        return $res;
    }

    static function Result($error, $code, $response, $errmsg)
    {
        $r = new stdClass();
        $r->error = $error;
        $r->code = $code;
        $r->response = $response;
        $r->errmsg = $errmsg;
        return $r;
    }

}