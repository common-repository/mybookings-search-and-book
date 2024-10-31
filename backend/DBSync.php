<?php

require_once plugin_dir_path(__FILE__) . 'DB.php';
require_once plugin_dir_path(__FILE__) . 'syncCategoryData.php';
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

/**
 * @package MyBookingsRESPlugin
 */

/**
 * This class contains all necessary database functions for the synchronization process between my-bookings and the plugin's database tables.
 * Class MyBookingsRES_DBSync
 */
class MyBookingsRES_DBSync extends MyBookingsRES_DB
{
    /**
     * Returns the configuration which contains all the plugin's database tables.
     * @return array
     */
    public static function getPluginTableConfigurations()
    {
        return [
            MyBookingsRES_DB::getTablePrefix() . 'categories' => [
                'category' => 'int(11) NOT NULL',
                'area' => 'int(11)',
                'ndesc_de' => 'varchar(255)',
                'ndesc_en' => 'varchar(255)',
                'max_num_of_persons' => 'int(11)',
                'checkin_time' => 'datetime',
                'checkout_time' => 'datetime',
                'sizem' => 'int(11)',
                'zip' => 'varchar(255)',
                'city' => 'varchar(255)',
                'street' => 'varchar(255)',
                'street_nr' => 'varchar(255)',
                'lat' => 'float',
                'lon' => 'float',
                'min_price' => 'float',
                'max_price' => 'float',
                'detail_page_id' => 'int(11)',
                'location_id' => 'int(11)',
                'sort_order' => 'int(11)',
                'last_changed' => 'timestamp NOT NULL',
                '_primary_key' => 'category'
            ],
            MyBookingsRES_DB::getTablePrefix() . 'texte' => [
                'id' => 'int(11) NOT NULL AUTO_INCREMENT',
                'category' => 'int(11) NOT NULL',
                'lang' => 'varchar(30)',
                'ndesc' => 'text',
                'short_desc' => 'text',
                'title' => 'varchar(255)',
                'last_changed' => 'timestamp NOT NULL',
                '_primary_key' => 'id, category'
            ],
            MyBookingsRES_DB::getTablePrefix() . 'images' => [
                'id' => 'int(11) NOT NULL AUTO_INCREMENT',
                'category' => 'int(11) NOT NULL',
                'image_id' => 'int(11) NOT NULL',
                'desc_de' => 'varchar(255)',
                'desc_en' => 'varchar(255)',
                'sort_order' => 'int(11)',
                'last_changed' => 'timestamp NOT NULL',
                '_primary_key' => 'id'
            ],
            MyBookingsRES_DB::getTablePrefix() . 'attributes' => [
                'id' => 'int(11) NOT NULL AUTO_INCREMENT',
                'category' => 'int(11) NOT NULL',
                'group_id' => 'int(11) NOT NULL',
                'attribut_id' => 'int(11) NOT NULL',
                'desc_de' => 'varchar(255)',
                'desc_en' => 'varchar(255)',
                'desc_es' => 'varchar(255)',
                'last_changed' => 'timestamp NOT NULL',
                '_primary_key' => 'id'
            ],
            MyBookingsRES_DB::getTablePrefix() . 'attributes_groups' => [
                'id' => 'int(11) NOT NULL AUTO_INCREMENT',
                'group_id' => 'int(11) NOT NULL',
                'group_name_de' => 'varchar(255)',
                'group_name_en' => 'varchar(255)',
                'group_name_es' => 'varchar(255)',
                'last_changed' => 'timestamp NOT NULL',
                '_primary_key' => 'id'
            ],
            MyBookingsRES_DB::getTablePrefix() . 'attribute_filter_settings' => [
                'id' => 'int(11) NOT NULL AUTO_INCREMENT',
                'area_id' => 'int(11) NOT NULL',
                'attribute_id' => 'int(11) NOT NULL',
                'last_changed' => 'timestamp NOT NULL',
                '_key' => 'area_id',
                '_primary_key' => 'id'
            ],
            MyBookingsRES_DB::getTablePrefix() . 'distances' => [
                'id' => 'int(11) NOT NULL AUTO_INCREMENT',
                'category' => 'int(11) NOT NULL',
                'field_id' => 'int(11) NOT NULL',
                'value' => 'varchar(255)',
                'last_changed' => 'timestamp NOT NULL',
                '_primary_key' => 'id'
            ],
            MyBookingsRES_DB::getTablePrefix() . 'distance_filter_settings' => [
                'id' => 'int(11) NOT NULL AUTO_INCREMENT',
                'area_id' => 'int(11) NOT NULL',
                'distance_id' => 'int(11) NOT NULL',
                'last_changed' => 'timestamp NOT NULL',
                '_key' => 'area_id',
                '_primary_key' => 'id'
            ],
            MyBookingsRES_DB::getTablePrefix() . 'distances_fields' => [
                'id' => 'int(11) NOT NULL AUTO_INCREMENT',
                'label_de' => 'varchar(255)',
                'label_en' => 'varchar(255)',
                'label_es' => 'varchar(255)',
                'last_changed' => 'timestamp NOT NULL',
                '_primary_key' => 'id'
            ],
            MyBookingsRES_DB::getTablePrefix() . 'areas' => [
                'id' => 'int(11) NOT NULL AUTO_INCREMENT',
                'area_id' => 'int(11) NOT NULL',
                'area_name' => 'varchar(255)',
                'last_changed' => 'timestamp NOT NULL',
                '_unique_key' => 'area_id',
                '_primary_key' => 'id'
            ],
            MyBookingsRES_DB::getTablePrefix() . 'locations' => [
                'id' => 'int(11) NOT NULL AUTO_INCREMENT',
                'location_id' => 'int(11) NOT NULL',
                'description' => 'varchar(255)',
                'description_en' => 'varchar(255)',
                'lat' => 'decimal(11,9)',
                'lon' => 'decimal(11,9)',
                'last_changed' => 'timestamp NOT NULL',
                '_unique_key' => 'location_id',
                '_primary_key' => 'id'
            ],
            MyBookingsRES_DB::getTablePrefix() . 'settings' => [
                'id' => 'int(11) NOT NULL AUTO_INCREMENT',
                'setting_key' => 'varchar(155)',
                'setting_value' => 'text',
                '_unique_key' => 'setting_key',
                '_primary_key' => 'id'
            ],
            MyBookingsRES_DB::getTablePrefix() . 'search_filter' => [
                // todo
            ],
            MyBookingsRES_DB::getTablePrefix() . 'search_distances' => [
                // todo
            ]
        ];
    }

