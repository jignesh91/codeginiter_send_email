<?php

if (!function_exists('convert_date_full_dmy'))
{	
	function convert_date_full_dmy($date)
	{
        $convert_date = date_create($date);
	    $converted_date = date_format($convert_date,"d M Y H:i:s");
	    return $converted_date;
	}
}

if (!function_exists('convert_date_dmy'))
{	
	function convert_date_dmy($date)
	{
        $convert_date = date_create($date);
	    $converted_date = date_format($convert_date,"d-m-Y");
	    return $converted_date;
	}
}

if (!function_exists('convert_date_month_name'))
{	
	function convert_date_month_name($date)
	{
        $convert_date = date_create($date);
	    $converted_date = date_format($convert_date,"d M Y");
	    return $converted_date;
	}
}

if (!function_exists('convert_date_full_ymd'))
{	
	function convert_date_full_ymd($date)
	{
        $convert_date = date_create($date);
	    $converted_date = date_format($convert_date,"Y-m-d H:i:s");
	    return $converted_date;
	}
}

if (!function_exists('convert_date_ymd'))
{	
	function convert_date_ymd($date)
	{
        $convert_date = date_create($date);
	    $converted_date = date_format($convert_date,"Y-m-d");
	    return $converted_date;
	}
}
if (!function_exists('convert_timestamp'))
{	
	function convert_timestamp($timestamp)
	{
		if($timestamp == '')
		{
			$convert_date = '-';
		}
		else
		{
			$convert_date = date('d-m-Y H:i:s', $timestamp);
		}
        
	    return $convert_date;
	}
}
if (!function_exists('convert_date'))
{	
	function convert_date($date)
	{
        $convert_date = strtotime($date);
	    return $convert_date;
	}
}





