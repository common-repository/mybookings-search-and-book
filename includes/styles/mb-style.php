<?php
// require_once plugin_dir_path(__FILE__) . '/my-bookings-frontend-http.php';


function get_mb_style_variables() {
    $db = new MyBookingsRES_DB();

    $storedSettings = $db->getSettings();

    $showFilter = $db->getSetting('showFilter');

    $filterStyle = "
        @media (min-width: 768px) {
            #MyBookingsRES_filter_button {
                display: block;
            }
        }
    ";

    if ($showFilter === '1') {
        $filterStyle = "
        @media (min-width: 100px) {
            #MyBookingsRES_filter_button {
                display: block;
            }
        }
        @media (min-width: 768px) {
            #MyBookingsRES_filter_button {
                display: none;
            }
        }
    ";
    }

?>
    <style type="text/css">
    :root {
        --mybookingsres-primary-color1: <?php echo  MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "color1");?>;
        --mybookingsres-primary-color2: <?php echo  MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "color2");?>;
        --mybookingsres-primary-color3: <?php echo  MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "color3");?>;
        --mybookingsres-primary-color4: <?php echo  MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "color4");?>;
        --mybookingsres-primary-color_bglist: <?php echo  MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "color_bglist");?>;

    }

    <?php echo  MyBookingsRESPlugin::getSingleValueFromSettings($storedSettings, "custom_css");?>

    <?php echo $filterStyle; ?>

    </style>
<?php
}

add_action( 'wp_head', 'get_mb_style_variables' );


// Load CSS on the frontend
function getFrontendStyle() {

    wp_enqueue_style(
        'fresco_css',
        MB_PLUGIN_URL . 'assets/fresco/css/fresco.css',
        [],
        time()
    );

    wp_enqueue_style(
        'when_datepicker_css',
        MB_PLUGIN_URL . 'assets/datepicker/when.min.css',
        [],
        time()
    );

    wp_enqueue_style(
        'caleran_css',
        MB_PLUGIN_URL . 'assets/caleran/css/caleran.min.css',
        [],
        time()
    );
    
    wp_enqueue_style(
        'mb_plugin_css',
        MB_PLUGIN_URL . 'frontend/css/mb-style.css',
        [],
        time()
    );
}

add_action( 'wp_enqueue_scripts', 'getFrontendStyle', 100 );

// Load CSS on the backend
function getBackendStyle() {

    wp_enqueue_style(
        'mb_plugin_css_backend',
        MB_PLUGIN_URL . 'backend/css/mb-style-backend.css',
        [],
        time()
    );
}

add_action( 'admin_enqueue_scripts', 'getBackendStyle', 100 );