    /**
     * Creates result pages to display all 'categories' from the plugin's 'categories' table in all available polylang languages.
     */
    public function createResultPages()
    {
        global $wpdb;

        $db = new MyBookingsRES_DB();

        $availableLanguages = pll_languages_list();

        $currentUser = wp_get_current_user();

        $resultPageId = $db->getResultPageId();


        $postTitleDE = "Apartments"; 
        $postTitleEN = "Apartments"; 

        $accommodationType = $db->getSetting("accommodationType");
        
        if ($accommodationType == "Z") {
            $postTitleDE = "Zimmer"; 
            $postTitleEN = "Rooms"; 
        }

        if (!$resultPageId) {

            $page = [
                'post_type'   => 'page',
                'post_status' => 'publish',
                'post_author' => $currentUser->ID,
                'post_title'  => $postTitleDE,
                'post_content' => "[MyBookingsRES-Result]",
                'meta_input' => ['_wp_page_template' => 'templates/template-full-width.php']
            ];

            $resultPageId = wp_insert_post($page);

            $db->addResultPageId($resultPageId);

            $pages = [];
            $pages['de'] = $resultPageId;

            foreach ($availableLanguages as $availableLanguage) {

                if ($availableLanguage === 'de') {
                    continue;
                }

                if ($availableLanguage === 'en') {
                    $page['post_title'] = $postTitleEN;
                }

                $translatedPageId = wp_insert_post($page);

                $pages[$availableLanguage] = $translatedPageId;
            }

            foreach ($pages as $language => $postId) {
                pll_set_post_language($postId, $language);
            }

            pll_save_post_translations($pages);
            return;
        }

        if (get_permalink($resultPageId)) {

            // check if page is translated in every available language
            $postTranslations = pll_get_post_translations($resultPageId);

            $existingPostTranslations = [];

            foreach ($postTranslations as $language => $postTranslationId) {
                $existingPostTranslations[] = $language;
            }

            $missingPostTranslations = array_diff($availableLanguages, $existingPostTranslations);

            $createdPageTranslations = [];
            foreach ($missingPostTranslations as $language) {

                $page = [
                    'post_type'   => 'page',
                    'post_status' => 'publish',
                    'post_author' => $currentUser->ID,
                    'post_title'  => $postTitleDE,
                    'post_content' => "[MyBookingsRES-Result]",
                    'meta_input' => ['_wp_page_template' => 'templates/template-full-width.php']
                ];

                if ($language === 'en') {
                    $page['post_title'] = 'Result Page';
                }

                if ($language === 'es') {
                    $page['post_title'] = 'Pagina de resultados';
                }

                $translatedPageId = wp_insert_post($page);

                pll_set_post_language($translatedPageId, $language);

                $createdPageTranslations[$language] = $translatedPageId;
            }

            pll_save_post_translations($createdPageTranslations + $postTranslations);

        } else {
            $db->removeResultPageId();

            $this->createResultPages();
        }
    }

