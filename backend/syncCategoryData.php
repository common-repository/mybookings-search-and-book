<?php

/**
 * @package MyBookingsRESPlugin
 */

require_once plugin_dir_path(__FILE__) . 'connectMB.php';
require_once plugin_dir_path(__FILE__) . 'DBSync.php';

class MyBookingsRES_SyncCategoryData
{  
    public function getAttributeGroups()
    {
        $url = MyBookingsRES_ConnectMB::getUrl("getAttributeGroups");
        $req = MyBookingsRES_ConnectMB::call($url);

        $res = null;

        if ($req->error == 0) {
            $data = json_decode($req->response);
            $res = $data->data;
        }

        return (new MyBookingsRESPluginHttpResult($req->error, $res));
    }

    public function getDistanceFields()
    {
        $url = MyBookingsRES_ConnectMB::getUrl("getDistanceFields");
        $req = MyBookingsRES_ConnectMB::call($url);

        $res = null;

        if ($req->error == 0) {
            $data = json_decode($req->response);
            $res = $data->data;
        }

        return (new MyBookingsRESPluginHttpResult($req->error, $res));
    }

    public function getShortInfos()
    {
        $url = MyBookingsRES_ConnectMB::getUrl("getCategoriesShortInfos");
        $data = MyBookingsRES_ConnectMB::call($url);

        return $data;
    }

    /**
     * Get Category Infos
     *
     * @param [type] $categoryIDS 50316,50317,...
     * @return object
     */
    public function getCategoryData($categoryIDS)
    {
        $url = MyBookingsRES_ConnectMB::getUrl("getCategoryData", ["category" => $categoryIDS ]);
        $data = MyBookingsRES_ConnectMB::call($url);

        return $data;
    }

    /**
     * Get area informations
     *
     * @param [type] $areas areaid,areaid,...
     * @return array
     */
    public function getAreaInfos($areas)
    {
        $url = MyBookingsRES_ConnectMB::getUrl("getAreaInfos", ["areas" => $areas ]);
        $req = MyBookingsRES_ConnectMB::call($url);

        $res = null;

        if ($req->error == 0) {
            $data = json_decode($req->response);
            $res = $data->data;
        }

        //return (new MyBookingsRESPluginHttpResult($req->error, $res));
        
        return $data;
    }

    /**
     * Get lcoation informations
     *
     * @param [type] $locations locationid,locationid,...
     * @return array
     */
    public function getLocationsInfos($locations)
    {
        $url = MyBookingsRES_ConnectMB::getUrl("getLocationsInfos", ["locations" => $locations ]);
        $req = MyBookingsRES_ConnectMB::call($url);

        $res = null;

        if ($req->error == 0) {
            $data = json_decode($req->response);
            $res = $data->data;
        }

        //return (new MyBookingsRESPluginHttpResult($req->error, $res));
        
        return $data;
    }

    public function sync($categories)
    {
        $db = new MyBookingsRES_DB();

        $categoryDetailsReq = $this->getCategoryData($categories);

        $res = [];

        if($categoryDetailsReq->error === 0) {
            
            $categoryDetails = json_decode($categoryDetailsReq->response);

            $dbSync = new MyBookingsRES_DBSync();

            $highestCategorySortOrder = $db->getHighestCategorySortOrder();

            $index = ($highestCategorySortOrder ? $highestCategorySortOrder : 0) + 1;

            foreach($categoryDetails->data as $catId=>$detailData) {
                
                $res[$catId] = "OK";

                $info = $detailData->categoryDetails;
                $infoObject = $detailData->objectDetails;
                $texte = $detailData->texte;
                $images = $detailData->images;
                $roomCount = $detailData->objectcount;
                $priceDetails = $detailData->priceDetails;
                $attributes = $detailData->attributes;
                $distances = $detailData->distances;

                if (!is_null($detailData)) {
                    $dbSync->addOrUpdateCategory($catId, $detailData, $index);
                    $dbSync->addOrUpdateTexts($catId, $texte);
                    $dbSync->addOrUpdateImages($catId, $images);
                    $dbSync->addOrUpdateAttributesAndAttributeGroups($catId, $attributes);
                    $dbSync->addOrUpdateDistances($catId, $distances);
                }

                $index++;

                // print_r($attributes);
                // print_r($distances);
            }

            $categoriesArr = explode(",", $categories);
            foreach($categoriesArr as $catId) {
                if (!isset($res[$catId])) {
                    $res[$catId] = "ERROR";
                }
            }
        }

        return (new MyBookingsRESPluginHttpResult($categoryDetailsReq->error, $res));
    }
}