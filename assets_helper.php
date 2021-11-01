<?php
if (!function_exists('assets')) {
	function assets($file) {
		return base_url('assets/') . $file;
	}
}
if (!function_exists('assets_css')) {
	function assets_css($file) {
		return base_url('assets/css/') . $file . '.css';
	}
}
if (!function_exists('assets_js')) {
	function assets_js($file) {
		return base_url('assets/js/') . $file . '.js';
	}
}
if (!function_exists('assets_image')) {
	function assets_image($file) {
		return base_url('assets/image/') . $file;
	}
}
if (!function_exists('assets_upload')) {
	function assets_upload($file) {
		return base_url('assets/upload/') . $file;
	}
}
if (!function_exists('validation_errors_response_web')) {
	function validation_errors_response_web() {
		$err_array = array();
		$err_str = "";
		$err_str = str_replace(array('<p>', '</p>'), array('|', ''), trim(validation_errors()));
		$err_str = ltrim($err_str, '|');
		$err_str = rtrim($err_str, '|');
		// $err_array=explode('|',$err_str);
		// $err_array = array_filter($err_array);
		return $err_array;
	}
}
/*
 * Additional :
 */
if (!function_exists('assets_less')) {
	function assets_less($file) {
		return base_url('assets/less/') . $file . '.less';
	}
}
if (!function_exists('assets_sass')) {
	function assets_sass($file) {
		return base_url('assets/sass/') . $file . '.sass';
	}
}
if (!function_exists('to_json')) {
	function to_json($array) {
	}
}
if (!function_exists('config')) {
	function config($key) {
		$CI = get_instance();
		$CI->load->model('Config');
		$config = Config::wherePath($key)->first();
		return $config->value;
	}
}
if (!function_exists('config_set')) {
	function config_set($key_array) {
		$CI = get_instance();
		$CI->load->model('Config');
		foreach ($key_array as $key => $value) {
			$update['value'] = $value;
			Config::wherePath($key)->update($update);
		}
		return true;
	}
}
if (!function_exists('image_upload')) {
	function image_upload($file, $path = '', $enc = FALSE) {
		$CI = get_instance();
		if (!is_dir("./assets/" . $path)) {
			mkdir("./assets/" . $path, 0777, TRUE);
		}
		$config = array(
			'upload_path' => "./assets/" . $path,
			'allowed_types' => "gif|jpg|png|jpeg|PNG|JPG",
			'overwrite' => TRUE,
			'max_size' => "2048000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
			'remove_spaces' => TRUE,
			'encrypt_name' => $enc,
			'file_ext_tolower' => TRUE,
		);
		$CI->load->library('upload', $config);
		$CI->upload->initialize($config);
		if ($CI->upload->do_upload($file)) {
			$data = $CI->upload->data();
			$data['status'] = true;
		} else {
			$data['error'] = $CI->upload->display_errors();
			$data['status'] = false;
		}
		return $data;
	}
}
if (!function_exists('CsvUpload')) {
	function CsvUpload($file, $path = '', $enc = FALSE) {
		$CI = get_instance();
		if (!is_dir("./assets/" . $path)) {
			mkdir("./assets/" . $path, 0777, TRUE);
		}
		$config = array(
			'upload_path' => "./assets/" . $path,
			'allowed_types' => "csv|xlsx",
			'overwrite' => TRUE,
			'max_size' => "2048000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
			'remove_spaces' => TRUE,
			'encrypt_name' => $enc,
			'file_ext_tolower' => TRUE,
		);
		$CI->load->library('upload', $config);
		$CI->upload->initialize($config);
		if ($CI->upload->do_upload($file)) {
			$data = $CI->upload->data();
			$data['status'] = true;
		} else {
			$data['error'] = $CI->upload->display_errors();
			$data['status'] = false;
		}
		return $data;
	}
}
if (!function_exists('__')) {
	function __() {
		$args = func_get_args();
		if (count($args) >= 1) {
			$CI = get_instance();
			$str = $args[0];
			$nstr = $str;
			$fstr = $CI->lang->line($str);
			if ($fstr != null) {
				$nstr = $fstr;
			}
			if (count($args) >= 2) {
				array_shift($args);
				return vsprintf($nstr, $args);
			}
			return $nstr;
		}
		return null;
	}
}
if (!function_exists('__timeago')) {
	function __timeago($datetime, $full = false) {
		$now = new DateTime;
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);
		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;
		$string = array(
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			} else {
				unset($string[$k]);
			}
		}
		if (!$full) {
			$string = array_slice($string, 0, 1);
		}
		return $string ? implode(', ', $string) . ' ago' : 'just now';
	}
}
if (!function_exists('__date')) {
	function __date($date, $format = false) {
		if ($format) {
			$date = date_create($date);
			return date_format($date, "Y-m-d");
		} else {
			$date = date_create($date);
			return date_format($date, config('date_format'));
		}
	}
}
if (!function_exists('base64ToImage')) {
	function base64ToImage($image, $dir) {
		$img = $image;
		$img = str_replace('data:image/png;base64,', '', $img);
		$img = str_replace('data:image/jpeg;base64,', '', $img);
		$img = str_replace(' ', '+', $img);
		$data = base64_decode($img);
		$time = time() . '.png';
		$file = 'assets/' . $dir . $time;
		$success = file_put_contents($file, $data);
		if ($success) {
			return $time;
		} else {
			return "";
		}
	}
}
if (!function_exists('_pre')) {
	function _pre($array) {
		echo "<pre>";
		print_r($array);
		echo "<pre>";
		exit;
	}
}
if (!function_exists('show_general')) {
	function show_general($message = "") {
		$CI = get_instance();
		$data['heading'] = "Auth error";
		$data['message'] = $message;
		$CI->load->view('errors/html/error_general', $data);
	}
}

if(!function_exists('active_link')) {
  function activate_menu($controller) {
    // Getting CI class instance.
    $CI = get_instance();
    // Getting router class to active.
    $class = $CI->router->fetch_class();
    
    return ($class == $controller) ? 'active' : '';
  }
}