    /**
     * Creates payment result pages in all available polylang languages.
     */
    public function createPaymentReturnPages()
    {
        $db = new MyBookingsRES_DB();

        $availableLanguages = pll_languages_list();

        $currentUser = wp_get_current_user();

        $paymentSuccessPageId = $db->getPaymentSuccessPageId();
        $paymentErrorPageId = $db->getPaymentErrorPageId();

        $englishIsAvailable = in_array('en', $availableLanguages);

        $returnPageSettings = [
            'success' => [
                'pageId' => $paymentSuccessPageId,
                'pageData' => [
                    'de' => [
                        'postTitle' => 'Zahlung Erfolgreich',
                        'postContentText' => 'Zahlung erfolgreich!'
                    ],
                    'en' => [
                        'postTitle' => 'Payment Successful',
                        'postContentText' => 'Payment successful!'
                    ]
                ]
            ],
            'error' => [
                'pageId' => $paymentErrorPageId,
                'pageData' => [
                    'de' => [
                        'postTitle' => 'Zahlung Fehlerhaft',
                        'postContentText' => 'Zahlung fehlerhaft!'
                    ],
                    'en' => [
                        'postTitle' => 'Payment Error',
                        'postContentText' => 'Payment Error!'
                    ]
                ]
            ]
        ];

        foreach ($returnPageSettings as $returnType => $returnPageSettingData) {

            $pageId = $returnPageSettingData['pageId'];
            $pageData = $returnPageSettingData['pageData'];

            if (!$pageId) {

                $page = [
                    'post_type'   => 'page',
                    'post_status' => 'publish',
                    'post_author' => $currentUser->ID,
                    'post_title'  => $pageData['de']['postTitle'],
                    'post_content' =>
                        "<div style='display: flex; align-items: center; justify-content: center;'><h3>{$pageData['de']['postContentText']}</h3></div>",
                    'meta_input' => ['_wp_page_template' => 'templates/template-full-width.php']
                ];

                $pageId = wp_insert_post($page);

                if ($returnType === 'success') {
                    $db->addPaymentSuccessPageId($pageId);
                } else {
                    $db->addPaymentErrorPageId($pageId);
                }

                $pages = [];
                $pages['de'] = $pageId;

                if ($englishIsAvailable) {

                    $page['post_title'] = $pageData['en']['postTitle'];
                    $page['post_content'] = "<div style='display: flex; align-items: center; justify-content: center;'><h3>{$pageData['en']['postContentText']}</h3></div>";

                    $translatedPageId = wp_insert_post($page);

                    $pages['en'] = $translatedPageId;
                }

                foreach ($pages as $language => $postId) {
                    pll_set_post_language($postId, $language);
                }

                pll_save_post_translations($pages);
                continue;
            }

            if (get_permalink($pageId)) {

                $postTranslations = pll_get_post_translations($pageId);

                $existingPostTranslations = [];

                foreach ($postTranslations as $language => $postTranslationId) {
                    $existingPostTranslations[] = $language;
                }

                // check if page is translated in english if available
                if (!in_array('en', $existingPostTranslations) && $englishIsAvailable) {

                    $createdPageTranslations = [];

                    $page = [
                        'post_type'   => 'page',
                        'post_status' => 'publish',
                        'post_author' => $currentUser->ID,
                        'post_title'  => $pageData['en']['postTitle'],
                        'post_content' => "<div style='display: flex; align-items: center; justify-content: center;'><h3>{$pageData['en']['postContentText']}</h3></div>",
                        'meta_input' => ['_wp_page_template' => 'templates/template-full-width.php']
                    ];

                    $translatedPageId = wp_insert_post($page);

                    pll_set_post_language($translatedPageId, 'en');

                    $createdPageTranslations['en'] = $translatedPageId;

                    pll_save_post_translations($createdPageTranslations + $postTranslations);
                }

            } else {
                if ($returnType === 'success') {
                    $db->removePaymentSuccessPageId();

                } else {
                    $db->removePaymentErrorPageId();
                }

                $this->createPaymentReturnPages();
            }

        }
    }

