<?php

/**
 * @package MyBookingsRESPlugin
 */

require_once plugin_dir_path(__FILE__) . 'backend/syncCategoryData.php';

/**
 * This class contains all ajax functions for the wordpress backend.
 * Class MyBookingsRESPluginBackendHttp
 */
class MyBookingsRESPluginBackendHttp
{
    public function getCategoriesShortInfos()
    {
        if (!is_admin()) {
            wp_die();
        }

        $myBookingsRES_SyncCategoryData = new MyBookingsRES_SyncCategoryData();
        $data = $myBookingsRES_SyncCategoryData->getShortInfos();
        
        $response = "";

        if ($data->error === 0) {
            $d = json_decode($data->response);
            $response = $d->data;            
        }

        MyBookingsRESPluginHttpResult::JSONOutput($data->error, $response);
    }

    public function syncCategories()
    {
        if (!is_admin()) {
            wp_die();
        }

        $categories = $_POST['categories'];

        $myBookingsRES_SyncCategoryData = new MyBookingsRES_SyncCategoryData();

        $categoryDetailsReq = $myBookingsRES_SyncCategoryData->sync($categories);

        MyBookingsRESPluginHttpResult::JSONOutput($categoryDetailsReq->error, $categoryDetailsReq->data);

    }

    public function createCategoryPages()
    {
        if (!is_admin()) {
            wp_die();
        }

        $db = new MyBookingsRES_DB();

        $db->createCategoryPages();

        MyBookingsRESPluginHttpResult::JSONOutput(0, null);
    }

    public function syncAreas()
    {
        if (!is_admin()) {
            wp_die();
        }

        $dbSync = new MyBookingsRES_DBSync();

        $dbSync->addOrUpdateAreas();

        MyBookingsRESPluginHttpResult::JSONOutput(0, null);
    }

    public function syncLocations()
    {
        if (!is_admin()) {
            wp_die();
        }

        $dbSync = new MyBookingsRES_DBSync();

        $dbSync->addOrUpdateLocations();

        MyBookingsRESPluginHttpResult::JSONOutput(0, null);
    }

    public function removeCategoryPages()
    {
        if (!is_admin()) {
            wp_die();
        }

        $db = new MyBookingsRES_DB();

        $db->removeCategoryPages();

        MyBookingsRESPluginHttpResult::JSONOutput(0, null);
    }

    public function createResultPages()
    {
        if (!is_admin()) {
            wp_die();
        }

        $dbSync = new MyBookingsRES_DBSync();

        $dbSync->createResultPages();

        MyBookingsRESPluginHttpResult::JSONOutput(0, null);
    }

    public function createPaymentReturnPages()
    {
        if (!is_admin()) {
            wp_die();
        }

        $dbSync = new MyBookingsRES_DBSync();

        $dbSync->createPaymentReturnPages();

        MyBookingsRESPluginHttpResult::JSONOutput(0, null);
    }


    public function saveSettings()
    {
        $apiKey = $_POST['apiKey'];
        $websiteConfigId = $_POST['websiteConfigId'];
        $accommodationType = $_POST['accommodationType'];
        $googleAPIKey = $_POST['googleAPIKey'];
        $color1 = $_POST['color1'];
        $color2 = $_POST['color2'];
        $color3 = $_POST['color3'];
        $color4 = $_POST['color4'];
        $color_bglist = $_POST['color_bglist'];
        $custom_css = $_POST['custom_css'];
        $showAreas = $_POST['showAreas'];
        $hideChildrenAges = $_POST['hideChildrenAges'];
        $sortCategories = $_POST['sortCategories'];
        $showFilter = $_POST['showFilter'];
        $hideUnavailableCategories = $_POST['hideUnavailableCategories'];

        $MyBookingsRES_DB = new MyBookingsRES_DB();

        $MyBookingsRES_DB->saveSettings($apiKey, $websiteConfigId, $accommodationType, $googleAPIKey, $color1, $color2, $color3, $color4, $color_bglist, $custom_css, $showAreas, $hideChildrenAges, $sortCategories, $showFilter, $hideUnavailableCategories);

        MyBookingsRESPluginHttpResult::JSONOutput(0, null);
    }

    public function loadAreaFilterDataForAttributes()
    {
        $areaId = $_POST['areaId'];

        if (!$areaId) {
            MyBookingsRESPluginHttpResult::JSONOutput(1, []);
        }

        $db = new MyBookingsRES_DB();

        $attributes = $db->getAttributesForArea($areaId);

        MyBookingsRESPluginHttpResult::JSONOutput(0, $attributes);
    }

    public function saveAreaFilterDataForAttributes()
    {
        $areaId = $_POST['areaId'];
        $attributeIds = $_POST['attributeIds'] ? $_POST['attributeIds'] : [];
        
        if (!$areaId) {
            MyBookingsRESPluginHttpResult::JSONOutput(1, null);
        }
        
        $db = new MyBookingsRES_DB();

        foreach ($attributeIds as $attributeId) {
            $db->saveFilterSettingForAttributes($areaId, $attributeId);
        }

        MyBookingsRESPluginHttpResult::JSONOutput(0, null);
    }

    public function loadAreaFilterAssignmentsForAttributes()
    {
        $areaId = $_POST['areaId'];

        if (!$areaId) {
            MyBookingsRESPluginHttpResult::JSONOutput(1, null);
        }

        $db = new MyBookingsRES_DB();

        $assignments = $db->getFilterSettingsForAttributes($areaId);

        MyBookingsRESPluginHttpResult::JSONOutput(0, $assignments);

    }

    public function loadAreaFilterDataForDistances()
    {
        $areaId = $_POST['areaId'];

        if (!$areaId) {
            MyBookingsRESPluginHttpResult::JSONOutput(1, []);
        }

        $db = new MyBookingsRES_DB();

        $distances = $db->getDistancesForArea($areaId);

        MyBookingsRESPluginHttpResult::JSONOutput(0, $distances);
    }

    public function saveAreaFilterDataForDistances()
    {
        $areaId = $_POST['areaId'];
        $distanceIds = $_POST['distanceIds'] ? $_POST['distanceIds'] : [];

        if (!$areaId) {
            MyBookingsRESPluginHttpResult::JSONOutput(1, null);
        }

        $db = new MyBookingsRES_DB();

        foreach ($distanceIds as $distanceId) {
            $db->saveFilterSettingForDistances($areaId, $distanceId);
        }

        MyBookingsRESPluginHttpResult::JSONOutput(0, null);
    }

    public function loadAreaFilterAssignmentsForDistances()
    {
        $areaId = $_POST['areaId'];

        if (!$areaId) {
            MyBookingsRESPluginHttpResult::JSONOutput(1, null);
        }

        $db = new MyBookingsRES_DB();

        $assignments = $db->getFilterSettingsForAttributes($areaId);

        MyBookingsRESPluginHttpResult::JSONOutput(0, $assignments);

    }
}