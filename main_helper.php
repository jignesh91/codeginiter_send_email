<?php
if (!function_exists('is_admin')) {
	function is_admin() {
		$CI = &get_instance();
		if (!$CI->session->userdata('admin')) {
			return false;
		}

		return true;
	}
}
if (!function_exists('is_investor')) {
	function is_investor() {
		$CI = &get_instance();
		if (!$CI->session->userdata('investor')) {
			return false;
		}
		return true;
	}
}
	
if (!function_exists('read_session')) {
	function read_session($flag) {
		$CI = &get_instance();
		$session = $CI->session->userdata($flag);
		return $session;
	}
}	


if (!function_exists('administrator_render')) {
	function administrator_render($page = null, $params = null, $return = false) {
		$CI = &get_instance();
		$params['body_class'] = _generate_body_class();
		$session['session'] = $CI->session->userdata('admin');
		$CI->load->view('administrator/layouts/header', $session);
		$CI->load->view('administrator/layouts/sidebar');
		if ($page != null) {
			$CI->load->view('administrator/pages/' . $page, $params, $return);
		}
		$CI->load->view('administrator/layouts/footer', $params);

	}
}

if (!function_exists('investor_render')) {
	function investor_render($page = null, $params = null, $return = false) {
		$CI = &get_instance();
		$params['body_class'] = _generate_body_class();
		$session['session'] = $CI->session->userdata('admin');
		$CI->load->view('administrator/layouts/header', $session);
		$CI->load->view('administrator/layouts/sidebar');
		if ($page != null) {
			$CI->load->view('administrator/investor/' . $page, $params, $return);
		}
		$CI->load->view('administrator/layouts/footer', $params);

	}
}

if (!function_exists('investor_user_render')) {
	function investor_user_render($page = null, $params = null, $return = false) {
		$CI = &get_instance();
		$params['body_class'] = _generate_body_class();
		$session['session'] = $CI->session->userdata('investor');
		$CI->load->view('investor/layouts/header', $session);
		$CI->load->view('investor/layouts/sidebar');
		if ($page != null) {
			$CI->load->view('investor/pages/' . $page, $params, $return);
		}
		$CI->load->view('investor/layouts/footer', $params);

	}
}

if (!function_exists('_generate_body_class')) {
	function _generate_body_class() {
		$CI = &get_instance();
		if ($CI->uri->segment_array() == null) {

			$uri = array('index');

		} else {

			$uri = $CI->uri->segment_array();

			if (end($uri) == 'index') {

				array_pop($uri);

			}

		}

		return implode('-', $uri);

	}

}

if (!function_exists('is_empty')) {
	function is_empty($array) {
		if (empty($array)) {
			show_error("Record not found!");
			return false;
		} else {
			return true;
		}
	}
}

if(!function_exists('validation_errors_response')){
    function validation_errors_response()
    {
        $err_array=array();
        $err_str="";
        $err_str=str_replace(array('<p>','</p>'),array('|',''),trim(validation_errors()));

        $err_str=ltrim($err_str,'|');
        $err_str=rtrim($err_str,'|');
        $err_array=explode('|',$err_str);
        $err_array = array_filter($err_array);
        return $err_array;
    }
}

if(!function_exists('checkEmpty'))
{
	function checkEmpty($value)
	{
		if($value !=="" && $value !=0 && $value !=null)
		{
			return true;
		}
		show_error('Unauthorized Access.');
	}
}