    /**
     * Adds or updates all plugin's tables from the main table configuration.
     */
    public function addOrUpdateTables()
    {
        $tableConfigs = MyBookingsRES_DBSync::getPluginTableConfigurations();

        foreach ($tableConfigs as $tableName => $tableConfig) {

            if (count($tableConfig) > 0) {
                $this->addOrUpdateTable($tableName, $tableConfig);
            }
        }
    }

    /**
     * Adds or updates a plugin's table.
     * @param $tableName
     * @param $tableConfig
     */
    public function addOrUpdateTable($tableName, $tableConfig)
    {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $counter = 0;
        $columnCount = count($tableConfig) - 1;

        $sql = "CREATE TABLE " . $tableName . " ( \n";

        foreach ($tableConfig as $columnName => $columnConfig) {

            if ($columnCount !== $counter) {

                if ($columnName === '_key') {
                    $sql .= 'KEY idx_' . $columnConfig . ' (' . $columnConfig . "),\n";
                } elseif ($columnName === '_unique_key') {
                    $sql .= 'UNIQUE KEY idx_' . $columnConfig . ' (' . $columnConfig . "),\n";
                } else {
                    $sql .= $columnName . " " . $columnConfig . ",\n";
                }
            } else {
                if ($columnName === '_primary_key') {

                    $sql .= 'PRIMARY KEY  (' . $columnConfig . ') ';
                } else {
                    $sql .= $columnName . ' ' . $columnConfig;
                }
            }

            $counter++;
        }

        $sql .= ') ' . MyBookingsRES_DBSync::getTableCollation() . ';';

        dbDelta($sql);
    }

