<?php

/**
 * @package MyBookingsRESPlugin
 */

require_once plugin_dir_path(__FILE__) . 'backend/DB.php';
require_once plugin_dir_path(__FILE__) . 'backend/DBSync.php';

class MyBookingsRESPlugin {

    /**
     * Initializes the plugin on activation.
     */
    public function activatePlugin()
    {
        $MyBookingsRES_DBSync = new MyBookingsRES_DBSync();

        $MyBookingsRES_DBSync->addOrUpdateTables();
        $MyBookingsRES_DBSync->createResultPages();
        $MyBookingsRES_DBSync->createPaymentReturnPages();
        $MyBookingsRES_DBSync->addOrUpdateAreas();
        $MyBookingsRES_DBSync->addOrUpdateLocations();
    }

    public function deactivatePlugin()
    {

    }

    /**
     * Checks if all required plugins are installed.
     */
    public function checkRequiredPlugins()
    {
        if(!isset($GLOBALS['polylang'])) {
            return false;
        }

        return true;
    }

    /**
     * Adds a link to the main plugin settings page in the admin menu.
     */
    public function addAdminMenuEntry()
    {
        add_menu_page('MyBookingsRES Plugin', 'MyBookingsRES', 'manage_options', 'MyBookingsRESPlugin',
            array($this, 'getAdminIndexFiles'), 'dashicons-cloud', 100);
    }

    /**
     * Returns a setting from the plugin's setting database table by a key.
     * @param $settings
     * @param $key
     * @return string
     */
    static function getSingleValueFromSettings($settings, $key)
    {
        if(isset($settings[$key])) {
            return $settings[$key]->setting_value;
        }

        return "";
    }

