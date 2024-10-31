<?php

/**
 * This class contains all necessary database functions for the plugin.
 * @package MyBookingsRESPlugin
 */
class MyBookingsRES_DB
{
    /**
     * Returns the plugin's table prefix for all tables that are created by the plugin.
     * @return string
     */
    public static function getTablePrefix()
    {
        global $wpdb;

        return $wpdb->base_prefix . 'mybookingsres_';
    }

    /**
     * Returns the main database table collation.
     * @return string
     */
    public static function getTableCollation()
    {
        global $wpdb;

        return $wpdb->get_charset_collate();
    }

    /**
     * Returns all the database tables.
     * @return array
     */
    public function getTableNames()
    {
        global $wpdb;

        $tables = $wpdb->get_results("SHOW TABLES");

        $tableNames = [];

        foreach ($tables as $table)
        {
            $tableNames[] = $table->Tables_in_wordpress;
        }

        return $tableNames;
    }

    public function getFilteredCategories($attributeIds, $locationIds)
    {
        global $wpdb;

        $sortCategories = (boolean) $this->getSetting('sortCategories');

        $categories = $this->getShortCategoriesInfo(!$sortCategories, null, false);

        if (count($attributeIds) === 0 && count($locationIds) === 0) {
            $categoryIds = [];

            foreach ($categories as $category) {
                $categoryIds[] = $category->category;
            }

            return $categoryIds;
        }

        $result = [];

        foreach($categories as $cat) {
            $hasAttributes = $this->checkCategoryHasAttributes($cat->category, $attributeIds);
            $hasLocations = $this->checkCategoryHasLocations($cat->category, $locationIds);

            if ($hasAttributes && $hasLocations) {
                $result[] = $cat->category;
            }
        }

        return $result;
    }

    /**
     * Returns the short information of a 'category' from the plugin's 'categories' table.
     * @param bool $orderByRand
     * @param null $limit
     * @param bool $loadTexte
     * @return array|object|null
     */
    public function getShortCategoriesInfo($orderByRand = true, $limit = null, $loadTexte = true)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'categories';
        $tableNameTexte = MyBookingsRES_DB::getTablePrefix() . 'texte';

        $sql = "SELECT i.category, i.ndesc_de, i.last_changed FROM $tableName i";
        
        if ($orderByRand) {
            $sql .= " ORDER BY RAND() ";
        } else {
            $sql .= ' ORDER BY sort_order ASC ';
        }

        if ($limit != null) {
            $sql .= " LIMIT " . (int) $limit;
        }

        $categoryData = $wpdb->get_results($sql, 'OBJECT');

        foreach($categoryData as $data) {
            if ($loadTexte) {
                $sql = $wpdb->prepare("SELECT t.title, t.lang FROM $tableNameTexte t WHERE t.category = %d", (int) $data->category);

                $data->textInfos = $wpdb->get_results($sql, 'OBJECT');
            }
        }