    /**
     * Adds or updates a 'category' from the plugin's 'categories' table.
     * @param $categoryId
     * @param $categoryData
     * @return bool|false|int
     * @throws Exception
     */
    public function addOrUpdateCategory($categoryId, $categoryData, $sortOrder)
    {
        global $wpdb;

        if (!$categoryId || $categoryId === 0 || !$categoryData) {
            return false;
        }

        $priceDetails = $categoryData->priceDetails;
        $categoryData = $categoryData->categoryDetails[0];

        $db = new MyBookingsRES_DB();

        $category = $db->getCategoryById($categoryId);

        $data = [
            'category' => $categoryId,
            'area' => $categoryData->area,
            'ndesc_de' => $categoryData->ndesc,
            'ndesc_en' => $categoryData->ndesc_en,
            'max_num_of_persons' => $categoryData->max_num_of_persons,
            'checkin_time' => $categoryData->checkin_time,
            'checkout_time' => $categoryData->checkout_time,
            'sizem' => $categoryData->sizem,
            'zip' => $categoryData->zip,
            'city' => $categoryData->city,
            'street' => $categoryData->street,
            'street_nr' => $categoryData->street_nr,
            'lat' => $categoryData->lat,
            'lon' => $categoryData->lon,
            'min_price' => $priceDetails->min_price,
            'max_price' => $priceDetails->max_price,
            'sort_order' => $sortOrder,
            'detail_page_id' => null,
            'location_id' => $categoryData->location_id,
            'last_changed' => (new DateTime('now'))->format('Y-m-d H:i:s')
        ];

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'categories';

        if ($category) {
            // update existing
            $data['detail_page_id'] = $category['detail_page_id'];
            $success = $wpdb->update($tableName, $data, ['category' => $categoryId]);
        } else {
            // create new
            $success = $wpdb->insert($tableName, $data);
        }

        return $success;
    }

    /**
     * Adds or updates a 'text' from the plugin's 'texts' table.
     * @param $categoryId
     * @param $textData
     * @return bool
     * @throws Exception
     */
    public function addOrUpdateTexts($categoryId, $textData)
    {
        global $wpdb;

        if (!$categoryId || $categoryId === 0 || !$textData) {
            return false;
        }

        $db = new MyBookingsRES_DB();

        $db->deleteTextByCategoryId($categoryId);

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'texte';

        foreach ($textData as $language => $textDataRow) {
            $text = $db->getText($categoryId, $language);

            $data = [
                'category' => $categoryId,
                'lang' => $language,
                'ndesc' => $textDataRow->desc,
                'short_desc' => $textDataRow->shortdesc,
                'title' => $textDataRow->title,
                'last_changed' => (new DateTime('now'))->format('Y-m-d H:i:s')
            ];

            if ($text) {
                // update existing
                $wpdb->update($tableName, $data, ['category' => $categoryId, 'lang' => $language]);
            } else {
                // create new
                $wpdb->insert($tableName, $data);
            }
        }
    }

    /**
     * Adds or updates a 'image' from the plugin's 'images' table.
     * @param $categoryId
     * @param $images
     * @return bool
     * @throws Exception
     */
    public function addOrUpdateImages($categoryId, $images)
    {
        global $wpdb;

        if (!$categoryId || $categoryId === 0 || !$images) {
            return false;
        }

        $db = new MyBookingsRES_DB();

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'images';

        $existingImages = $db->getImagesByCategoryId($categoryId);
        $existingImageIds = [];
        foreach ($existingImages as $existingImage) {
            $existingImageIds[] = $existingImage['image_id'];
        }

        $remoteImageIds = [];

        foreach ($images as $imageData) {

            $remoteImageIds[] = $imageData->imageid;

            $image = $db->getImage($categoryId, $imageData->imageid);

            $data = [
                'category' => $categoryId,
                'image_id' => $imageData->imageid,
                'desc_de' => $imageData->desc_de,
                'desc_en' => $imageData->desc_en,
                'sort_order' => $imageData->sortorder,
                'last_changed' => (new DateTime('now'))->format('Y-m-d H:i:s')
            ];

            if ($image) {
                // update existing
                $wpdb->update($tableName, $data, ['category' => $categoryId, 'image_id' => $imageData->imageid]);
            } else {
                // create new
                $wpdb->insert($tableName, $data);
            }
        }

        $imageIdsToDelete = array_diff($existingImageIds, $remoteImageIds);

        foreach ($imageIdsToDelete as $imageIdToDelete) {
            $db->deleteImage($categoryId, $imageIdToDelete);
        }
    }

