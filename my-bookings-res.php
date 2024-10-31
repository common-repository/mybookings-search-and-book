<?php

/**
 * @package MyBookingsRESPlugin
 */

/*
 Plugin Name: MyBookingsRES
 Plugin URI: https://www.my-bookings.org/
 Description: Official my-bookings.org WordPress plugin.
 Version: 2.0.1
 Author: Christian, Dominic
 License: CC BY-NC-SA 4.0 or later
 */

require_once plugin_dir_path(__FILE__) . '/my-bookings.php';
require_once plugin_dir_path(__FILE__) . '/ajax-result-struct.php';
require_once plugin_dir_path(__FILE__) . '/helper.php';
require_once plugin_dir_path(__FILE__) . '/my-bookings-backend-http.php';
require_once plugin_dir_path(__FILE__) . '/my-bookings-frontend-http.php';
require_once plugin_dir_path(__FILE__) . 'TranslationService.php';

if(!defined('ABSPATH') || !class_exists('myBookingsRESPlugin')) {
    die;
}

$myBookingsRESPlugin = new MyBookingsRESPlugin();
$MyBookingsRESPluginBackendHttp = new MyBookingsRESPluginBackendHttp();
$MyBookingsRESPluginFrontentHttp = new MyBookingsRESPluginFrontentHttp();
$GLOBALS['mbTranslation'] = new TranslationService();

