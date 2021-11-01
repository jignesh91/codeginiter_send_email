<?php 


if (!function_exists('send_mail2'))
{
    function send_mail2($to,$subject,$message)
    {
        //print_r($message);exit;
        $ci=& get_instance();
        $config['protocol'] = "smtp";
        $config['smtp_host'] = "smtp.gmail.com";
        $config['smtp_crypto'] = 'tls';
        //$config['_smtp_auth'] = TRUE;

        $config['smtp_port'] = "587";
        $config['smtp_user'] = 'developer.eww4@gmail.com';
        $config['smtp_pass'] = 'Dev@123*';
        $config['charset'] = "utf-8";
        $config['mailtype'] = "html";
        $config['newline'] = "\r\n";

        $ci->email->initialize($config);
        //$ci->email->set_newline("\r\n");

        $ci->email->from('developer.eww4@gmail.com','Chickpick');           
        $ci->email->to($to);
        // $ci->email->reply_to('my-email@gmail.com', 'Explendid Videos');
        $ci->email->subject($subject);
        $ci->email->message($message);
        //$ci->email->attach('http://example.com/filename.pdf');
        //$ci->email->attach('/path/to/photo3.jpg');
        $ci->email->send();
        echo $ci->email->print_debugger();exit;
       
    }
}

if (!function_exists('fixRotate')) {
    function fixRotate($image) {
        $filename = $image;
        $CI =& get_instance();
        $CI->load->library('image_lib');
        $config['image_library'] = 'gd2';
        $config['source_image'] = $filename;
        $config['new_image'] = $filename;
        $exif = exif_read_data($config['source_image']);
        $exif['Orientation'] = 5;
        if ($exif && isset($exif['Orientation'])) {
            $ort = $exif['Orientation'];

            if ($ort == 6 || $ort == 5) {
                $config['rotation_angle'] = '270';
            }

            if ($ort == 3 || $ort == 4) {
                $config['rotation_angle'] = '180';
            }

            if ($ort == 8 || $ort == 7) {
                $config['rotation_angle'] = '90';
            }

        }
        $CI->image_lib->initialize($config);
        if (!$CI->image_lib->rotate()) {
            echo $CI->image_lib->display_errors();
        }
        $CI->image_lib->clear();
    }
}