    /**
     * Adds or updates 'attributes' from the plugin's 'attributes' table.
     * @param $categoryId
     * @param $attributeData
     * @return bool
     * @throws Exception
     */
    public function addOrUpdateAttributesAndAttributeGroups($categoryId, $attributeData)
    {
        global $wpdb;

        if (!$categoryId || $categoryId === 0 || !$attributeData) {
            return false;
        }

        $db = new MyBookingsRES_DB();

        $db->deleteAttributesByCategoryId($categoryId);

        $this->addOrUpdateAttributeGroups($attributeData);

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'attributes';

        foreach ($attributeData as $attributeGroup) {
            foreach ($attributeGroup->attributes as $attribute) {

                $data = [
                    'category' => $attribute->category,
                    'group_id' => $attribute->group_id,
                    'attribut_id' => $attribute->attribut_id,
                    'desc_de' => $attribute->desc_de,
                    'desc_en' => $attribute->desc_en,
                    'desc_es' => $attribute->desc_es,
                    'last_changed' => (new DateTime('now'))->format('Y-m-d H:i:s')
                ];

                $wpdb->insert($tableName, $data);
            }
        }
    }

    /**
     * Adds or updates 'attribute_groups' from the plugin's 'attributes_groups' table.
     * @param $attributesData
     * @return bool
     * @throws Exception
     */
    private function addOrUpdateAttributeGroups($attributesData)
    {
        global $wpdb;

        if (!$attributesData) {
            return false;
        }

        $db = new MyBookingsRES_DB();

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'attributes_groups';

        foreach ($attributesData as $attributeGroupData) {
            $attributeGroup = $db->getAttributeGroup($attributeGroupData->group_id);

            $data = [
                'group_id' => $attributeGroupData->group_id,
                'group_name_de' => $attributeGroupData->group_name_de,
                'group_name_en' => $attributeGroupData->group_name_en,
                'group_name_es' => $attributeGroupData->group_name_es,
                'last_changed' => (new DateTime('now'))->format('Y-m-d H:i:s')
            ];

            if ($attributeGroup) {
                // update existing
                $wpdb->update($tableName, $data, ['group_id' => $attributeGroupData->group_id]);
            } else {
                // create new
                $wpdb->insert($tableName, $data);
            }
        }
    }

    /**
     * Adds or updates 'distances' from the plugin's 'distances' table.
     * @param $categoryId
     * @param $distances
     * @return bool
     * @throws Exception
     */
    public function addOrUpdateDistances($categoryId, $distances)
    {
        global $wpdb;

        if (!$distances) {
            return false;
        }

        $db = new MyBookingsRES_DB();

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'distances_fields';

        $db->deleteDistanceAssignmentByCategoryId($categoryId);

        foreach ($distances as $distance) {
            $existingDistance = $db->getDistanceField($distance->id);

            $data = [
                'id' => $distance->id,
                'label_de' => $distance->label_de,
                'label_en' => $distance->label_en,
                'label_es' => $distance->label_es,
                'last_changed' => (new DateTime('now'))->format('Y-m-d H:i:s')
            ];

            if ($existingDistance) {
                // update existing
                $wpdb->update($tableName, $data, ['id' => $distance->id]);
            } else {
                // create new
                $wpdb->insert($tableName, $data);
            }

            if ($distance->value) {
                $this->addOrUpdateDistanceAssignment($categoryId, $distance->id, $distance->value);
            }
        }
    }

    /**
     * Adds or updates 'distance'-assignments from the plugin's 'distances' table.
     * @param $categoryId
     * @param $distanceFieldId
     * @param $value
     * @throws Exception
     */
    private function addOrUpdateDistanceAssignment($categoryId, $distanceFieldId, $value)
    {
        global $wpdb;

        $db = new MyBookingsRES_DB();

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'distances';

        $distanceAssignment = $db->getDistanceAssignment($categoryId, $distanceFieldId);

        $data = [
            'category' => $categoryId,
            'field_id' => $distanceFieldId,
            'value' => $value,
            'last_changed' => (new DateTime('now'))->format('Y-m-d H:i:s')
        ];

        if ($distanceAssignment) {
            // update existing
            $wpdb->update($tableName, $data, ['category' => $categoryId, 'field_id' => $distanceFieldId]);
        } else {
            // create new
            $wpdb->insert($tableName, $data);
        }
    }

