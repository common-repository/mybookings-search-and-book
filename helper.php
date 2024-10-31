<?php

class MyBookingsRESPluginHelper
{
	/**
	 * Undocumented function
	 *
	 * @param [type] $key
	 * @return void
	 */
	static function checkAndGetQueryVal($key)
	{
		if (isset($_GET[$key]))
			return $_GET[$key];
		return "";
	}

	static function checkAndGetPostVal($key)
	{
		if (isset($_POST[$key]))
			return $_POST[$key];
		return "";
	}

	static function getGermanDateFromISODate($date)
	{
		if (empty($date)) {
			return "";
		}
		list($year, $month, $day) = explode("-", $date, 3);
		$newdate = "";
		if (checkdate($month, $day, $year)){
            $newdate = $day.".".$month.".".$year;
		}
		return $newdate;
	}
}