    /**
     * Builds plugin's main settings page on the wordpress backend.
     */
    public function getAdminIndexFiles()
    {
        $db = new MyBookingsRES_DB();

        // translation text example: esc_html_e( 'Settings page', 'MyBookingsRESPlugin' );

        // Double check user capabilities
        if ( !current_user_can('manage_options') || !is_admin() ) {
            return;
        }

        $requiredPluginsInstalled = $this->checkRequiredPlugins();

        $storedSettings = $db->getSettings();

        $color1 = MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "color1") == "" ? "#0079c8" : MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "color1");
        $color2 = MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "color2") == "" ? "#5ac4ff" : MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "color2");
        $color3 = MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "color3") == "" ? "#715aff" : MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "color3");
        $color4 = MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "color4") == "" ? "#ac4747" : MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "color4");
        $color_bglist = MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "color_bglist") == "" ? "#ffffff" : MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "color_bglist");

        $showAreasSetting = $db->getSetting('showAreas');
        $hideChildrenAgesSetting = $db->getSetting('hideChildrenAges');
        $sortCategories = $db->getSetting('sortCategories');
        $showFilter = $db->getSetting('showFilter');
        $hideUnavailableCategories = $db->getSetting('hideUnavailableCategories');

        ?>
        <div class="wrap">
            <h1><?php echo ___('MyBookingsRES Einstellungen') ?></h1>
            <div class="mybookingsres-card mt-3">
                <div class="mybookingsres-card-header mybookingsres-d-flex mybookingsres-align-items-center mybookingsres-justify-content-between">
                    <h3 class="m-0"><?php echo ___('Schritt 1: Grundeinstellungen') ?></h3>
                </div>
                <div class="mybookingsres-card-body">
                    <div class="mybookingsres-row">
                        <label class="mybookingsres-label mybookingsres-col-3" for="MyBookingsRES-api-key"><?php echo ___('API-Key') ?></label>
                        <input class="mybookingsres-input mybookingsres-col-9" id="MyBookingsRES-api-key" type="text" value="<?php echo MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "apiKey"); ?>">
                    </div>

                    <div class="mybookingsres-row mt-3">
                        <label class="mybookingsres-label mybookingsres-col-3" for="MyBookingsRES-website-config-id"><?php echo ___('Webseitenkonfiguration') ?></label>
                        <input class="mybookingsres-input mybookingsres-col-9" id="MyBookingsRES-website-config-id" type="text" value="<?php echo MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "websiteConfigId"); ?>">
                    </div>

                    <div class="mybookingsres-row mt-3">
                        <label class="mybookingsres-label mybookingsres-col-3" for="MyBookingsRES-accommodation-type"><?php echo ___('Unterkunftsart') ?></label>
                        <select class="mybookingsres-input mybookingsres-col-9" id="MyBookingsRES-accommodation-type">
                            <option value="" <?php echo MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "accommodationType") == "" ? " selected" : ""; ?>></option>
                            <option value="Z" <?php echo MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "accommodationType") == "Z" ? " selected" : ""; ?>><?php echo ___('Zimmer') ?></option>
                            <option value="A" <?php echo MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "accommodationType") == "A" ? " selected" : ""; ?>><?php echo ___('Apartments') ?></option>
                        </select>
                    </div>

                    <div class="mybookingsres-row mt-3">
                        <label class="mybookingsres-label mybookingsres-col-3" for="MyBookingsRES-google-apikey"><?php echo ___('Google API Key') ?></label>
                        <input class="mybookingsres-input mybookingsres-col-9" id="MyBookingsRES-google-apikey" type="text" value="<?php echo MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "googleAPIKey"); ?>">
                    </div>

                    <div class="mybookingsres-row mt-3">
                        <label class="mybookingsres-label mybookingsres-col-3" for="MyBookingsRES-color1"><?php echo ___('Farbe 1') ?></label>
                        <input class="mybookingsres-input mybookingsres-col-6" id="MyBookingsRES-color1" type="text" value="<?php echo $color1; ?>">
                        <input class="mybookingsres-input mybookingsres-col-3" readonly type="text" style="background-color:<?= $color1;?>;">
                    </div>

                    <div class="mybookingsres-row mt-3">
                        <label class="mybookingsres-label mybookingsres-col-3" for="MyBookingsRES-color2"><?php echo ___('Farbe 2') ?></label>
                        <input class="mybookingsres-input mybookingsres-col-6" id="MyBookingsRES-color2" type="text" value="<?php echo $color2; ?>">
                        <input class="mybookingsres-input mybookingsres-col-3" readonly type="text" style="background-color:<?= $color2;?>;">
                    </div>

                    <div class="mybookingsres-row mt-3">
                        <label class="mybookingsres-label mybookingsres-col-3" for="MyBookingsRES-color3"><?php echo ___('Farbe 3') ?></label>
                        <input class="mybookingsres-input mybookingsres-col-6" id="MyBookingsRES-color3" type="text" value="<?php echo $color3; ?>">
                        <input class="mybookingsres-input mybookingsres-col-3" readonly type="text" style="background-color:<?= $color3;?>;">
                    </div>

                    <div class="mybookingsres-row mt-3">
                        <label class="mybookingsres-label mybookingsres-col-3" for="MyBookingsRES-color4"><?php echo ___('Farbe 4 (Nicht verfügbar)') ?></label>
                        <input class="mybookingsres-input mybookingsres-col-6" id="MyBookingsRES-color4" type="text" value="<?php echo $color4; ?>">
                        <input class="mybookingsres-input mybookingsres-col-3" readonly type="text" style="background-color:<?= $color4;?>;">
                    </div>

                    <div class="mybookingsres-row mt-3">
                        <label class="mybookingsres-label mybookingsres-col-3" for="MyBookingsRES-color_bglist"><?php echo ___('Hintergrundfarbe Listeneintrag') ?></label>
                        <input class="mybookingsres-input mybookingsres-col-6" id="MyBookingsRES-color_bglist" type="text" value="<?php echo $color_bglist; ?>">
                        <input class="mybookingsres-input mybookingsres-col-3" readonly type="text" style="background-color:<?= $color_bglist;?>;">
                    </div>

                    <div class="mybookingsres-row mt-3 mybookingsres-align-items-center">
                        <label class="mybookingsres-label mybookingsres-col-3" for="MyBookingsRES-show-area-in-search"><?php echo ___('Regionsfeld beim Buchungskalendar anzeigen') ?></label>
                        <input class="mybookingsres-input" id="MyBookingsRES-show-area-in-search" type="checkbox" <?php echo ($showAreasSetting === '1' ? 'checked' : '');?>>
                    </div>

                    <div class="mybookingsres-row mt-3 mybookingsres-align-items-center">
                        <label class="mybookingsres-label mybookingsres-col-3" for="MyBookingsRES-show-children-in-search"><?php echo ___('Alter der Kinder beim Buchungskalendar verstecken') ?></label>
                        <input class="mybookingsres-input" id="MyBookingsRES-show-children-in-search" type="checkbox" <?php echo ($hideChildrenAgesSetting === '1' ? 'checked' : '');?>>
                    </div>

                    <div class="mybookingsres-row mt-3 mybookingsres-align-items-center">
                        <label class="mybookingsres-label mybookingsres-col-3" for="MyBookingsRES-sort-categories-by-sort-order"><?php echo ___('Kategorien im Suchergebnis nicht zufällig sortieren') ?></label>
                        <input class="mybookingsres-input" id="MyBookingsRES-sort-categories-by-sort-order" type="checkbox" <?php echo ($sortCategories === '1' ? 'checked' : '');?>>
                    </div>

                    <div class="mybookingsres-row mt-3 mybookingsres-align-items-center">
                        <label class="mybookingsres-label mybookingsres-col-3" for="MyBookingsRES-show-category-page-filter"><?php echo ___('Attribut- und Standort-Filter in Kategorieliste anzeigen') ?></label>
                        <input class="mybookingsres-input" id="MyBookingsRES-show-category-page-filter" type="checkbox" <?php echo ($showFilter === '1' ? 'checked' : '');?>>
                    </div>

                    <div class="mybookingsres-row mt-3 mybookingsres-align-items-center">
                        <label class="mybookingsres-label mybookingsres-col-3" for="MyBookingsRES-hide-unavailable-categories"><?php echo ___('Nicht verfügbare Kategorien ausblenden') ?></label>
                        <input class="mybookingsres-input" id="MyBookingsRES-hide-unavailable-categories" type="checkbox" <?php echo ($hideUnavailableCategories === '1' ? 'checked' : '');?>>
                    </div>

                    <div class="mybookingsres-row mt-3">
                        <label class="mybookingsres-label mybookingsres-col-3" for="MyBookingsRES-custom_css"><?php echo ___('CSS') ?></label>
                        <textarea class="mybookingsres-input mybookingsres-col-9" id="MyBookingsRES-custom_css" style="height:200px;"><?php echo MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "custom_css"); ?></textarea>
                    </div>

                    <div class="mybookingsres-d-flex mybookingsres-align-items-center mybookingsres-justify-content-start mt-5">
                        <button id="MyBookingsRES_settings_save" class="mybookingsres-btn mybookingsres-btn-primary"><?php echo ___('Speichern') ?></button></span>
                    </div>
                </div>
            </div>
            <?php
            if($requiredPluginsInstalled) {
                $availableLanguages = pll_languages_list();

                if (empty(MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "apiKey")) ||
                        empty(MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "websiteConfigId")) ||
                            empty(MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "accommodationType")))
                {
                    ?>
                    <div class="mybookingsres-warning">
                        <?php esc_html_e('Bitte Grundeinstellungen ausfüllen.', 'MyBookingsRESPlugin') ?>
                    </div>
                    <?php
                } else {
                    $storedCategories = $db->getShortCategoriesInfo()
                    ?>
                        <div class="mybookingsres-card mt-3">
                            <div class="mybookingsres-card-header mybookingsres-d-flex mybookingsres-align-items-center mybookingsres-justify-content-between">
                                <h3 class="m-0"><?php echo ___('Schritt 2: Texte und Bilder abgleichen') ?></h3>
                            </div>
                            <div class="mybookingsres-card-body">
                                <div style="text-align:right;">
                                    <button id="MyBookingsRESAdmin_initSync" class="mybookingsres-btn mybookingsres-btn-primary"><?php echo ___('Abgleich starten') ?></button>
                                </div>
                                <div id="MyBookingsRESAdmin_infoDeleteUnusedCategories" class="mybookingsres-dontShow">
                                    <div class="mybookingsres-row mybookingsres-row1">
                                        <div class="mybookingsres-col-8"><?php echo ___('Nicht mehr benötigte Kategorien werden entfernt ... ') ?></div>
                                        <div class="mybookingsres-col-4"><span class="MyBookingsRESAdmin_isDone mybookingsres-dontShow"><?php echo ___('ERLEDIGT') ?></span></div>
                                    </div>
                                </div>
                                <div id="MyBookingsRESAdmin_categoryStatus" class="mybookingsres-row-list"></div>

                                <div id="MyBookingsRESAdmin_syncStoredCategories" class="mybookingsres-row-list">
                                    <h3><?php echo ___('Aktueller Stand') . ' - ' . count($storedCategories); ?></h3>
                                    <?php
                                        foreach ($storedCategories as $sc) {
                                            ?>
                                        <div class="mybookingsres-row mybookingsres-row1">
                                            <div class="mybookingsres-col-8"><?php echo $sc->ndesc_de; ?></div>
                                            <div class="mybookingsres-col-4"><?php echo ___('Letzter Abgleich') . ': ' . $sc->last_changed; ?></div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>


                        <div class="mybookingsres-card mt-3">
                            <div class="mybookingsres-card-header mybookingsres-d-flex mybookingsres-align-items-center mybookingsres-justify-content-between">
                                <h3 class="m-0"><?php echo ___('Schritt 3: Seiten für Sprachen verwalten') ?></h3>
                            </div>
                            <div class="mybookingsres-card-body">
                                <div style="margin-bottom: 1rem;"><?php echo ___('Aktive Polylang Sprachen') . ': ' . json_encode($availableLanguages) ?></div>

                                <div>
                                    <button id="MyBookingsRES_createResultPages" class="mybookingsres-btn mybookingsres-btn-primary"><?php echo ___('Ergebnisseiten anlegen') ?></button>
                                    <button id="MyBookingsRES_createPaymentReturnPages" class="mybookingsres-btn mybookingsres-btn-primary"><?php echo ___('Zahlungs-Ergebnisseiten anlegen') ?></button>
                                    <button id="MyBookingsRESAdmin_createPagesButton" class="mybookingsres-btn mybookingsres-btn-primary"><?php echo ___('Kategorieseiten anlegen') ?></button>
                                    <button id="MyBookingsRES_removeCategoryPages" class="mybookingsres-btn mybookingsres-btn-primary"><?php echo ___('Kategorieseiten löschen') ?></button>
                                </div>
                            </div>
                        </div>


                        <div class="mybookingsres-card mt-3">
                            <div class="mybookingsres-card-header mybookingsres-d-flex mybookingsres-align-items-center mybookingsres-justify-content-between">
                                <h3 class="m-0"><?php echo ___('Schritt 4: Filter für Attribute verwalten') ?></h3>
                            </div>
                            <div class="mybookingsres-card-body">
                                <div><?php echo ___('Einstellungen je Region') ?></div>

                                <div class="mybookingsres-attribute-filter-container mt-3">
                                    <?php

                                    $html = "";

                                    $areas = $db->getAreas();

                                    foreach ($areas as $area) {
                                        $html .= "
                                            <div class='mybookingsres-area-item'>
                                                <div id='{$area['area_id']}' class='mybookingsres-area-header mybookingsres-attributes-filter'>
                                                    {$area['area_name']}
                                                </div>
                                                <div class='mybookingsres-area-body'></div>
                                            </div>
                                        ";
                                    }

                                    echo $html;
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="mybookingsres-card mt-3">
                            <div class="mybookingsres-card-header mybookingsres-d-flex mybookingsres-align-items-center mybookingsres-justify-content-between">
                                <h3 class="m-0"><?php echo ___('Schritt 5: Filter für Entfernungen verwalten') ?></h3>
                            </div>
                            <div class="mybookingsres-card-body">
                                <div><?php echo ___('Einstellungen je Region') ?></div>

                                <div class="mybookingsres-distance-filter-container mt-3">
                                    <?php

                                    $html = "";

                                    $areas = $db->getAreas();

                                    foreach ($areas as $area) {
                                        $html .= "
                                            <div class='mybookingsres-area-item'>
                                                <div id='{$area['area_id']}' class='mybookingsres-area-header mybookingsres-distances-filter'>
                                                    {$area['area_name']}
                                                </div>
                                                <div class='mybookingsres-area-body'></div>
                                            </div>
                                        ";
                                    }

                                    echo $html;
                                    ?>
                                </div>
                            </div>
                        </div>


                        <div class="mybookingsres-card mt-3">
                            <div class="mybookingsres-card-header mybookingsres-d-flex mybookingsres-align-items-center mybookingsres-justify-content-between">
                                <h3 class="m-0"><?php echo ___('CSS Styles') ?></h3>
                            </div>
                            <div class="mybookingsres-card-body">

                            </div>
                        </div>



                    <div class="mybookingsres-loader">
                        <div class="mybookingsres-spinner"></div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="mybookingsres-warning">
                    <?php esc_html_e('Bitte die erforderlichen Plugins: "Polylang" aktivieren.', 'MyBookingsRESPlugin') ?>
                </div>
                <?php
            }
            ?>

        </div>
        <?php
    }

    /**
     * Returns the short code content for '[MyBookingsRES-Search]'.
     * @param $attributesRaw
     * @return string
     * @deprecated Newer version: getSearchShortCodeContent()
     */
    public function getSearchShortCodeContentOld($attributesRaw)
    {
        return
            '
                <div class="mybookingsres-bookings-search-container-old" style="background: #878787; padding: 10px;">
                    <div class="mybookingsres-right-border">
                        <div class="mybookingsres-bookings-search-container-label-old">
                            Select Region
                        </div>
                        <select id="MyBookingsRES-selectedRegion">
                            <option selected value="vienna">Wien</option>
                        </select>
                    </div>
                    <div class="mybookingsres-right-border">
                        <div class="mybookingsres-bookings-search-container-label-old">
                            Select booking date range
                        </div>
                        <input id="MyBookingsRES-selectedBookingDateRangeInput" type="text" placeholder="Select Date Range">
                    </div>
                    <div>
                        <div class="mybookingsres-bookings-search-container-label-old">
                            Select total persons
                        </div>
                        <input id="MyBookingsRES-selectedPersonCount" type="number" value="2">
                    </div>
                    <div id="MyBookingsRES-searchBookings" class="mybookingsres-full-height">
                        <button>Check availability</button>
                    </div>
                </div>
            ';
    }

    /**
     * Returns the short code content for '[MyBookingsRES-Search]'.
     * @param $attributesRaw
     * @param $searchParameter array
     * @return string
     * @throws Exception
     */
    public function getSearchShortCodeContent($attributesRaw, $searchParameter = null)
    {
        $attributes = shortcode_atts(['size' => null], $attributesRaw);
        $size = $attributes['size'];

        $db = new MyBookingsRES_DB();

        $areas = $db->getAreas();

        $hasNoAreas = count($areas) === 0;

        $showAreasSetting = (boolean) $db->getSetting('showAreas');
        $hideChildrenAgesSetting = (boolean) $db->getSetting('hideChildrenAges');

        $germanMonthNames = [
            'Jän', 'Feb', 'Mrz', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'
        ];

        $initialFrom = new DateTime('now');
        $initialFromDay = $initialFrom->format('d');
        $initialFromMonth = $germanMonthNames[((int) $initialFrom->format('m')) - 1];
        $initialTo = (new DateTime('now'))->add(new DateInterval('P7D'));
        $initialToDay = $initialTo->format('d');
        $initialToMonth = $germanMonthNames[((int) $initialTo->format('m')) - 1];


        if ($size === null) {
            $size = 'sm';
        }

        if ($size === 'sm') {

            if ($hasNoAreas || !$showAreasSetting) {

                $contentSm = '
                    <div class="MyBookingsRES-search-container-no-area">
                ';

            } else {

                $contentSm = '
                <div class="MyBookingsRES-search-container">
                    <div class="MyBookingsRES-search-item">
                        <select id="MyBookingsRES-search-container-region">';
                    foreach ($areas as $area) {
                        $contentSm .= "<option value='{$area['area_id']}'>{$area['area_name']}</option>";
                    }
                    $contentSm .=
                        '</select>
                    </div>
            ';

            }

            $contentSm .= '
                    <div class="MyBookingsRES-search-item" id="MyBookingsRES-search-box-dates">
                        <img src="'. MyBookingsRESPlugin::getIconUrl("calendar") .'" alt="calendar">';
            if (!$searchParameter) {
                $contentSm .= '<div id="MyBookingsRES-search-box-from">'. ___('ANREISE') .'</div>';
            } else {
                $contentSm .= '<div id="MyBookingsRES-search-box-from">'. $searchParameter['from'] .'</div>';
            }
            $contentSm .= '<div style="margin-bottom: 3px;">&rarr;</div>';
            if (!$searchParameter) {
                $contentSm .= '<div id="MyBookingsRES-search-box-to">'. ___('ABREISE') .'</div>';
            } else {
                $contentSm .= '<div id="MyBookingsRES-search-box-to">'. $searchParameter['to'] .'</div>';
            }
            $contentSm .= '</div>';

            if ($hideChildrenAgesSetting) {

                $contentSm .= '
                    <div class="MyBookingsRES-search-item MyBookingsRES-search-guests-container">
                        <img src="'. MyBookingsRESPlugin::getIconUrl("family") .'" alt="family">
                        <div>'. ___('GÄSTE') .'</div>
                        <div class="MyBookingsRES-search-guests-counter">
                            <button id="MyBookingsRES-search-box-guests-minus">-</button>
                            <div id="MyBookingsRES-search-box-guests-result">2</div>
                            <button id="MyBookingsRES-search-box-guests-plus">+</button>
                        </div>
                    </div>
                ';

            } else {
                $contentSm .= '
                    <div class="MyBookingsRES-search-item MyBookingsRES-search-box-people-field-onclick" id="MyBookingsRES-search-box-people">
                        <img src="'. MyBookingsRESPlugin::getIconUrl("family") .'" alt="family">
                        <div id="MyBookingsRES-search-box-adults">2</div><div>'. ___('Erwachsene') .'</div>
                        <div>-</div>
                        <div id="MyBookingsRES-search-box-children">0</div><div>'. ___('Kinder') .'</div>
                        
                        <div class="MyBookingsRES-search-box-people-field">
                            <div id="MyBookingsRES-search-box-people-dropdown">
                                <div class="MyBookingsRES-search-box-people-dropdown-counter">
                                    <div>'. ___('Erwachsene') .'</div>
                                    <button id="MyBookingsRES-search-box-adults-minus">-</button>
                                    <div id="MyBookingsRES-search-box-adults-result">2</div>
                                    <button id="MyBookingsRES-search-box-adults-plus">+</button>
                                </div>
                                <div class="MyBookingsRES-search-box-people-dropdown-counter">
                                    <div>'. ___('Kinder') .'</div>
                                    <button id="MyBookingsRES-search-box-children-minus">-</button>
                                    <div id="MyBookingsRES-search-box-children-result">0</div>
                                    <button id="MyBookingsRES-search-box-children-plus">+</button>
                                </div>
                                <div class="MyBookingsRES-search-box-children-ages"></div>
                            </div>
                        </div>
                    </div>';
            }

            $contentSm .= '
                    <div class="MyBookingsRES-search-item MyBookingsRES-no-border">
                        <button id="MyBookingsRES-search-submit">
                            <img src="'. MyBookingsRESPlugin::getIconUrl("search") .'" alt="search">
                            <div>'. ___('Verfügbarkeiten Suchen') .'</div>
                        </button>
                    </div>
                </div>
            ';

            return $contentSm;
        }

        if ($size === 'bg') {

            if ($hasNoAreas || !$showAreasSetting) {

                $contentBg = '
                <div class="MyBookingsRES-search-container-bg-no-area">
                ';

            } else {

                $contentBg = '
                <div class="MyBookingsRES-search-container-bg">
                    <div class="MyBookingsRES-search-item-bg">
                        <label for="MyBookingsRES-search-container-bg-region">'. ___('REGION') .'</label>
                        <select id="MyBookingsRES-search-container-bg-region">';
                        foreach ($areas as $area) {
                            $contentBg .= "<option value='{$area['area_id']}'>{$area['area_name']}</option>";
                        }
                        $contentBg .=
                        '</select>
                    </div>
                ';

            }

            $contentBg .= '
                    <div class="MyBookingsRES-search-item-bg MyBookingsRES-search-box-bg-date-from">
                        <label>'. ___('CHECK-IN') .'</label>
                        <div class="MyBookingsRES-search-box-bg-date-picker">
                            <div id="MyBookingsRES-search-box-bg-checkin-day">'. $initialFromDay .'</div>
                            <div>
                                <div id="MyBookingsRES-search-box-bg-checkin-month">'. $initialFromMonth .'</div>
                                <div>
                                    <img src="'. MyBookingsRESPlugin::getIconUrl("chevron-down") .'" alt="chevron-down">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="MyBookingsRES-search-item-bg MyBookingsRES-search-box-bg-date-to">
                        <label>'. ___('CHECK-OUT') .'</label>
                        <div class="MyBookingsRES-search-box-bg-date-picker">
                            <div id="MyBookingsRES-search-box-bg-checkout-day">'. $initialToDay .'</div>
                            <div>
                                <div id="MyBookingsRES-search-box-bg-checkout-month">'. $initialToMonth .'</div>
                                <div>
                                    <img src="'. MyBookingsRESPlugin::getIconUrl("chevron-down") .'" alt="chevron-down">
                                </div>
                            </div>
                        </div>
                    </div>';

            if ($hideChildrenAgesSetting) {

                $contentBg .= '
                    <div class="MyBookingsRES-search-item-bg">
                        <label>'. ___('GÄSTE') .'</label>
                        <div class="MyBookingsRES-search-box-bg-guests-counter">
                            <div>
                                <img src="'. MyBookingsRESPlugin::getIconUrl("family") .'" alt="family">
                            </div>
                            <div>
                                <button id="MyBookingsRES-search-box-bg-guests-minus">-</button>
                                <div id="MyBookingsRES-search-box-bg-guests-result">2</div>
                                <button id="MyBookingsRES-search-box-bg-guests-plus">+</button>
                            </div>
                        </div>
                    </div>
                ';

            } else {
                $contentBg .= '
                    <div class="MyBookingsRES-search-item-bg MyBookingsRES-search-box-bg-people-onclick" id="MyBookingsRES-search-box-bg-people">
                        <label>'. ___('GÄSTE') .'</label>
                        <div class="MyBookingsRES-search-box-bg-people-counter">
                            <div>
                                <img src="'. MyBookingsRESPlugin::getIconUrl("family") .'" alt="family">
                            </div>
                            <div>
                                <div>
                                    <div id="MyBookingsRES-search-box-bg-adults">2</div><div>&nbsp;'. ___('Erwachsene') .'</div>
                                </div>
                                <div>
                                    <div id="MyBookingsRES-search-box-bg-children">0</div><div>&nbsp;'. ___('Kinder') .'</div>
                                </div>
                            </div>
                        </div>
                        <div class="MyBookingsRES-search-box-bg-people-dropdown-parent">
                            <div id="MyBookingsRES-search-box-bg-people-dropdown">
                                <div class="MyBookingsRES-search-box-bg-people-dropdown-counter">
                                    <div>'. ___('Erwachsene') .'</div>
                                    <button id="MyBookingsRES-search-box-bg-adults-minus">-</button>
                                    <div id="MyBookingsRES-search-box-bg-adults-result">2</div>
                                    <button id="MyBookingsRES-search-box-bg-adults-plus">+</button>
                                </div>
                                <div class="MyBookingsRES-search-box-bg-people-dropdown-counter">
                                    <div>'. ___('Kinder') .'</div>
                                    <button id="MyBookingsRES-search-box-bg-children-minus">-</button>
                                    <div id="MyBookingsRES-search-box-bg-children-result">0</div>
                                    <button id="MyBookingsRES-search-box-bg-children-plus">+</button>
                                </div>
                                <div class="MyBookingsRES-search-box-bg-children-ages"></div>
                            </div>
                        </div>
                    </div>
                ';
            }

            $contentBg .= '
                <div class="MyBookingsRES-search-item-bg">
                        <button id="MyBookingsRES-search-submit-bg">
                            <img src="'. MyBookingsRESPlugin::getIconUrl("search") .'" alt="search">
                            <div>'. ___('Verfügbarkeiten Suchen') .'</div>
                        </button>
                    </div>
                </div>
            ';
            return $contentBg;
        }
    }

    /**
     * Returns a text from the plugin's 'attributes' database table.
     * @param $attr
     * @param $lang
     * @return mixed
     */
    static function getAttributeText($attr, $lang)
    {
        $availLangs = [ "en", "de", "es" ];

        if (!in_array($lang, $availLangs) || empty($attr["desc_" . $lang])) {
            return $attr["desc_en"];
        }

        return $attr["desc_" . $lang];
    }

    /**
     * Returns a text from the plugin's 'distances' database table.
     * @param $dist
     * @param $lang
     * @return mixed
     */
    static function getDistanceText($dist, $lang)
    {
        $availLangs = [ "en", "de", "es" ];

        if (!in_array($lang, $availLangs) || empty($dist["label_" . $lang])) {
            return $dist["label_en"];
        }

        return $dist["label_" . $lang];
    }

    /**
     * Returns a value from the plugin's 'distances' database table.
     * @param $value
     * @return string
     */
    static function getDistanceValue($value) {
        if((int) $value >= 1000) {
            return number_format(($value/1000), 2, ",", "") . "km";
        }

        return $value . "m";
    }

    /**
     * Builds and returns the full path for an plugin's 'attributes' - icon.
     * @param $attrId
     * @return string
     */
    static function getAttributeIconUrl($attrId)
    {
        /*
        if (!is_file(MB_PLUGIN_PATH . 'includes\media\attributes\\' . ((int)$attrId) . '.svg')) {
            return MB_PLUGIN_URL . "includes/media/attributes/missing.svg";
        } */

        return MB_PLUGIN_URL . "includes/media/attributes/" . ((int)$attrId) . ".svg";
    }

    /**
     * Builds and returns the full path for an icon.
     * @param $name
     * @return string
     */
    static function getIconUrl($name)
    {
        return MB_PLUGIN_URL . "includes/media/icons/" . $name . ".svg";
    }

    /**
     * Returns the short code content for '[MyBookingsRES-Category]'.
     * @param $attributesRaw
     * @return string|null
     */
    public function getCategoryShortCodeContent($attributesRaw)
    {
        $attributes = shortcode_atts(['id' => null], $attributesRaw);
        $categoryId = $attributes['id'];
        $currentLanguage = pll_current_language();

        $currentLang = "de";

        if ($categoryId === null) {
            return null;
        }

        $db = new MyBookingsRES_DB();

        $searchParameters = $this->getSearchUrlParameters();

        $from = $searchParameters->from;
        $to = $searchParameters->to;
        $people = $searchParameters->people;
        $area = $searchParameters->area;

        $category = $db->getCategoryById($categoryId);

        $pricePerNight = $category['min_price'];

        $images = $db->getImagesByCategoryId($categoryId);

        $attributeGroups = $db->getAttributeGroups();
        usort($attributeGroups, function ($a, $b) {
            $groupNameA = (string) $a['group_id'];
            $groupNameB = (string) $b['group_id'];

            return strcmp($groupNameA, $groupNameB);
        });

        $topAttributes = $db->getAttributes($categoryId, 1);

        $distances = $db->getDistancesForCategory($categoryId);

        $baseImageUrl900 = 'https://www.my-bookings.cc/everest/photo/W900/H600/C1/';
        $baseImageUrl400 = 'https://www.my-bookings.cc/everest/photo/W400/H266/C1/';
        $baseImageUrl200 = 'https://www.my-bookings.cc/everest/photo/W200/H133/C1/';

        $text = $db->getText($categoryId, $currentLanguage);

        $lat = $category['lat'];
        $lon = $category['lon'];
        $googleMapsUrl = "https://maps.google.com/maps?q=$lat,$lon&hl=$currentLanguage&z=14&amp;output=embed";

        if (!$text) {
            $text = $db->getText($categoryId, $currentLanguage);
        }

        $html = "
            <div class='mybookingsres-category-page'>
                <div class='mybookingsres-category-container'>
                    <div class='mybookingsres-category-header'>
                        <div class='mybookingsres-category-header-attr'>";
                            if (is_array($topAttributes)) {
                                foreach ($topAttributes as $attr) {
                                    $html .= "<img src='" . MyBookingsRESPlugin::getAttributeIconUrl($attr["attribut_id"]) . "'  title='" . MyBookingsRESPlugin::getAttributeText($attr, $currentLang) . "'>";
                                }
                            }

        $html .= "
                        </div>
                    </div>
                    <div class='mybookingsres-category-body'>
                        <div class='mybookingsres-category-gallery'>
                            <div class='mybookingsres-category-gallery-big-images'>
                                <div class='mybookingsres-category-gallery-primary-image'>
                                    <div class='mybookingsres-category-gallery-image-container' data-imgpos='1'><img src='" . $baseImageUrl900 . $images[0]['image_id'] . "' alt='{$images[0]['image_id']}'></div>
                                    <div class='mybookingsres-category-gallery-primary-image-overlay'>
                                        <img src='" . MyBookingsRESPlugin::getIconUrl('fullscreen') ."' alt=fullscreen>
                                    </div>
                                </div>
                                <div class='mybookingsres-category-gallery-secondary-images'>
                                    <div class='mybookingsres-category-gallery-image-container' data-imgpos='2'><img src='" . $baseImageUrl400 . $images[1]['image_id'] . "' alt='{$images[1]['image_id']}'></div>
                                    <div class='mybookingsres-category-gallery-image-container' data-imgpos='3'><img src='" . $baseImageUrl400 . $images[2]['image_id'] . "' alt='{$images[2]['image_id']}'></div>
                                </div>
                            </div>
                            <div class='mybookingsres-category-gallery-small-images'>
                                    <div class='mybookingsres-category-gallery-image-container' data-imgpos='4'><img src='" . $baseImageUrl200 . $images[3]['image_id'] . "' alt='{$images[3]['image_id']}'></div>
                                    <div class='mybookingsres-category-gallery-image-container' data-imgpos='5'><img src='" . $baseImageUrl200 . $images[4]['image_id'] . "' alt='{$images[4]['image_id']}'></div>
                                    <div class='mybookingsres-category-gallery-image-container' data-imgpos='6'><img src='" . $baseImageUrl200 . $images[5]['image_id'] . "' alt='{$images[5]['image_id']}'></div>
                                    <div class='mybookingsres-category-gallery-image-container' data-imgpos='7'><img src='" . $baseImageUrl200 . $images[6]['image_id'] . "' alt='{$images[6]['image_id']}'></div>
                                    <div class='mybookingsres-category-gallery-image-container' data-imgpos='8'><img src='" . $baseImageUrl200 . $images[7]['image_id'] . "' alt='{$images[7]['image_id']}'></div>
                            </div>
                        </div>
  
                        <div class='mybookingsres-category-section mybookingsres-first'>
                            <div class='mybookingsres-category-section-inner'>
                                <h5>". ___('Buchen') ."</h5>
                                <div class=\"mybookingsres-list-searchbox-container mybookingsres-list-searchbox-container-detailpage\">
                                    <div class=\"mybookingsres-list-searchbox\">
                                        <div class=\"mybookingsres-list-searchbox-item mybookingsres-list-searchbox-item-date\">
                                            <input type=\"text\" id=\"mybookingsres-list-searchbox-date\" placeholder=\"". ___('Anreise - Abreise') ."\">
                                        </div>
                                        <div class=\"mybookingsres-list-searchbox-item mybookingsres-list-searchbox-item-people\">
                                            <select id=\"mybookingsres-list-searchbox-people\">";

                                            for($i = 1; $i < 20; $i++) {
                                                $html .= '<option value="' . $i . '" ' . ($people == $i ? 'selected' : '') . '>' . $i . ' ' . ($i == 1 ? (" " . ___('Person')) : ___('Personen')) . '</option>';
                                            }
                            $html .= "    
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class='mybookingsres-category-pricedetails'>
                                    <div class='mybookingsres-list-loader'></div>
                                    <div class='mybookingsres-category-pricedetails-inner'>
                                        <div class='mybookingsres-category-pricedetails-list'>
                                            
                                        </div>
                                        <div class='mybookingsres-category-pricedetails-booknow'>
                                            <button data-category='$categoryId' data-available='1' class='MyBookingsRESFrontent_categoryPage_bookNowButton'>". ___('Weiter zur Buchung') ."</button>
                                        </div>
                
                                        <div class='mybookingsres-category-pricedetails-notavailable'>
                                            <button data-category='$categoryId' data-available='0' class='MyBookingsRESFrontent_categoryPage_bookNowButton'>". ___('Nicht verfügbar') ."</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='mybookingsres-category-section'>
                            <div class='mybookingsres-category-section-inner'>
                                <h5>". ___('Beschreibung') ."</h5>
                                <div>
                                    {$text['ndesc']}
                                </div>
                            </div>
                        </div>";

        if (1 == 2) {
            $html .= "      
                        <div class='mybookingsres-category-section'>
                            <div class='mybookingsres-category-section-inner'>
                                <h5>". ___('Verfügbarkeit') ."</h5>
                                <div>
                                    
                                </div>
                            </div>
                        </div>";
        }

        if (1 == 2) {
            $html .= "
                        <div class='mybookingsres-category-section'>
                            <div class='mybookingsres-category-section-inner'>
                                <h5>". ___('Details') ."</h5>
                                <div>
                                    
                                </div>
                            </div>
                        </div>";
        }

        $currentAttributeLanguage = 'en';
        if ($currentLanguage === 'de' || $currentLanguage === 'en' || $currentLanguage === 'es') {
            $currentAttributeLanguage = $currentLanguage;
        }

        $html .= "
                        <div class='mybookingsres-category-section'>
                            <div class='mybookingsres-category-section-inner'>
                                <h5>". ___('Ausstattung') ."</h5>
                                <div class='mybookingsres-category-attribute-container'>";

                                foreach ($attributeGroups as $attributeGroup) {

                                    $attributeGroupName = $attributeGroup['group_name_' . $currentAttributeLanguage];

                                    $attributes = $db->getAttributes($categoryId, $attributeGroup['group_id']);

                                    #print_r($attributes);
                                    if ((int)$attributeGroup['group_id'] === 1 || !is_array($attributes) || count($attributes) == 0) {
                                        continue;
                                    }

                        $html .= "  <div class='mybookingsres-category-attribute-item'>
                                        <div class='mybookingsres-category-attribute-item-title'>$attributeGroupName</div>
                                        <div class='mybookingsres-category-attribute-entry'>";

                                        if (is_array($attributes)) {
                                            foreach ($attributes as $attr) {
                                                $html .= "
                                                    <div class='mybookingsres-category-attribute-entry-item'>
                                                        <img src='" . MyBookingsRESPlugin::getAttributeIconUrl($attr["attribut_id"]) . "' width='20' height='auto' alt='" . $attr["attribut_id"] . "'> <div>" . MyBookingsRESPlugin::getAttributeText($attr, $currentAttributeLanguage) . "</div>
                                                    </div>
                                                ";
                                            }
                                        }

                        $html .= "                      
                                        </div>
                                    </div>
                                    ";
                                }

                    $html .= "  </div>
                            </div>            
                    </div>
                        <div class='mybookingsres-category-section'>
                            <div class='mybookingsres-category-section-inner'>
                                <h5>". ___('Karte') ."</h5>
                                <div>
                                    <iframe 
                                        src='$googleMapsUrl'
                                        width='100%' height='450' frameborder='0' style='border:0;' allowfullscreen='' aria-hidden='false' tabindex='0'>
                                    </iframe>
                                </div>
                            </div>
                        </div>";

                        if (is_array($distances) && count($distances) > 0) {

                            $html .= "
                                <div class='mybookingsres-category-section'>
                                    <div class='mybookingsres-category-section-inner'>
                                        <h5>". ___('Umgebung') ."</h5>
                                        <div>
                                            <ul class='mybookingsres-category-distances'>";

                                                foreach ($distances as $dist) {
                                                    $html .= "<li>" . MyBookingsRESPlugin::getDistanceText($dist, $currentLang) . ": " . MyBookingsRESPlugin::getDistanceValue($dist["value"]) . "</li>";
                                                }

                                $html .= "  </ul>
                                        </div>
                                    </div>
                                </div>";
                        }

                $html .= "  
                    </div>
                </div>
                
               
            </div>

            <script>
                var MyBookingsRes_galleryImages = " . json_encode($images) . ";

                var MyBookingsRes_galleryImagesData = [];
                
                for(var i = 0; i < MyBookingsRes_galleryImages.length; i++) {

                    MyBookingsRes_galleryImagesData.push({
                        url: 'https://www.my-bookings.cc/everest/photo/W1920/H0/' + MyBookingsRes_galleryImages[i].image_id,
                        options: {
                            thumbnail: 'https://www.my-bookings.cc/everest/photo/W240/H240/C1/' + MyBookingsRes_galleryImages[i].image_id,
                            loop: true
                        }
                    });
                }
                
                function MyBookingsRes_openGallery(position) {
                    Fresco.show(MyBookingsRes_galleryImagesData, position);
                }

                MyBookingsRESFrontent_searchParameters.area = \"$area\";
                MyBookingsRESFrontent_searchParameters.from = \"$from\";
                MyBookingsRESFrontent_searchParameters.to =  \"$to\";
                MyBookingsRESFrontent_searchParameters.people = \"$people\";
                MyBookingsRESFrontent_searchParameters.calendarFrom = null;
                MyBookingsRESFrontent_searchParameters.calendarTo = null;
                
                (function($) {

                    $(function() {

                        MyBookingsRESFrontent.DetailPage.currentCategory = $categoryId;
                        
                        MyBookingsRESFrontent.DetailPage.init();

                        $('.mybookingsres-category-gallery-image-container').on('click', function(event) {
                            event.preventDefault();
                            MyBookingsRes_openGallery($(this).data('imgpos'));
                        });

                        MyBookingsRESFrontent.initSearchCalendar(function() {
                            MyBookingsRESFrontent.DetailPage.checkAvailability();
                        });

                        $('#mybookingsres-list-searchbox-people').on('change', function(event) {
                            event.preventDefault();
                            MyBookingsRESFrontent.DetailPage.checkAvailability();
                        });


                    });

                    

                })(jQuery);
            </script>
        ";

        return '<div class="mybookingsres-container">' . $html . "</div>";
    }

    /**
     * Returns the GET parameters of a request.
     * @return stdClass
     */
    public function getSearchUrlParameters()
    {
        $from = MyBookingsRESPluginHelper::checkAndGetQueryVal("f");
        $to = MyBookingsRESPluginHelper::checkAndGetQueryVal("t");
        $people = MyBookingsRESPluginHelper::checkAndGetQueryVal("people");
        $area = MyBookingsRESPluginHelper::checkAndGetQueryVal("a");
        $location = MyBookingsRESPluginHelper::checkAndGetQueryVal("l");

        // hotel template params
        if (!empty(MyBookingsRESPluginHelper::checkAndGetQueryVal("nd_booking_archive_form_date_range_from"))) {

            $from = MyBookingsRESPluginHelper::checkAndGetQueryVal("nd_booking_archive_form_date_range_from");
            $to = MyBookingsRESPluginHelper::checkAndGetQueryVal("nd_booking_archive_form_date_range_to");
            $people = MyBookingsRESPluginHelper::checkAndGetQueryVal("nd_booking_archive_form_guests");

            $arrFrom = explode("/", $from);
            $arrTo = explode("/", $to);

            $from = $arrFrom[1].".".$arrFrom[0].".".$arrFrom[2];
            $to = $arrTo[1].".".$arrTo[0].".".$arrTo[2];

            if(empty($people)) {
                $people = 1;
            }
        }

        // own searchbox
        if (isset($_GET["mbf"])) {
            /*
                mba: area,
                mbf: from date als iso string,
                mbt: to date als iso string,
                mbp: people count (adults + children),
                mbc: children count,
                mbca: children ages mit ',' getrennt. parameter ist nur da wenn children count > 0
            */

            $from = MyBookingsRESPluginHelper::checkAndGetQueryVal("mbf");
            $from = MyBookingsRESPluginHelper::getGermanDateFromISODate($from);
            $to = MyBookingsRESPluginHelper::checkAndGetQueryVal("mbt");
            $to = MyBookingsRESPluginHelper::getGermanDateFromISODate($to);
            $people = MyBookingsRESPluginHelper::checkAndGetQueryVal("mbp");
            $area = MyBookingsRESPluginHelper::checkAndGetQueryVal("mba");
            $location = MyBookingsRESPluginHelper::checkAndGetQueryVal("mbl");
        }

        $result = new stdClass();

        $result->from = $from;
        $result->to = $to;
        $result->people = $people;
        $result->area = $area;
        $result->location = $location;

        return $result;

    }

    /**
     * Returns the short code content for '[MyBookingsRES-Result]'.
     * @return string
     * @throws Exception
     */
    public function getResultShortCodeContent()
    {
        $db = new MyBookingsRES_DB();

        $currentLanguage = pll_current_language();

        $locations = $db->getLocations();

        $searchParameters = $this->getSearchUrlParameters();

        $from = $searchParameters->from;
        $to = $searchParameters->to;
        $people = $searchParameters->people;
        $area = $searchParameters->area;

        $attributes = $db->getAttributesForSearch();

        $storedSettings = $db->getSettings();

        $showFilter = $db->getSetting('showFilter');

        $resultContainerStyle = $showFilter === '1' ? '' : 'style="display: block;"';

        $isSearchUrlParameter = !empty($from) &&
                                    !empty($to) &&
                                        !empty($people);

        $searchBoxSm = $this->getSearchShortCodeContent(null, ['from' => $from, 'to' => $to]);

        $html = $searchBoxSm;

        $html .= '
        <div class="mybookingsres-result-container" '. $resultContainerStyle .'>
            '. $this->getResultShortCodeFilterHtml() .'
        
            <div id="mybookingsres-list-container" class="mybookingsres-result-container-items">
                <div class="mybookingsres-list-searchbox-container">
                    <div class="mybookingsres-list-searchbox" style="display: none;">
                        <div class="mybookingsres-list-searchbox-item mybookingsres-list-searchbox-item-date">
                            <input type="text" id="mybookingsres-list-searchbox-date" placeholder="'. ___('Anreise - Abreise') .'">
                        </div>
                        <div class="mybookingsres-list-searchbox-item mybookingsres-list-searchbox-item-people">
                            <select id="mybookingsres-list-searchbox-people">';
                            for($i = 1; $i < 20; $i++) {
                                $html .= '<option value="' . $i . '" ' . ($people == $i ? 'selected' : '') . '>' . $i . ' ' . ($i == 1 ? " " . ___('Person') : ___('Personen')) . '</option>';
                            }
            $html .= '    
                            </select>
                        </div>
                        <div class="mybookingsres-list-searchbox-item mybookingsres-list-searchbox-item-button">
                            <button id="MyBookingsRESFrontent_List_button_search">'. ___('Suche') .'</button>
                        </div>
                    </div>
                    <div class="mybookingsres-list-submenu">
                        <div class="mybookingsres-submenu-left">
                            <button id="MyBookingsRES_filter_button">'. ___('Filter') .'</button>
                            <button style="display: none;" class="MyBookingsRESFrontent_List_submenu_button" data-container="mybookingsres-list-attributes-container" '. $resultContainerStyle .'>'. ___('Filter') .'</button>
                            <button class="MyBookingsRESFrontent_List_submenu_button" data-container="mybookingsres-list-map-container">'. ___('Karte') .'</button>
                            <small><a id="MyBookingsRESFrontent_List_button_reset">'. ___('Reset') .'</a></small>
                        </div>
                        <div class="mybookingsres-submenu-right" style="display: none;">
                            <select id="MyBookingsRES-location-select" class="">
                                <option value="ALL">'. ___('Alle Standorte') .'</option>';

                        foreach ($locations as $location) {

                            $locationLabel = $currentLanguage === 'de' ? $location['description'] : $location['description_en'];

                            $html .= '
                                <option
                                    value="'. $location['location_id'] .'">'
                                    . $locationLabel .
                                '</option>
                            ';
                        }

            $html .= '
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="mybookingsres-list-attributes-container mybookingsres-dontshow">
                            
                    <div class="mybookingsres-list-attributes-container-items">
                    </div>
                
                </div>

                <div class="mybookingsres-list-map-container mybookingsres-dontshow">
                    <div id="mybookingsres-list-map">
                    </div>
                </div>
                
                '. $this->getResultShortCodeFilterHtml(true) .'

                <div id="mybookingsres-list-container-items" class="mybookingsres-list-items ' . ($isSearchUrlParameter ? "mybookingsres-dontshow" : "") . '">';

                    $sortCategories = (boolean) $db->getSetting('sortCategories');

                    $categories = $db->getShortCategoriesInfo(!$sortCategories);

                    $sort = 1;
                    $mapData = [];

                    foreach($categories as $category) {
                        $listEntry = $this->getListEntry($category->category, $sort, $isSearchUrlParameter);
                        $html .= $listEntry->html;

                        $mapData[] = [ $listEntry->lat, $listEntry->lon, $listEntry->title ];
                        $sort++;
                    }



        $html .= '
                </div>
            </div>
         </div>

            
            <script>

                MyBookingsRESFrontent_searchParameters.area =  "' . $area . '";
                MyBookingsRESFrontent_searchParameters.from =  "' . $from . '";
                MyBookingsRESFrontent_searchParameters.to =  "' . $to . '";
                MyBookingsRESFrontent_searchParameters.people =  "' . $people . '";
                MyBookingsRESFrontent_searchParameters.calendarFrom =  null;
                MyBookingsRESFrontent_searchParameters.calendarTo =  null;
                MyBookingsRESFrontent_List_mapData = ' . json_encode($mapData) . ';

                (function($) {

                    $(function() {
                        /** start */
                        MyBookingsRESFrontent.List.categories = ' . json_encode($categories) . ';
                        MyBookingsRESFrontent.List.init();

                        $(".lazy").Lazy({ autoDestroy: true });

                        MyBookingsRESFrontent.initSearchCalendar(null);
                    });

                })(jQuery);

                if (typeof(google) == "undefined") {
                    /* google maps */
                    var script = document.createElement("script");
                    script.src = "https://maps.googleapis.com/maps/api/js?key=' . MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "googleAPIKey") . '&callback=initMap";
                    script.defer = true;
                    
                    window.initMap = function() {
                        // JS API is loaded and available
                        MyBookingsRESFrontent.List.GMap.load();
                    };

                    document.head.appendChild(script);
                } else {
                    MyBookingsRESFrontent.List.GMap.load();
                }
            </script>
        ';

        return '<div class="mybookingsres-container">' . $html . '</div>';
    }

    /**
     * Returns a list entry for the short code content for '[MyBookingsRES-Search]'.
     * @param $category
     * @param int $sort
     * @param bool $isSearchUrlParameter
     * @param string $currentLang
     * @return stdClass
     */
    public function getListEntry($category, $sort = 1, $isSearchUrlParameter = false, $currentLang = "de")
    {

        $db = new MyBookingsRES_DB();

        $currentLanguage = pll_current_language();

        $text = $db->getText($category, $currentLanguage);

        if (!$text) {
            $text = $db->getText($category, 'de');
        }

        $categoryData = $db->getCategoryById($category);

        $topAttributes = $db->getAttributes($category, 1);

        $images = $db->getImagesByCategoryId($category);
        $firstImageId = $images[0]['image_id'];
        $firstImageUrl = 'https://www.my-bookings.cc/everest/photo/W300/H200/C1/' . $firstImageId;
        $firstImageUrlRetina = 'https://www.my-bookings.cc/everest/photo/W600/H400/C1/' . $firstImageId;

        $categoryPageTranslations = pll_get_post_translations($categoryData['detail_page_id']);
        $categoryPageUrl = get_permalink($categoryPageTranslations[$currentLanguage]);

        $title = $text && $text['title'] ? $text['title'] : $categoryData['ndesc_en'];

        $description = $text && $text['short_desc'] ? substr(strip_tags($text['short_desc']), 0, 200) . '...' : '...';

        $categorySize = $categoryData['sizem'];
        $categoryMaxGuests = $categoryData['max_num_of_persons'];

        $html = "
            <div class='mybookingsres-list-item entry-$category " . ($isSearchUrlParameter ? "mybookingsres-is-loading" : "mybookingsres-is-nosearch") . "' data-category='$category' data-sort='$sort' data-permalink='$categoryPageUrl'>
                
                <div class='mybookingsres-list-item-preview-img-container'>
                    <a href='" . $categoryPageUrl . "}'>
                        <img class='lazy mybookingsres-list-item-preview-img object-fit_cover' src='https://via.placeholder.com/300x200?text=Bild%20wird%20geladen' data-src='$firstImageUrl'  data-retina='$firstImageUrlRetina' alt='$firstImageId'>
                    </a>
                </div>

                <div class='mybookingsres-list-item-category-info'>
                    <div class='mybookingsres-list-category-specs'>
                        <div class='mybookingsres-list-category-spec'>
                            <div class='mybookingsres-list-category-spec-number'>". $categorySize ."</div>
                            <img src='" . MyBookingsRESPlugin::getIconUrl('square-meters') ."' alt='square-meters'>
                        </div>
                        <div class='mybookingsres-list-category-spec'>
                            <div class='mybookingsres-list-category-spec-number'>". $categoryMaxGuests ."</div>
                            <img src='" . MyBookingsRESPlugin::getIconUrl('guest-count') ."' alt='guest-count'>
                        </div>
                    </div>
                    <h3 class='mybookingsres-list-item-title'>
                        <a href='" . $categoryPageUrl . "' class='mybookingsres-titlelink'>
                            " . $title . "
                        </a>
                    </h3>
                    <div class='mybookingsres-list-topattributes'>";

                        if (is_array($topAttributes)) {
                            foreach ($topAttributes as $attr) {
                                $html .= "<img style='display:inline;margin-right:10px;' src='" . MyBookingsRESPlugin::getAttributeIconUrl($attr["attribut_id"]) . "' width='25' height='25' title='" . MyBookingsRESPlugin::getAttributeText($attr, $currentLang) . "'>";
                            }
                        }

        $html .= "
                    </div>
                    <div class='mybookingsres-available-rooms'>". ___('Verfügbar') .": <span class='count'></span></div>
                    <div class='mybookingsres-description'>$description</div>
                </div>

                <div class='mybookingsres-list-item-price-details'>

                        <div class='mybookingsres-list-loader'></div>

                        <div class='mybookingsres-priceinfo-nosearch'>
                            <div class='mybookingsres-priceinfo-info-container'>
                                <div class='priceinfo-from'>". ___('ab') ." </div>
                                <div class='priceinfo-line'><span class='price'>" . $categoryData["min_price"] .  " €</span></div>
                                <div><span class='nights'>". ___('für 1 Nacht') ."</span></div>
                            </div>
                            <div>
                                <button data-category='$category' data-search='0' class='MyBookingsRESFrontent_bookNowButton'>". ___('Details') ."</button>
                            </div>
                        </div>

                        <div class='mybookingsres-priceinfo-search'>
                            <div class='mybookingsres-priceinfo-info-container'>
                                <div class='priceinfo-line'><span class='price'></span> €</div>
                                <div>". ___('für') ." <span class='nights'></span> ". ___('Nächte') ."</div>
                            </div>
                            <div>
                                <button data-category='$category' data-search='1' class='MyBookingsRESFrontent_bookNowButton'>". ___('Jetzt buchen') ."</button>
                            </div>
                        </div>

                        <div class='mybookingsres-priceinfo-notavailable'>
                            <div>
                                <button data-category='$category' data-search='0' class='MyBookingsRESFrontent_bookNowButton'>". ___('Nicht verfügbar') ."</button>
                            </div>
                        </div>
                    
                </div>
            </div>
        ";

        $res = new stdClass();
        $res->html = $html;
        $res->title = $title;
        $res->lat = (float) $categoryData["lat"];
        $res->lon = (float) $categoryData["lon"];

        return $res;
    }

    public function getResultShortCodeFilterHtml($mobileView = false)
    {
        $db = new MyBookingsRES_DB();

        $currentLanguage = pll_current_language();

        $attributes = $db->getAttributesForSearch();

        $locations = $db->getLocations();

        $showFilter = $db->getSetting('showFilter');

        $filterContainerStyle = $showFilter === '1' ? '' : 'style="display: none;"';

        if ($mobileView) {
            $filterContainerClass = 'mybookingsres-filter-container-mobile';
        } else {
            $filterContainerClass = 'mybookingsres-filter-container';
        }

        $html = '
            <div class="'. $filterContainerClass .'" '. $filterContainerStyle .'>
                <h1>'. ___('Filtern nach') .'</h1>';

        if (count($attributes) > 0) {
            $html .= '<h2>'. ___('Ausstattung') .'</h2>';
        }

        foreach ($attributes as $attribute) {

            $label = '';
            if (array_search($currentLanguage, ['de', 'en', 'es']) !== false) {
                $label = $attribute['desc_' . $currentLanguage];
            } else {
                $label = $attribute['desc_en'];
            }

            $html .= '
                <div class="mybookingsres-filter-item">
                    <div style="flex-grow: 1">
                        <input id="mybookingsres-filter-attribute-'. $attribute['attribut_id'] .'" type="checkbox">
                    </div>
                    <div class="mybookingsres-filter-item-label">
                        <label for="mybookingsres-filter-attribute-'. $attribute['attribut_id'] .'">'. $label .'</label>
                    </div>
                </div>
            ';
        }

        if (count($locations) > 0) {
            $html .= '<h2>'. ___('Standort') .'</h2>';
        }

        foreach ($locations as $location) {

            $label = '';
            if ($currentLanguage === 'de') {
                $label = $location['description'];
            } else {
                $label = $location['description_en'];
            }

            $html .= '
                <div class="mybookingsres-filter-item">
                    <div style="flex-grow: 1">
                        <input id="mybookingsres-filter-location-'. $location['location_id'] .'" type="checkbox">
                    </div>
                    <div class="mybookingsres-filter-item-label">
                        <label for="mybookingsres-filter-location-'. $location['location_id'] .'">'. $label .'</label>
                    </div>
                </div>';
        }

        $html .= '<input type="submit" value="'. ___('Suchen') .'">
                </div>';

        return $html;
    }

    /**
     * Returns the short code content for '[MyBookingsRES-Map]'.
     * @param $attributesRaw
     * @return string
     */
    public function getMapShortCodeContent($attributesRaw)
    {
        $attributes = shortcode_atts(['lat' => null, 'lon' => null], $attributesRaw);
        $attributeLat = $attributes['lat'];
        $attributeLon = $attributes['lon'];

        $currentLanguage = pll_current_language();

        if ($attributeLat && $attributeLon) {

            $googleMapsUrl = "https://maps.google.com/maps?q=$attributeLat,$attributeLon&hl=$currentLanguage&z=14&amp;output=embed";

            return "
                <iframe 
                    src='$googleMapsUrl'
                    width='100%' height='450' frameborder='0' style='border:0;' allowfullscreen='' aria-hidden='false' tabindex='0'>
                </iframe>
            ";
        } else {

            $db = new MyBookingsRES_DB();
            $storedSettings = $db->getSettings();

            $categories = $db->getCategories();

            $mapData = [];

            foreach($categories as $category) {

                $text = $db->getText($category, 'de');

                $title = $text && $text['title'] ? $text['title'] : "-";

                $lat = $category['lat'];
                $lon = $category['lon'];

                $mapData[] = [ $lat, $lon, $title ];
            }

            return '
                <div class="mybookingsres-map-container">
                    <div id="mybookingsres-list-map">
                    </div>
                </div>
                <script>

                    MyBookingsRESFrontent_List_mapData = ' . json_encode($mapData) . ';

                    if (typeof(google) == "undefined") {
                        /* google maps */
                        var script = document.createElement("script");
                        script.src = "https://maps.googleapis.com/maps/api/js?key=' . MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "googleAPIKey") . '&callback=initMap";
                        script.defer = true;
                        
                        window.initMap = function() {
                            // JS API is loaded and available
                            MyBookingsRESFrontent.List.GMap.load();
                        };

                        document.head.appendChild(script);
                    } else {
                        MyBookingsRESFrontent.List.GMap.load();
                    }
                </script>';


        }


    }

    /**
     * Returns the short code content for '[MyBookingsRES-Teaser]'.
     * @param $attributesRaw
     * @return string
     */
    public function getTeaser($attributesRaw)
    {
        $db = new MyBookingsRES_DB();

        $attributes = shortcode_atts(['id' => null, 'anzahl' => null], $attributesRaw);
        $containerId = $attributes['id'];
        $countItems = $attributes['anzahl'];

        if (empty($containerId)) {
            $containerId = "custom1";
        }

        if (empty($countItems)) {
            $countItems = 3;
        }

        $currentLanguage = pll_current_language();


        $html = '
            <div class="mybookingsres-teaser mybookingsres-teaser-'.$containerId.'">';


        $categories = $db->getShortCategoriesInfo(true, (int) $countItems);

        foreach($categories as $categoryEntry) {

            $category = $categoryEntry->category;

            $text = $db->getText($category, $currentLanguage);

            $categoryData = $db->getCategoryById($category);
            $topAttributes = $db->getAttributes($category, 1);

            $images = $db->getImagesByCategoryId($category);
            $firstImageId = $images[0]['image_id'];
            $firstImageUrl = 'https://www.my-bookings.cc/everest/photo/W800/H533/C1/' . $firstImageId;

            $categoryPageTranslations = pll_get_post_translations($categoryData['detail_page_id']);
            $categoryPageUrl = get_permalink($categoryPageTranslations[$currentLanguage]);

            $title = $text && $text['title'] ? $text['title'] : $categoryData['ndesc_en'];

            $html .= '
                <div class="mybookingsres-teaser-image-container">
                    <div class="mybookingsres-teaser-image">
                        <a href="' . $categoryPageUrl . '"><img src="' . $firstImageUrl . '" /></a>
                        <div class="mybookingsres-teaser-image-info">
                            ' . $categoryData["max_num_of_persons"] . ' ' . ___('Personen') . ' | ' . $categoryData["sizem"] . 'm² | ab ' . round($categoryData["min_price"], 0) . ' &euro;
                        </div>
                    </div>
                    <div class="mybookingsres-teaser-category-text">
                        ' . $title . '
                    </div>
                </div>            
            ';
        }

        $html .= '                
            </div>
        ';

        return $html;
    }


    /**
     * Deletes unused 'categories' from the plugin's 'categories' database table.
     */
    public function deleteUnusedCategories()
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

        $dbSync = new MyBookingsRES_DBSync();
        $dbSync->deleteRemovedCategories($response);
        $dbSync->resetCategorySortOrder();

        MyBookingsRESPluginHttpResult::JSONOutput($data->error, $response);
    }

    /**
     * Returns the my-bookings 'apiKey' setting from the plugin's 'settings' database table.
     */
    public function getApiKey()
    {
        $MyBookingsRES_DB = new MyBookingsRES_DB();

        MyBookingsRESPluginHttpResult::JSONOutput(0, ['apiKey' => $MyBookingsRES_DB->getApiKey()]);
    }

    /**
     * Returns the my-bookings 'websiteConfigId' setting from the plugin's 'settings' database table.
     */
    public function getWebsiteConfigId()
    {
        $MyBookingsRES_DB = new MyBookingsRES_DB();

        MyBookingsRESPluginHttpResult::JSONOutput(0, ['websiteConfigId' => $MyBookingsRES_DB->getWebsiteConfigId()]);
    }

    /**
     * This function was used early on for test porpuses. Returns all plugin's database tables in a JSON format.
     */
    public function testDB()
    {
        $MyBookingRES_DB = new MyBookingsRES_DB();

        MyBookingsRESPluginHttpResult::JSONOutput(0, $MyBookingRES_DB->getTableNames());
    }
}