    /**
     * Adds or updates 'areas' from the plugin's 'areas' table.
     * @throws Exception
     */
    public function addOrUpdateAreas()
    {
        global $wpdb;

        $db = new MyBookingsRES_DB();

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'areas';

        $distinctAreaIds = $db->getDistinctAreaIds();

        $syncCategoryData = new MyBookingsRES_SyncCategoryData();

        $areaIds = [];

        foreach ($distinctAreaIds as $distinctAreaId) {
            $areaIds[] = $distinctAreaId['area'];
        }

        $areaInfos = $syncCategoryData->getAreaInfos(implode(', ', $areaIds))->data;

        foreach ($areaInfos as $areaInfo) {
            $existingArea = $db->getAreaByAreaId((int) $areaInfo->areaid);

            $data = [
                'area_id' => (int) $areaInfo->areaid,
                'area_name' => $areaInfo->areaname,
                'last_changed' => (new DateTime('now'))->format('Y-m-d H:i:s')
            ];

            if ($existingArea) {
                // update
                $wpdb->update($tableName, $data, ['area_id' => (int) $areaInfo->areaid]);

            } else {
                // create
                $wpdb->insert($tableName, $data);
            }
        }
    }

    /**
     * Adds or updates 'locations' from the plugin's 'locations' table.
     * @throws Exception
     */
    public function addOrUpdateLocations()
    {
        global $wpdb;

        $db = new MyBookingsRES_DB();

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'locations';

        $distinctLocationIds = $db->getDistinctLocationIds();

        $syncCategoryData = new MyBookingsRES_SyncCategoryData();

        $locationIds = [];

        foreach ($distinctLocationIds as $locationId) {
            $locationIds[] = $locationId['location_id'];
        }

        $locationInfos = $syncCategoryData->getLocationsInfos(implode(', ', $locationIds))->data;

        foreach ($locationInfos as $locationInfo) {

            $existingArea = $db->getLocationById((int) $locationInfo->id);

            $data = [
                'location_id' => (int) $locationInfo->id,
                'description' => $locationInfo->description,
                'description_en' => $locationInfo->description_en,
                'lat' => $locationInfo->lat,
                'lon' => $locationInfo->lon,
                'last_changed' => (new DateTime('now'))->format('Y-m-d H:i:s')
            ];

            if ($existingArea) {
                // update
                $wpdb->update($tableName, $data, ['location_id' => (int) $locationInfo->id]);

            } else {
                // create
                $wpdb->insert($tableName, $data);
            }
        }
    }

    /**
     * Removes unused 'categories' from the plugin's 'categories' table.
     * @param $categories
     */
    public function deleteRemovedCategories($categories)
    {
        global $wpdb;

        $db = new MyBookingsRES_DB();
        $tableNamePrefix = MyBookingsRES_DB::getTablePrefix();

        $categoryIds = [];

        foreach ($categories as $category) {
            $categoryIds[] = (int) $category->catid;
        }

        $existingCategories = $db->getCategories();
        $existingCategoryIds = [];

        foreach ($existingCategories as $existingCategory) {
            $existingCategoryIds[] = (int) $existingCategory['category'];
        }

        $categoryIdsToDelete = array_diff($existingCategoryIds, $categoryIds);

        foreach ($categoryIdsToDelete as $categoryIdToDelete) {
            $wpdb->delete($tableNamePrefix . 'categories', ['category' => $categoryIdToDelete]);
            $wpdb->delete($tableNamePrefix . 'texte', ['category' => $categoryIdToDelete]);
            $wpdb->delete($tableNamePrefix . 'images', ['category' => $categoryIdToDelete]);
            $wpdb->delete($tableNamePrefix . 'distances', ['category' => $categoryIdToDelete]);
            $wpdb->delete($tableNamePrefix . 'attributes', ['category' => $categoryIdToDelete]);
        }
    }

    public function resetCategorySortOrder()
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'categories';

        $success = $wpdb->query("UPDATE $tableName SET sort_order = NULL WHERE sort_order > 0");

        return $success;
    }
}