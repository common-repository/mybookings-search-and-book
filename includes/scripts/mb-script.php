<?php



// Load JS on the frontend
function getFrontendScript() {

    wp_enqueue_script(
        'lazyloader',
        MB_PLUGIN_URL . 'assets/lazyloader/jquery.lazy.min.js',
        ['jquery']
    );

    wp_enqueue_script(
        'fresco',
        MB_PLUGIN_URL . 'assets/fresco/js/fresco.min.js',
        ['jquery']
    );

    wp_enqueue_script(
        'when_datepicker_js',
        MB_PLUGIN_URL . 'assets/datepicker/when.min.js',
        []
    );

    /*
    wp_enqueue_script(
        'moment',
        MB_PLUGIN_URL . 'assets/caleran/vendor/moment.min.js'
    );*/

    wp_enqueue_script(
        'moment'
    );

    wp_enqueue_script(
        'caleran',
        MB_PLUGIN_URL . 'assets/caleran/js/caleran.min.js',
        ['jquery', 'moment']
    );

    wp_enqueue_script( 
        'mb_plugin_js',
        MB_PLUGIN_URL . 'frontend/js/mb-script.js',
        ['jquery', 'lazyloader', 'when_datepicker_js', 'caleran' ],
        time()
    );

    wp_localize_script('mb_plugin_js', 'ajax', array('url' => admin_url('admin-ajax.php')));   
}

add_action( 'wp_enqueue_scripts', 'getFrontendScript', 100 );

// Load JS on the backend
function getBackendScript() {

    wp_enqueue_script(
        'mb_plugin_js_backend',
        MB_PLUGIN_URL . 'backend/js/mb-script-backend.js',
        ['jquery'],
        time()
    );

    wp_localize_script('mb_plugin_js_backend', 'ajax', array('url' => admin_url('admin-ajax.php')));
}

add_action( 'admin_enqueue_scripts', 'getBackendScript', 100 );




