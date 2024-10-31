<?php

class MyBookingsRESPluginHttpResult
{
	var $error;
    var $data;	
    var $info;

    public function __construct ($error, $data, $info = "")
    {
        $this->error = $error;
        $this->data = $data;
        $this->info = $info;
    }

    static function JSONOutput($error, $data, $info = "")
    {
        $d = new MyBookingsRESPluginHttpResult($error, $data, $info);
        echo json_encode($d);
        
        wp_die();
    }
}