define('MB_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('MB_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

// Enqueue Plugin CSS
include( plugin_dir_path( __FILE__ ) . 'includes/styles/mb-style.php');

// Enqueue Plugin JavaScript
include( plugin_dir_path( __FILE__ ) . 'includes/scripts/mb-script.php');

// Plugin activation
register_activation_hook(__FILE__, array($myBookingsRESPlugin, 'activatePlugin'));

// Add main menu entry
add_action('admin_menu', array($myBookingsRESPlugin, 'addAdminMenuEntry'));



/* prevent google to index pages with url parameters */
function meta_noindex_no_follow() {

    // Page conditional if needed
    // if( is_page() ){}
  ?>
    <meta name="robots" content="noindex, nofollow">
  <?php
}

$isSearchUrlParameter = !empty(MyBookingsRESPluginHelper::checkAndGetQueryVal("mbf")) ||
                          !empty(MyBookingsRESPluginHelper::checkAndGetQueryVal("f")) ||
                              !empty(MyBookingsRESPluginHelper::checkAndGetQueryVal("t")) ||
                                  !empty(MyBookingsRESPluginHelper::checkAndGetQueryVal("people")) ||
                                    !empty(MyBookingsRESPluginHelper::checkAndGetQueryVal("nd_booking_archive_form_date_range_from")) ||
                                      !empty(MyBookingsRESPluginHelper::checkAndGetQueryVal("nd_booking_archive_form_date_range_to")) ||
                                        !empty(MyBookingsRESPluginHelper::checkAndGetQueryVal("nd_booking_archive_form_guests"));

if ($isSearchUrlParameter) {    
    add_action('wp_head', 'meta_noindex_no_follow');
}

/**
 * Sets global javascript variable 'MyBookingsRESFrontent_lang' with the available polylang languages.
 */
function globalJS() {
  $myBookingsRESPlugin = new MyBookingsRESPlugin();
  $requiredPluginsInstalled = $myBookingsRESPlugin->checkRequiredPlugins();
  
  $currentLanguage = pll_current_language();

  if ($requiredPluginsInstalled) {
      ?>
    <script>
      var MyBookingsRESFrontent_lang = "<?php echo $currentLanguage; ?>";

    </script>
  <?php
  }

}
add_action( 'wp_head', 'globalJS' );


/* AJAX Calls */

add_action( 'wp_ajax_MyBookingsRES_syncCategories', array($MyBookingsRESPluginBackendHttp, 'syncCategories') );
add_action( 'wp_ajax_nopriv_MyBookingsRES_syncCategories', array($MyBookingsRESPluginBackendHttp, 'syncCategories'));

add_action( 'wp_ajax_MyBookingsRES_deleteUnusedCategories', array($myBookingsRESPlugin, 'deleteUnusedCategories') );
add_action( 'wp_ajax_nopriv_MyBookingsRES_deleteUnusedCategories', array($myBookingsRESPlugin, 'deleteUnusedCategories'));

add_action( 'wp_ajax_MyBookingsRES_createCategoryPages', array($MyBookingsRESPluginBackendHttp, 'createCategoryPages') );
add_action( 'wp_ajax_nopriv_MyBookingsRES_createCategoryPages', array($MyBookingsRESPluginBackendHttp, 'createCategoryPages'));

add_action( 'wp_ajax_MyBookingsRES_removeCategoryPages', array($MyBookingsRESPluginBackendHttp, 'removeCategoryPages') );
add_action( 'wp_ajax_nopriv_MyBookingsRES_removeCategoryPages', array($MyBookingsRESPluginBackendHttp, 'removeCategoryPages'));

add_action( 'wp_ajax_MyBookingsRES_createResultPages', array($MyBookingsRESPluginBackendHttp, 'createResultPages') );
add_action( 'wp_ajax_nopriv_MyBookingsRES_createResultPages', array($MyBookingsRESPluginBackendHttp, 'createResultPages'));

add_action( 'wp_ajax_MyBookingsRES_createPaymentReturnPages', array($MyBookingsRESPluginBackendHttp, 'createPaymentReturnPages') );
add_action( 'wp_ajax_nopriv_MyBookingsRES_createPaymentReturnPages', array($MyBookingsRESPluginBackendHttp, 'createPaymentReturnPages'));

add_action( 'wp_ajax_MyBookingsRES_getCategoriesShortInfos', array($MyBookingsRESPluginBackendHttp, 'getCategoriesShortInfos') );
add_action( 'wp_ajax_nopriv_MyBookingsRES_getCategoriesShortInfos', array($MyBookingsRESPluginBackendHttp, 'getCategoriesShortInfos'));

add_action( 'wp_ajax_MyBookingsRES_saveSettings', array($MyBookingsRESPluginBackendHttp, 'saveSettings'));
add_action( 'wp_ajax_nopriv_MyBookingsRES_saveSettings', array($MyBookingsRESPluginBackendHttp, 'saveSettings'));

add_action( 'wp_ajax_MyBookingsRES_loadAreaFilterDataForAttributes', array($MyBookingsRESPluginBackendHttp, 'loadAreaFilterDataForAttributes'));
add_action( 'wp_ajax_nopriv_MyBookingsRES_loadAreaFilterDataForAttributes', array($MyBookingsRESPluginBackendHttp, 'loadAreaFilterDataForAttributes'));

add_action( 'wp_ajax_MyBookingsRES_saveAreaFilterDataForAttributes', array($MyBookingsRESPluginBackendHttp, 'saveAreaFilterDataForAttributes'));
add_action( 'wp_ajax_nopriv_MyBookingsRES_saveAreaFilterDataForAttributes', array($MyBookingsRESPluginBackendHttp, 'saveAreaFilterDataForAttributes'));

add_action( 'wp_ajax_MyBookingsRES_loadAreaFilterAssignmentsForAttributes', array($MyBookingsRESPluginBackendHttp, 'loadAreaFilterAssignmentsForAttributes'));
add_action( 'wp_ajax_nopriv_MyBookingsRES_loadAreaFilterAssignmentsForAttributes', array($MyBookingsRESPluginBackendHttp, 'loadAreaFilterAssignmentsForAttributes'));

add_action( 'wp_ajax_MyBookingsRES_loadAreaFilterDataForDistances', array($MyBookingsRESPluginBackendHttp, 'loadAreaFilterDataForDistances'));
add_action( 'wp_ajax_nopriv_MyBookingsRES_loadAreaFilterDataForDistances', array($MyBookingsRESPluginBackendHttp, 'loadAreaFilterDataForDistances'));

add_action( 'wp_ajax_MyBookingsRES_saveAreaFilterDataForDistances', array($MyBookingsRESPluginBackendHttp, 'saveAreaFilterDataForDistances'));
add_action( 'wp_ajax_nopriv_MyBookingsRES_saveAreaFilterDataForDistances', array($MyBookingsRESPluginBackendHttp, 'saveAreaFilterDataForDistances'));

add_action( 'wp_ajax_MyBookingsRES_loadAreaFilterAssignmentsForDistances', array($MyBookingsRESPluginBackendHttp, 'loadAreaFilterAssignmentsForDistances'));
add_action( 'wp_ajax_nopriv_MyBookingsRES_loadAreaFilterAssignmentsForDistances', array($MyBookingsRESPluginBackendHttp, 'loadAreaFilterAssignmentsForDistances'));

add_action( 'wp_ajax_MyBookingsRES_syncAreas', array($MyBookingsRESPluginBackendHttp, 'syncAreas'));
add_action( 'wp_ajax_nopriv_MyBookingsRES_syncAreas', array($MyBookingsRESPluginBackendHttp, 'syncAreas'));

add_action( 'wp_ajax_MyBookingsRES_syncLocations', array($MyBookingsRESPluginBackendHttp, 'syncLocations'));
add_action( 'wp_ajax_nopriv_MyBookingsRES_syncLocations', array($MyBookingsRESPluginBackendHttp, 'syncLocations'));


/** for frontend */
add_action( 'wp_ajax_MyBookingsRES_getCategoriesShortInfosFrontent', array($MyBookingsRESPluginFrontentHttp, 'getCategoriesShortInfosFrontent') );
add_action( 'wp_ajax_nopriv_MyBookingsRES_getCategoriesShortInfosFrontent', array($MyBookingsRESPluginFrontentHttp, 'getCategoriesShortInfosFrontent'));

add_action( 'wp_ajax_MyBookingsRES_getFilteredCategories', array($MyBookingsRESPluginFrontentHttp, 'getFilteredCategories') );
add_action( 'wp_ajax_nopriv_MyBookingsRES_getFilteredCategories', array($MyBookingsRESPluginFrontentHttp, 'getFilteredCategories'));

add_action( 'wp_ajax_MyBookingsRES_getListEntryPackage', array($MyBookingsRESPluginFrontentHttp, 'getListEntryPackage') );
add_action( 'wp_ajax_nopriv_MyBookingsRES_getListEntryPackage', array($MyBookingsRESPluginFrontentHttp, 'getListEntryPackage'));

add_action( 'wp_ajax_MyBookingsRES_checkAvailability', array($MyBookingsRESPluginFrontentHttp, 'checkAvailability') );
add_action( 'wp_ajax_nopriv_MyBookingsRES_checkAvailability', array($MyBookingsRESPluginFrontentHttp, 'checkAvailability'));

add_action( 'wp_ajax_MyBookingsRES_getApartmentURL', array($MyBookingsRESPluginFrontentHttp, 'getApartmentURL') );
add_action( 'wp_ajax_nopriv_MyBookingsRES_getApartmentURL', array($MyBookingsRESPluginFrontentHttp, 'getApartmentURL'));

add_action( 'wp_ajax_MyBookingsRES_getAttributesForSearch', array($MyBookingsRESPluginFrontentHttp, 'getAttributesForSearch') );
add_action( 'wp_ajax_nopriv_MyBookingsRES_getAttributesForSearch', array($MyBookingsRESPluginFrontentHttp, 'getAttributesForSearch'));

add_action( 'wp_ajax_MyBookingsRES_getCategoriesWithAttributes', array($MyBookingsRESPluginFrontentHttp, 'getCategoriesWithAttributes') );
add_action( 'wp_ajax_nopriv_MyBookingsRES_getCategoriesWithAttributes', array($MyBookingsRESPluginFrontentHttp, 'getCategoriesWithAttributes'));

add_action( 'wp_ajax_MyBookingsRES_getResultPageUrl', array($MyBookingsRESPluginFrontentHttp, 'getResultPageUrl') );
add_action( 'wp_ajax_nopriv_MyBookingsRES_getResultPageUrl', array($MyBookingsRESPluginFrontentHttp, 'getResultPageUrl'));

add_action( 'wp_ajax_MyBookingsRES_getResultPageUrlWithParameters', array($MyBookingsRESPluginFrontentHttp, 'getResultPageUrlWithParameters') );
add_action( 'wp_ajax_nopriv_MyBookingsRES_getResultPageUrlWithParameters', array($MyBookingsRESPluginFrontentHttp, 'getResultPageUrlWithParameters'));



/* - - - */
/* Short Codes */

// booking search shortcode
add_shortcode('MyBookingsRES-Search', array($myBookingsRESPlugin, 'getSearchShortCodeContent'));

// booking result shortcode
add_shortcode('MyBookingsRES-Result', array($myBookingsRESPlugin, 'getResultShortCodeContent'));

// category detail page shortcode
add_shortcode('MyBookingsRES-Category', array($myBookingsRESPlugin, 'getCategoryShortCodeContent'));

// category detail page shortcode
add_shortcode('MyBookingsRES-Map', array($myBookingsRESPlugin, 'getMapShortCodeContent'));

// teaster shortcode
add_shortcode('MyBookingsRES-Teaser', array($myBookingsRESPlugin, 'getTeaser'));

/* - - - */