<?php

/**
 * @package MyBookingsRESPlugin
 */

require_once plugin_dir_path(__FILE__) . 'backend/connectMB.php';
require_once plugin_dir_path(__FILE__) . 'backend/DB.php';

/**
 * This class contains all ajax functions for the wordpress frontend.
 * Class MyBookingsRESPluginFrontentHttp
 */
class MyBookingsRESPluginFrontentHttp
{
    public function getCategoriesShortInfosFrontent()
    {
        $db = new MyBookingsRES_DB();

        MyBookingsRESPluginHttpResult::JSONOutput(0, $db->getShortCategoriesInfo());
    }

    public function getFilteredCategories()
    {
        $db = new MyBookingsRES_DB();

        $attributeIds = $_POST['attributeIds'];
        $locationIds = $_POST['locationIds'];

        MyBookingsRESPluginHttpResult::JSONOutput(0, $db->getFilteredCategories($attributeIds, $locationIds));
    }


    public function getListEntryPackage()
    {
        // sleep(1);
        $MyBookingsRESPlugin = new MyBookingsRESPlugin();

        $categories = $_POST["categories"];

        $data = "";

        foreach($categories as $category) {
            $data .= $MyBookingsRESPlugin->getListEntry($category);
        }

        MyBookingsRESPluginHttpResult::JSONOutput(0, $data, $categories);
    }

    public function checkAvailability()
    {
        $categories = $_POST["categories"];

        $db = new MyBookingsRES_DB();

        $hideUnavailableCategories = $db->getSetting('hideUnavailableCategories');

        $from = MyBookingsRESPluginHelper::checkAndGetPostVal("from");
        $to = MyBookingsRESPluginHelper::checkAndGetPostVal("to");

        $params = [
            "from" =>                       $from,
            "to" =>                         $to,
            "people" =>                     MyBookingsRESPluginHelper::checkAndGetPostVal("people"),
            "onlythiscat" =>                implode(",", $categories),
            "loadPriceDetails" =>           1,
            "loadPriceDetailInfos" =>       1,
            "loadRestrictionsDetails" =>    1,
            "area" =>       	            MyBookingsRESPluginHelper::checkAndGetPostVal("area"),
            "location" =>                   MyBookingsRESPluginHelper::checkAndGetPostVal("location")

        ];

        $url = MyBookingsRES_ConnectMB::getUrl("checkAvailability", $params);
        $req = MyBookingsRES_ConnectMB::call($url);

        $res = null;

        if ($req->error == 0) {
           // print_r($req->response);
            //$data = json_decode($req->response);
            $res = json_decode($req->response);

            if (count($res) > 0) {
                $res = $res[0];
            }
        }

        $response = [];
        $response['data'] = $res;
        $response['hideUnavailableCategories'] = $hideUnavailableCategories;

        MyBookingsRESPluginHttpResult::JSONOutput(0, $response, $categories);


    }

    public function getApartmentURL()
    {
        $db = new MyBookingsRES_DB();

        $resultPageId = $db->getSetting('resultPageId');

        if (!$resultPageId) {
            MyBookingsRESPluginHttpResult::JSONOutput(0, null);
        }

        $url = get_permalink($resultPageId);

        MyBookingsRESPluginHttpResult::JSONOutput(0, $url);
    }

    public function getAttributesForSearch()
    {
        $db = new MyBookingsRES_DB();

        $data = $db->getAttributesForSearch();

        MyBookingsRESPluginHttpResult::JSONOutput(0, $data);
    }

    public function getCategoriesWithAttributes()
    {
        $db = new MyBookingsRES_DB();

        $attributesForFilter = $_POST["attributesForFilter"];

        /*
        $attributesForFilter = array_map(
            function($value) { return (int)$value; },
            $attributesForFilter
        );
*/
        MyBookingsRESPluginHttpResult::JSONOutput(0, $db->getCategoriesWithAttributes($attributesForFilter));
    }

    public function getResultPageUrl()
    {
        $db = new MyBookingsRES_DB();

        $currentLanguage = pll_current_language();

        $resultPageTranslations = pll_get_post_translations($db->getResultPageId());
        $resultPageUrl = get_permalink($resultPageTranslations[$currentLanguage]);

        MyBookingsRESPluginHttpResult::JSONOutput(0, $resultPageUrl);
    }

    public function getResultPageUrlWithParameters()
    {
        $db = new MyBookingsRES_DB();

        $currentLanguage = pll_current_language();

        $resultPageTranslations = pll_get_post_translations($db->getResultPageId());
        $resultPageUrl = get_permalink($resultPageTranslations[$currentLanguage]);

        $existingParameters = $_POST['existingParameters'];

        foreach ($existingParameters as $parameterKey => $parameterValue) {

            $index = array_search($parameterKey, array_keys($existingParameters));

            if ($index === 0) {
                $resultPageUrl .= '?' . $parameterKey . '=' . $parameterValue;
            } else {
                $resultPageUrl .= '&' . $parameterKey . '=' . $parameterValue;
            }
        }

        MyBookingsRESPluginHttpResult::JSONOutput(0, $resultPageUrl);
    }
}