        return $categoryData;
    }

    public function getCategoriesForLocation($locationId, $orderByRand = true)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'categories';
        $tableNameTexte = MyBookingsRES_DB::getTablePrefix() . 'texte';

        $sql = $wpdb->prepare("SELECT i.category, i.ndesc_de, i.last_changed FROM $tableName i WHERE i.location_id = %d", (int) $locationId);

        if ($orderByRand) {
            $sql .= " ORDER BY RAND() ";
        } else {
            $sql .= ' ORDER BY sort_order ASC ';
        }

        $categoryData = $wpdb->get_results($sql, 'OBJECT');

        foreach($categoryData as $data) {
            $sql = $wpdb->prepare("SELECT t.title, t.lang FROM $tableNameTexte t WHERE t.category = %d", (int) $data->category);

            $data->textInfos = $wpdb->get_results($sql, 'OBJECT');
        }

        return $categoryData;
    }

    /**
     * Returns a 'category' from the plugin's 'categories' table by an id.
     * @param $categoryId
     * @return array|object|void|null
     */
    public function getCategoryById($categoryId)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'categories';

        $sql = $wpdb->prepare("SELECT c.* FROM $tableName c WHERE c.category = %d", (int) $categoryId);

        $category = $wpdb->get_row($sql, 'ARRAY_A');

        if (!$category) {
            return null;
        }

        return $category;
    }

    /**
     * Returns the highest sort order from the plugin's 'categories' table.
     * @return string|null
     */
    public function getHighestCategorySortOrder()
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'categories';

        return $wpdb->get_var("SELECT c.sort_order FROM $tableName c ORDER BY c.sort_order DESC LIMIT 1");
    }

    /**
     * Returns a 'text' from the plugin's 'texts' table by an id and language string.
     * @param $categoryId
     * @param $language
     * @return array|object|void|null
     */
    public function getText($categoryId, $language)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'texte';

        $sql = $wpdb->prepare("SELECT t.* FROM $tableName t WHERE t.category = %d AND t.lang = %s", (int) $categoryId, $language);

        $text = $wpdb->get_row($sql, 'ARRAY_A');

        if (!$text) {
            return null;
        }

        return $text;
    }

    /**
     * Deletes a 'text' from the plugin's 'texts' table by an id.
     * @param $categoryId
     */
    public function deleteTextByCategoryId($categoryId)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'texte';

        $wpdb->delete($tableName, ['category' => $categoryId]);
    }

    /**
     * Returns an 'image' from the plugin's 'images' table by an 'category'-id and 'image'-id.
     * @param $categoryId
     * @param $imageId
     * @return array|object|void|null
     */
    public function getImage($categoryId, $imageId)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'images';

        $sql = $wpdb->prepare("SELECT i.* FROM $tableName i WHERE i.category = %d AND i.image_id = %d", (int) $categoryId, (int) $imageId);

        $image = $wpdb->get_row($sql, 'ARRAY_A');

        if (!$image) {
            return null;
        }

        return $image;
    }

    /**
     * Returns an array of 'images' from the plugin's 'images' table by an 'category'-id.
     * @param $categoryId
     * @return array|object|null
     */
    public function getImagesByCategoryId($categoryId)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'images';

        $sql = $wpdb->prepare("SELECT i.* FROM $tableName i WHERE i.category = %d ORDER BY i.sort_order ASC", (int) $categoryId);

        $images = $wpdb->get_results($sql, 'ARRAY_A');

        if (!$images) {
            return null;
        }

        return $images;
    }

    /**
     * Deletes an 'image' from the plugin's 'images' table by a 'category'-id and an 'image'-id.
     * @param $categoryId
     * @param $imageId
     */
    public function deleteImage($categoryId, $imageId)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'images';


        $wpdb->delete($tableName, ['category' => $categoryId, 'image_id' => $imageId]);
    }

    /**
     * Returns all 'attributes_groups' from the plugin's 'attributes_groups' table.
     * @return array|object|null
     */
    public function getAttributeGroups()
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'attributes_groups';

        $sql = "SELECT ag.* FROM $tableName ag";

        $attributeGroups = $wpdb->get_results($sql, 'ARRAY_A');

        if (!$attributeGroups) {
            return null;
        }

        return $attributeGroups;
    }

    /**
     * Returns an 'attributes_group' from the plugin's 'attributes_groups' table by a 'attributes_group'-id.
     * @param $groupId
     * @return array|object|void|null
     */
    public function getAttributeGroup($groupId)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'attributes_groups';

        $sql = $wpdb->prepare("SELECT ag.* FROM $tableName ag WHERE ag.group_id = %d", (int) $groupId);

        $attributeGroup = $wpdb->get_row($sql, 'ARRAY_A');

        if (!$attributeGroup) {
            return null;
        }

        return $attributeGroup;
    }

    /**
     * Returns an 'attribute' from the plugin's 'attributes' table by a 'category'-id, an 'attributes_group'-id and an 'attribute'-id.
     * @param $categoryId
     * @param $attributeGroupId
     * @param $attributeId
     * @return array|object|void|null
     */
    public function getAttribute($categoryId, $attributeGroupId, $attributeId)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'attributes';

        $sql = $wpdb->prepare(
            "SELECT a.* FROM $tableName a WHERE a.category = %d AND a.group_id = %d AND a.attribut_id = %d",
            (int) $categoryId,
            (int) $attributeGroupId,
            (int) $attributeId
        );

        $attribute = $wpdb->get_row($sql, 'ARRAY_A');

        if (!$attribute) {
            return null;
        }

        return $attribute;
    }

    /**
     * Returns an 'attribute' from the plugin's 'attributes' table by a 'category'-id and a 'group'-id.
     * @param $categoryId
     * @param $groupId
     * @return array|object|null
     */
    public function getAttributes($categoryId, $groupId)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'attributes';

        $sql = $wpdb->prepare("SELECT ag.* FROM $tableName ag WHERE ag.category = %d AND ag.group_id = %d", (int)$categoryId, (int) $groupId);

        $attributes = $wpdb->get_results($sql, 'ARRAY_A');

        if (!$attributes) {
            return null;
        }

        return $attributes;
    }

    /**
     * Deletes 'attributes' from the plugin's 'attributes' table by a 'category'-id.
     * @param $categoryId
     */
    public function deleteAttributesByCategoryId($categoryId)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'attributes';

        $wpdb->delete($tableName, ['category' => $categoryId]);
    }

    /**
     * Returns all 'attributes' from the plugin's 'attributes' table.
     * @return array|object|null
     */
    public function getAttributesForSearch()
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'attributes';

        return $wpdb->get_results("SELECT ag.* FROM $tableName ag WHERE group_id = 1 GROUP BY attribut_id", 'ARRAY_A');
    }

    /**
     * Returns all 'categories' from the plugin's 'categories' table which have 'attributes' assigned.
     * @param $attributes
     * @return array
     */
    public function getCategoriesWithAttributes($attributes)
    {
        global $wpdb;

        $categories = $this->getShortCategoriesInfo(false, null, false);

        $result = [];
        
        foreach($categories as $cat) {
            if ($this->checkCategoryHasAttributes($cat->category, $attributes)) {
                $result[] = $cat->category;
            }
        }

        return $result;
    }

    /**
     * Checks if a 'category' from the plugin's 'categories' table has 'attributes' assigned.
     * @param $category
     * @param $attributes
     * @return bool
     */
    public function checkCategoryHasAttributes($category, $attributes)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'attributes';

        foreach ($attributes as $attribut_id) {
            $count = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $tableName WHERE category = %d AND attribut_id = %d", $category, $attribut_id));
            if ($count === 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if a 'category' from the plugin's 'categories' table has 'locations' assigned.
     * @param $category
     * @param $locationIds
     * @return bool
     */
    public function checkCategoryHasLocations($category, $locationIds)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'categories';

        if (count($locationIds) === 0) {
            return true;
        }

        foreach ($locationIds as $locationId) {
            $count = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $tableName WHERE category = %d AND location_id = %d", $category, $locationId));
            if ($count !== 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns a 'distance' from the plugin's 'distances' table by an id.
     * @param $distanceId
     * @return array|object|void|null
     */
    public function getDistanceField($distanceId)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'distances_fields';

        $sql = $wpdb->prepare("SELECT d.* FROM $tableName d WHERE d.id = %d", (int) $distanceId);

        $distance = $wpdb->get_row($sql, 'ARRAY_A');

        if (!$distance) {
            return null;
        }

        return $distance;
    }

    /**
     * Returns a 'distance' from the plugin's 'distances' table by its assignment.
     * @param $categoryId
     * @param $distanceFieldId
     * @return array|object|void|null
     */
    public function getDistanceAssignment($categoryId, $distanceFieldId)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'distances';

        $sql = $wpdb->prepare("SELECT d.* FROM $tableName d WHERE d.category = %d AND d.field_id = %d", (int) $categoryId, (int) $distanceFieldId);

        $distance = $wpdb->get_row($sql, 'ARRAY_A');

        if (!$distance) {
            return null;
        }

        return $distance;
    }

    /**
     * Deletes a 'distance' from the plugin's 'distances' table by an 'category'-id.
     * @param $categoryId
     */
    public function deleteDistanceAssignmentByCategoryId($categoryId)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'distances';

        $wpdb->delete($tableName, ['category' => $categoryId]);
    }

    /**
     * Returns a 'distance' from the plugin's 'distances' table by an 'category'-id.
     * @param $categoryId
     * @return array|object|null
     */
    public function getDistancesForCategory($categoryId)
    {
        global $wpdb;

        $sql = $wpdb->prepare("
            SELECT 
                f.label_de, f.label_en, f.label_es, d.category, d.value 
            FROM 
                " . MyBookingsRES_DB::getTablePrefix() . "distances_fields f, 
                " . MyBookingsRES_DB::getTablePrefix() . "distances d 
            WHERE 
                f.id = d.field_id AND d.category = %d", (int) $categoryId);

        return $wpdb->get_results($sql, 'ARRAY_A');
    }

    /**
     * Creates or updates wordpress sites for each 'category' from the plugin's 'categories' table in all available polylang
     * languages.
     */
    public function createCategoryPages()
    {
        $categories = $this->getCategories();

        foreach ($categories as $category) {

            // Check if page exists
            if($category['detail_page_id']) {

                if (!get_permalink($category['detail_page_id'])) {
                    $this->createCategoryPage($category);
                } else {

                    $currentUser = wp_get_current_user();

                    $availableLanguages = pll_languages_list();

                    // check if page is translated in every available language
                    $postTranslations = pll_get_post_translations($category['detail_page_id']);

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
                            'post_title'  => $category['ndesc_de'],
                            'post_content' => "[MyBookingsRES-Category id='{$category['category']}']",
                            'meta_input' => ['_wp_page_template' => 'templates/template-full-width.php']
                        ];

                        $translatedPageId = wp_insert_post($page);

                        pll_set_post_language($translatedPageId, $language);

                        $createdPageTranslations[$language] = $translatedPageId;
                    }

                    pll_save_post_translations($createdPageTranslations + $postTranslations);
                }
            } else {
                $this->createCategoryPage($category);
            }
        }
    }

    /**
     * Creates or updates a wordpress site for a 'category' from the plugin's 'categories' table in all available polylang
     * languages.
     * @param $category
     */
    public function createCategoryPage($category)
    {
        global $wpdb;

        $currentUser = wp_get_current_user();

        $page = [
            'post_type'   => 'page',
            'post_status' => 'publish',
            'post_author' => $currentUser->ID,
            'post_title'  => $category['ndesc_de'],
            'post_content' => "[MyBookingsRES-Category id='{$category['category']}']",
            'meta_input' => ['_wp_page_template' => 'templates/template-full-width.php']
        ];

        $pageId = wp_insert_post($page);

        if ($pageId) {

            $tableName = MyBookingsRES_DB::getTablePrefix() . 'categories';

            $wpdb->update($tableName, ['detail_page_id' => $pageId], ['category' => $category['category']]);

            // create pages in other languages

            $availableLanguages = pll_languages_list();

            $pages = [];
            $pages['de'] = $pageId;

            foreach ($availableLanguages as $availableLanguage) {

                if ($availableLanguage === 'de') {
                    continue;
                }

                $page['post_content'] = "[MyBookingsRES-Category id='{$category['category']}']";

                $translatedPageId = wp_insert_post($page);

                $pages[$availableLanguage] = $translatedPageId;
            }

            foreach ($pages as $language => $postId) {
                pll_set_post_language($postId, $language);
            }

            pll_save_post_translations($pages);
        }
    }

    /**
     * Removes wordpress sites for each 'category' from the plugin's 'categories' table in all available polylang
     * languages.
     */
    public function removeCategoryPages()
    {
        $categories = $this->getCategories();

        foreach ($categories as $category) {

            $this->removeCategoryPage($category);
        }
    }

    /**
     * Removes wordpress sites for a 'category' from the plugin's 'categories' table in all available polylang
     * languages.
     * @param $category
     */
    public function removeCategoryPage($category)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'categories';

        // Check if page exists
        if(!$category['detail_page_id'] || !get_permalink($category['detail_page_id'])) {
            return;
        }

        $postTranslations = pll_get_post_translations($category['detail_page_id']);

        $success = wp_delete_post($category['detail_page_id'], true);

        if ($success) {
            $wpdb->update($tableName, ['detail_page_id' => null], ['category' => $category['category']]);
        }

        foreach ($postTranslations as $language => $postTranslationId) {
            if ($language === 'de') {
                continue;
            }

            wp_delete_post($postTranslationId, true);
        }
    }

    /**
     * Returns all 'settings' from the plugin's 'settings' table.
     * @return array|object|null
     */
    public function getSettings()
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'settings';
        $sql = "SELECT t.setting_key, t.setting_value FROM $tableName t";

        $result = $wpdb->get_results($sql, 'OBJECT_K');

        return $result;
    }

    /**
     * Returns a 'setting'-value from the plugin's 'settings' table by a 'setting'-key.
     * @param $settingKey
     * @return string|null
     */
    public function getSetting($settingKey)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'settings';

        $sql = $wpdb->prepare("SELECT t.setting_value FROM $tableName t WHERE t.setting_key = %s", $settingKey);

        $result = $wpdb->get_var($sql);

        return $result;
    }

    /**
     * Creates a 'setting' in the plugin's 'settings' table.
     * @param $settingKey
     * @param $settingValue
     */
    public function saveSetting($settingKey, $settingValue)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'settings';

        $data = [
            'setting_key' => $settingKey,
            'setting_value' => $settingValue,
        ];

        $existingSettingKey = $this->getSetting($settingKey);

        if ($existingSettingKey) {
            // update
            $wpdb->update($tableName, $data, ['setting_key' => $settingKey]);
        } else {
            // create
            $wpdb->insert($tableName, $data);
        }
    }


    /**
     * Saves the main plugin's settings in the plugin's 'settings' table.
     * @param null $apiKey
     * @param null $websiteConfigId
     * @param null $accommodationType
     * @param null $googleAPIKey
     * @param null $color1
     * @param null $color2
     * @param null $color3
     * @param null $color4
     * @param null $color_bglist
     * @param null $custom_css
     * @param null $showAreas
     */
    public function saveSettings(
        $apiKey = null, $websiteConfigId = null, 
        $accommodationType = null, $googleAPIKey = null, 
        $color1 = null, $color2 = null, $color3 = null, 
        $color4 = null, $color_bglist = null, $custom_css = null,
        $showAreas = null, $hideChildrenAges = null, $sortCategories = null,
        $showFilter = null, $hideUnavailableCategories = null)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'settings';

        $format = ['%s', '%s'];

        $storedSettings = $this->getSettings();


        if ($apiKey != null) {

            $data = [
                'setting_key' => 'apiKey',
                'setting_value' => $apiKey
            ];

            $existingApiKey = isset($storedSettings['apiKey']);

            if ($existingApiKey) {

                $where = ['setting_key' => 'apiKey'];
                $whereFormat = ['%s'];

                $wpdb->update($tableName, $data, $where, $format, $whereFormat);
            } else {
                $wpdb->insert($tableName, $data, $format);
            }
        }

        if ($websiteConfigId != null) {

            $data = [
                'setting_key' => 'websiteConfigId',
                'setting_value' => $websiteConfigId
            ];

            $existingWebsiteConfigId = isset($storedSettings['websiteConfigId']);

            if ($existingWebsiteConfigId) {

                $where = ['setting_key' => 'websiteConfigId'];
                $whereFormat = ['%s'];

                $wpdb->update($tableName, $data, $where, $format, $whereFormat);

            } else {
                $wpdb->insert($tableName, $data, $format);
            }
        }

        if ($accommodationType != null) {

            $data = [
                'setting_key' => 'accommodationType',
                'setting_value' => $accommodationType
            ];

            $existing = isset($storedSettings['accommodationType']);

            if ($existing) {

                $where = ['setting_key' => 'accommodationType'];
                $whereFormat = ['%s'];

                $wpdb->update($tableName, $data, $where, $format, $whereFormat);

            } else {
                $wpdb->insert($tableName, $data, $format);
            }
        }
        
        if ($googleAPIKey != null) {

            $data = [
                'setting_key' => 'googleAPIKey',
                'setting_value' => $googleAPIKey
            ];

            $existing = isset($storedSettings['googleAPIKey']);

            if ($existing) {

                $where = ['setting_key' => 'googleAPIKey'];
                $whereFormat = ['%s'];

                $wpdb->update($tableName, $data, $where, $format, $whereFormat);

            } else {
                $wpdb->insert($tableName, $data, $format);
            }
        }
        
        if ($color1 != null) {

            $data = [
                'setting_key' => 'color1',
                'setting_value' => $color1
            ];

            $existing = isset($storedSettings['color1']);

            if ($existing) {

                $where = ['setting_key' => 'color1'];
                $whereFormat = ['%s'];

                $wpdb->update($tableName, $data, $where, $format, $whereFormat);

            } else {
                $wpdb->insert($tableName, $data, $format);
            }
        }

        if ($color2 != null) {

            $data = [
                'setting_key' => 'color2',
                'setting_value' => $color2
            ];

            $existing = isset($storedSettings['color2']);

            if ($existing) {

                $where = ['setting_key' => 'color2'];
                $whereFormat = ['%s'];

                $wpdb->update($tableName, $data, $where, $format, $whereFormat);

            } else {
                $wpdb->insert($tableName, $data, $format);
            }
        }
        
        if ($color3 != null) {

            $data = [
                'setting_key' => 'color3',
                'setting_value' => $color3
            ];

            $existing = isset($storedSettings['color3']);

            if ($existing) {

                $where = ['setting_key' => 'color3'];
                $whereFormat = ['%s'];

                $wpdb->update($tableName, $data, $where, $format, $whereFormat);

            } else {
                $wpdb->insert($tableName, $data, $format);
            }
        }

        if ($color4 != null) {

            $data = [
                'setting_key' => 'color4',
                'setting_value' => $color4
            ];

            $existing = isset($storedSettings['color4']);

            if ($existing) {

                $where = ['setting_key' => 'color4'];
                $whereFormat = ['%s'];

                $wpdb->update($tableName, $data, $where, $format, $whereFormat);

            } else {
                $wpdb->insert($tableName, $data, $format);
            }
        }

        if ($color_bglist != null) {

            $data = [
                'setting_key' => 'color_bglist',
                'setting_value' => $color_bglist
            ];

            $existing = isset($storedSettings['color_bglist']);

            if ($existing) {

                $where = ['setting_key' => 'color_bglist'];
                $whereFormat = ['%s'];

                $wpdb->update($tableName, $data, $where, $format, $whereFormat);

            } else {
                $wpdb->insert($tableName, $data, $format);
            }
        }

        if ($custom_css != null) {

            $data = [
                'setting_key' => 'custom_css',
                'setting_value' => $custom_css
            ];

            $existing = isset($storedSettings['custom_css']);

            if ($existing) {

                $where = ['setting_key' => 'custom_css'];
                $whereFormat = ['%s'];

                $wpdb->update($tableName, $data, $where, $format, $whereFormat);

            } else {
                $wpdb->insert($tableName, $data, $format);
            }
        }

        if ($showAreas != null) {

            $data = [
                'setting_key' => 'showAreas',
                'setting_value' => $showAreas === 'true' ? '1' : '0'
            ];

            $existing = isset($storedSettings['showAreas']);

            if ($existing) {

                $where = ['setting_key' => 'showAreas'];
                $whereFormat = ['%s'];

                $wpdb->update($tableName, $data, $where, $format, $whereFormat);

            } else {
                $wpdb->insert($tableName, $data, $format);
            }
        }

        if ($hideChildrenAges != null) {

            $data = [
                'setting_key' => 'hideChildrenAges',
                'setting_value' => $hideChildrenAges === 'true' ? '1' : '0'
            ];

            $existing = isset($storedSettings['hideChildrenAges']);

            if ($existing) {

                $where = ['setting_key' => 'hideChildrenAges'];
                $whereFormat = ['%s'];

                $wpdb->update($tableName, $data, $where, $format, $whereFormat);

            } else {
                $wpdb->insert($tableName, $data, $format);
            }
        }

        if ($sortCategories != null) {

            $data = [
                'setting_key' => 'sortCategories',
                'setting_value' => $sortCategories === 'true' ? '1' : '0'
            ];

            $existing = isset($storedSettings['sortCategories']);

            if ($existing) {

                $where = ['setting_key' => 'sortCategories'];
                $whereFormat = ['%s'];

                $wpdb->update($tableName, $data, $where, $format, $whereFormat);

            } else {
                $wpdb->insert($tableName, $data, $format);
            }
        }

        if ($showFilter != null) {

            $data = [
                'setting_key' => 'showFilter',
                'setting_value' => $showFilter === 'true' ? '1' : '0'
            ];

            $existing = isset($storedSettings['showFilter']);

            if ($existing) {

                $where = ['setting_key' => 'showFilter'];
                $whereFormat = ['%s'];

                $wpdb->update($tableName, $data, $where, $format, $whereFormat);

            } else {
                $wpdb->insert($tableName, $data, $format);
            }
        }

        if ($hideUnavailableCategories != null) {

            $data = [
                'setting_key' => 'hideUnavailableCategories',
                'setting_value' => $hideUnavailableCategories === 'true' ? '1' : '0'
            ];

            $existing = isset($storedSettings['hideUnavailableCategories']);

            if ($existing) {

                $where = ['setting_key' => 'hideUnavailableCategories'];
                $whereFormat = ['%s'];

                $wpdb->update($tableName, $data, $where, $format, $whereFormat);

            } else {
                $wpdb->insert($tableName, $data, $format);
            }
        }
    }

    /**
     * Returns all 'categories' from the plugin's 'categories' table.
     * @return array|object|null
     */
    public function getCategories()
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'categories';

        return $wpdb->get_results("SELECT c.* FROM $tableName c", 'ARRAY_A');
    }

    /**
     * Returns all distinct 'area'-ids from the plugin's 'categories' table.
     * @return array|object|null
     */
    public function getDistinctAreaIds()
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'categories';

        if (!$this->checkIfTableExists($tableName)) {
            return null;
        }

        return $wpdb->get_results("SELECT DISTINCT c.area FROM $tableName c", 'ARRAY_A');
    }

    /**
     * Returns all distinct 'location'-ids from the plugin's 'categories' table.
     * @return array|object|null
     */
    public function getDistinctLocationIds()
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'categories';

        if (!$this->checkIfTableExists($tableName)) {
            return null;
        }

        return $wpdb->get_results("SELECT DISTINCT c.location_id FROM $tableName c", 'ARRAY_A');
    }

    /**
     * Returns a 'location' from the plugin's 'locations' table by an id.
     * @param $locationId
     * @return array|object|void|null
     */
    public function getLocationById($locationId)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'locations';

        $sql = $wpdb->prepare("SELECT l.* FROM $tableName l WHERE l.location_id = %d", $locationId);

        return $wpdb->get_row($sql, 'ARRAY_A');
    }

    /**
     * Returns all 'locations' from the plugin's 'locations' table.
     * @return array|object|null
     */
    public function getLocations()
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'locations';

        return $wpdb->get_results("SELECT l.* FROM $tableName l", 'ARRAY_A');
    }

    /**
     * Returns an 'area' from the plugin's 'areas' table by an id.
     * @param $areaId
     * @return array|object|void|null
     */
    public function getAreaByAreaId($areaId)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'areas';

        $sql = $wpdb->prepare("SELECT a.* FROM $tableName a WHERE a.area_id = %d", $areaId);

        return $wpdb->get_row($sql, 'ARRAY_A');
    }

    /**
     * Returns all 'attributes' from the plugin's 'attributes' table by an 'area'-id.
     * @param $areaId
     * @return array|object|null
     */
    public function getAttributesForArea($areaId)
    {
        global $wpdb;

        $attributesTableName = MyBookingsRES_DB::getTablePrefix() . 'attributes';
        $categoriesTableName = MyBookingsRES_DB::getTablePrefix() . 'categories';
        $areasTableName = MyBookingsRES_DB::getTablePrefix() . 'areas';
        $attributeFilterSettingsTableName = MyBookingsRES_DB::getTablePrefix() . 'attribute_filter_settings';

        $sql = $wpdb->prepare("
            SELECT DISTINCT attr.attribut_id, attr.desc_de, attr.desc_en, attr.desc_es, afs.attribute_id as assignment
            FROM $attributesTableName attr
            INNER JOIN $categoriesTableName cat ON cat.category = attr.category
            INNER JOIN $areasTableName area ON area.area_id = cat.area
            LEFT OUTER JOIN $attributeFilterSettingsTableName afs ON afs.area_id = area.area_id AND afs.attribute_id = attr.attribut_id
            WHERE area.area_id = %d
            ", $areaId);

        return $wpdb->get_results($sql, 'ARRAY_A');
    }

    /**
     * Returns all 'distances' from the plugin's 'attributes' table by an 'area'-id.
     * @param $areaId
     * @return array|object|null
     */
    public function getDistancesForArea($areaId)
    {
        global $wpdb;

        $distancesTableName = MyBookingsRES_DB::getTablePrefix() . 'distances';
        $distanceFieldsTableName = MyBookingsRES_DB::getTablePrefix() . 'distances_fields';
        $categoriesTableName = MyBookingsRES_DB::getTablePrefix() . 'categories';
        $areasTableName = MyBookingsRES_DB::getTablePrefix() . 'areas';
        $distanceFilterSettingsTableName = MyBookingsRES_DB::getTablePrefix() . 'distance_filter_settings';

        $sql = $wpdb->prepare("
            SELECT d.field_id, df.label_de, df.label_en, df.label_es, dfs.distance_id as assignment
            FROM $distanceFieldsTableName df
            INNER JOIN $distancesTableName d ON d.field_id = df.id
            INNER JOIN $categoriesTableName cat ON cat.category = d.category
            INNER JOIN $areasTableName area ON area.area_id = cat.area
            LEFT OUTER JOIN $distanceFilterSettingsTableName dfs ON dfs.area_id = area.area_id AND dfs.distance_id = df.id
            WHERE area.area_id = %d
            ", $areaId);

        return $wpdb->get_results($sql, 'ARRAY_A');
    }

    /**
     * Saves 'attributes' preferences in the plugin's 'attribute_filter_settings' table.
     * @param $areaId
     * @param $attributeId
     */
    public function saveFilterSettingForAttributes($areaId, $attributeId)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'attribute_filter_settings';

        $wpdb->insert($tableName, ['area_id' => $areaId, 'attribute_id' => $attributeId]);
    }

    /**
     * Saves 'attributes' preferences in the plugin's 'attribute_filter_settings' table.
     * @param $areaId
     * @param $distanceId
     */
    public function saveFilterSettingForDistances($areaId, $distanceId)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'distance_filter_settings';

        $wpdb->insert($tableName, ['area_id' => $areaId, 'distance_id' => $distanceId]);
    }

    /**
     * Returns all 'attributes' preferences in the plugin's 'attribute_filter_settings' table.
     * @param $areaId
     * @return array|object|null
     */
    public function getFilterSettingsForAttributes($areaId)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'attribute_filter_settings';

        $sql = $wpdb->prepare("SELECT f.* FROM $tableName f WHERE f.area_id = %d", (int) $areaId);

        return $wpdb->get_results($sql, 'ARRAY_A');
    }

    /**
     * Returns all 'areas' from the plugin's 'areas' table.
     * @return array|object|null
     */
    public function getAreas()
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'areas';

        return $wpdb->get_results("SELECT a.* FROM $tableName a", 'ARRAY_A');
    }

    /**
     * Returns the 'apiKey' from the plugin's 'settings' table.
     * @return string|null
     */
    public function getApiKey()
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'settings';

        $sql = "SELECT setting_value FROM $tableName WHERE setting_key = 'apiKey'";

        return $wpdb->get_var($sql);
    }

    /**
     * Returns the 'websiteConfigId' from the plugin's 'settings' table.
     * @return string|null
     */
    public function getWebsiteConfigId()
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'settings';

        $sql = "SELECT setting_value FROM $tableName WHERE setting_key = 'websiteConfigId'";

        return $wpdb->get_var($sql);
    }

    /**
     * Returns the 'resultPageId' from the plugin's 'settings' table.
     * @return string|null
     */
    public function getResultPageId()
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'settings';

        $sql = "SELECT setting_value FROM $tableName WHERE setting_key = 'resultPageId'";

        return $wpdb->get_var($sql);
    }

    /**
     * Saves the 'resultPageId' in the plugin's 'settings' table.
     * @param $pageId
     */
    public function addResultPageId($pageId)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'settings';

        $wpdb->insert($tableName, ['setting_key' => 'resultPageId', 'setting_value' => $pageId]);
    }

    /**
     * Removes the 'resultPageId' in the plugin's 'settings' table.
     */
    public function removeResultPageId()
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'settings';

        $wpdb->delete($tableName, ['setting_key' => 'resultPageId']);
    }

    /**
     * Returns the 'paymentSuccessPageId' from the plugin's 'settings' table.
     * @return string|null
     */
    public function getPaymentSuccessPageId()
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'settings';

        $sql = "SELECT setting_value FROM $tableName WHERE setting_key = 'paymentSuccessPageId'";

        return $wpdb->get_var($sql);
    }

    /**
     * Saves the 'paymentSuccessPageId' in the plugin's 'settings' table.
     * @param $pageId
     */
    public function addPaymentSuccessPageId($pageId)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'settings';

        $wpdb->insert($tableName, ['setting_key' => 'paymentSuccessPageId', 'setting_value' => $pageId]);
    }

    /**
     * Removes the 'paymentSuccessPageId' in the plugin's 'settings' table.
     */
    public function removePaymentSuccessPageId()
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'settings';

        $wpdb->delete($tableName, ['setting_key' => 'paymentSuccessPageId']);
    }

    /**
     * Returns the 'paymentErrorPageId' from the plugin's 'settings' table.
     * @return string|null
     */
    public function getPaymentErrorPageId()
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'settings';

        $sql = "SELECT setting_value FROM $tableName WHERE setting_key = 'paymentErrorPageId'";

        return $wpdb->get_var($sql);
    }

    /**
     * Saves the 'paymentErrorPageId' in the plugin's 'settings' table.
     * @param $pageId
     */
    public function addPaymentErrorPageId($pageId)
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'settings';

        $wpdb->insert($tableName, ['setting_key' => 'paymentErrorPageId', 'setting_value' => $pageId]);
    }

    /**
     * Removes the 'paymentErrorPageId' in the plugin's 'settings' table.
     */
    public function removePaymentErrorPageId()
    {
        global $wpdb;

        $tableName = MyBookingsRES_DB::getTablePrefix() . 'settings';

        $wpdb->delete($tableName, ['setting_key' => 'paymentErrorPageId']);
    }

    /**
     * Returns table names like the given parameter if they exists.
     * @param $tableName
     * @return string|null
     */
    public function checkIfTableExists($tableName)
    {
        global $wpdb;

        return $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $tableName));
    }
}