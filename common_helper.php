<?php

if (!function_exists('check_file_exist'))
{   
    function check_file_exist($file)
    {
        $file = file_exists($file) ? BASE_URL().$file : BASE_URL().'assets/images/common/no-image.jpg';
        return $file;
    }
}

if (!function_exists('check_data'))
{   
    function check_data($table,$id)
    {
        $ci=& get_instance();
        $data = $ci->db->get_where($table,array('id'=>$id))->num_rows();        
        return $data;
    }
}

if (!function_exists('additional_date'))
{   
    function additional_date($day)
    {
        $curr_date =date('Y-m-d', strtotime('+7 days'));
        return $curr_date;
    }
}

if (!function_exists('check_data_with_key'))
{   
    function check_data_with_key($table,$key,$value)
    {
        $ci=& get_instance();
        $data = $ci->db->get_where($table,array($key=>$value,'trash'=>0))->num_rows();        
        return $data;
    }
}

if (!function_exists('get_single_record'))
{   
    function get_single_record($table,$key,$value)
    {
        $ci=& get_instance();
        $ci->db->order_by('id','desc');
        $data = (array)$ci->db->get_where($table,array($key=>$value))->row();        
        return $data;
    }
}
if (!function_exists('get_multi_record'))
{   
    function get_multi_record($table,$key,$value)
    {
        $ci=& get_instance();
        $data = $ci->db->get_where($table,array($key=>$value))->result_array();        
        return $data;
    }
}

if (!function_exists('get_single_record_with_multi'))
{   
    function get_single_record_with_multi($table,$array)
    {
        $ci=& get_instance();
        $data = (array)$ci->db->get_where($table,array($array))->row();        
        return $data;
    }
}

if (!function_exists('num_format_value'))
{   
    function num_format_value($value)
    { 
        $num_fromated_value = number_format($value,2);
        return $num_fromated_value;
    }
}

if (!function_exists('round_value'))
{   
    function round_value($value)
    { 
        $rounded_value = round($value,2);
        return $rounded_value;
    }
} 

if (!function_exists('convert_seconds'))
{   
    function convert_seconds($value)
    { 
        $timeformat = gmdate("H:i:s", $value);
        return $timeformat;
    }
}
if (!function_exists('append_currency'))
{   
    function append_currency($value)
    { 
        $ci=& get_instance();
        $currency = $ci->config->item('currency');
        $value = $currency.number_format($value,2);
        return $value;
    }
}

if (!function_exists('_pre'))
{   
    function _pre($array)
    {
        echo "<pre>";
        print_r($array);
        echo "<pre>";
        exit;
    }
}


if (!function_exists('send_mail_to_multiple'))
{
    function send_mail_to_multiple($to,$subject,$message)
    {
        //print_r($message);exit;
        $ci=& get_instance();
        $mail_type = $ci->config->item('mail_type');
        if($mail_type == 'SMTP')
        {
            $receivers = array();
            if(count($to) > $ci->config->item('bulk_sending_limit'))
            {
                $i = 1;
                $j = 0;

                $receivers = array();
                foreach($to as $receiver)
                {
                    $receivers[$j][] = $receiver;
                    if($i % $ci->config->item('bulk_sending_limit') == 0)
                    {
                        $j++;
                    }
                    $i++;
                }
            }
            else
            {
                $receivers[0] = $to;
            }


            foreach($receivers as $batch_row)
            {
                //print_r($batch_row);exit;
                $config['protocol'] = "smtp";
                $config['smtp_host'] = "ssl://smtp.gmail.com";
                $config['smtp_port'] = "465";
                $config['smtp_user'] = $ci->config->item('smtp_user');
                $config['smtp_pass'] = $ci->config->item('smtp_pass');
                $config['charset'] = "utf-8";
                $config['mailtype'] = "html";
                $config['newline'] = "\r\n";
                $config['bcc_batch_mode'] = TRUE;
                $config['bcc_batch_size'] = count($ci->config->item('bulk_sending_limit'));

                $ci->email->initialize($config);

                $ci->email->from($ci->config->item('from'),$ci->config->item('header'));           
                $ci->email->bcc($batch_row);
                $ci->email->subject($subject);
                $ci->email->message($message);
                $ci->email->send();
            }


           
        }
        else if($mail_type == 'mandrill')
        {
            $ci->load->config('mandrill');
            $ci->load->library('mandrill');

            $mandrill_ready = NULL;

            try 
            {
                $ci->mandrill->init( $ci->config->item('mandrill_api_key') );
                $mandrill_ready = TRUE;
                
            } 
            catch(Mandrill_Exception $e) 
            {
                $mandrill_ready = FALSE;                
            }

            if($mandrill_ready) 
            {

                //Send us some email!
                $email = array(
                    'html' => $message, //Consider using a view file
                   // 'text' => ,
                    'subject' => $subject,
                    'from_email' => $ci->config->item('from_email'),
                    'from_name' => $ci->config->item('from_name'),
                    'to' => array(array('email' => $to)) //Check documentation for more details on ci one
                    //'to' => array(array('email' => 'joe@example.com' ),array('email' => 'joe2@example.com' )) //for multiple emails
                    );

                $result = $ci->mandrill->messages_send($email);
                
            } 
        }  
    }
}

if (!function_exists('send_mail'))
{
    function send_mail2($to,$subject,$message)
    {
        //print_r($message);exit;
        $ci=& get_instance();
        $mail_type = $ci->config->item('mail_type');
        if($mail_type == 'SMTP')
        {
            $url = 'http://email.excellentwebworld.in/index.php';

            $params['to'] = $to;
            $params['subject'] = $subject;
            $params['message'] = $message;

            $request = $url;
            $session = curl_init($request);

            curl_setopt ($session, CURLOPT_POST, true);
            curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
            curl_setopt($session, CURLOPT_HEADER, false);
            curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($session);
            curl_close($session);
        }
        else if($mail_type == 'mandrill')
        {
            $ci->load->config('mandrill');
            $ci->load->library('mandrill');

            $mandrill_ready = NULL;

            try 
            {
                $ci->mandrill->init( $ci->config->item('mandrill_api_key') );
                $mandrill_ready = TRUE;
                
            } 
            catch(Mandrill_Exception $e) 
            {
                $mandrill_ready = FALSE;                
            }

            if($mandrill_ready) 
            {

                //Send us some email!
                $email = array(
                    'html' => $message, //Consider using a view file
                   // 'text' => ,
                    'subject' => $subject,
                    'from_email' => $ci->config->item('from_email'),
                    'from_name' => $ci->config->item('from_name'),
                    'to' => array(array('email' => $to)) //Check documentation for more details on ci one
                    //'to' => array(array('email' => 'joe@example.com' ),array('email' => 'joe2@example.com' )) //for multiple emails
                    );

                $result = $ci->mandrill->messages_send($email);
                
            } 
        }  
    }
}
if (!function_exists('send_mail'))
{
    function send_mail($to,$subject,$message)
    {
        
        $ci=& get_instance();
        $mail_type = $ci->config->item('mail_type');
        if($mail_type == 'SMTP')
        {
            $config['protocol'] = "smtp";
            $config['smtp_host'] = "smtp.gmail.com";
            $config['smtp_port'] = "587";
            $config['_smtp_auth'] = TRUE;
            $config['smtp_user'] = $ci->config->item('smtp_user');
            $config['smtp_pass'] = $ci->config->item('smtp_pass');
            $config['smtp_crypto'] = 'tls';
            $config['charset'] = "utf-8";
            $config['mailtype'] = "html";
            // $config['newline'] = "\r\n";

            $ci->email->initialize($config);
            $ci->email->set_newline("\r\n");
            $ci->email->from($ci->config->item('from'),$ci->config->item('header'));           
            $ci->email->to($to);
            // $ci->email->reply_to('my-email@gmail.com', 'Explendid Videos');
            $ci->email->subject($subject);
            $ci->email->message($message);
            //$ci->email->attach('http://example.com/filename.pdf');
            //$ci->email->attach('/path/to/photo3.jpg');
            $ci->email->send();

          // echo $ci->email->print_debugger();
           // exit;
        }
        else if($mail_type == 'mandrill')
        {
            $ci->load->config('mandrill');
            $ci->load->library('mandrill');

            $mandrill_ready = NULL;

            try 
            {
                $ci->mandrill->init( $ci->config->item('mandrill_api_key') );
                $mandrill_ready = TRUE;
                
            } 
            catch(Mandrill_Exception $e) 
            {
                $mandrill_ready = FALSE;                
            }

            if($mandrill_ready) 
            {

                //Send us some email!
                $email = array(
                    'html' => $message, //Consider using a view file
                   // 'text' => ,
                    'subject' => $subject,
                    'from_email' => $ci->config->item('from_email'),
                    'from_name' => $ci->config->item('from_name'),
                    'to' => array(array('email' => $to)) //Check documentation for more details on ci one
                    //'to' => array(array('email' => 'joe@example.com' ),array('email' => 'joe2@example.com' )) //for multiple emails
                    );

                $result = $ci->mandrill->messages_send($email);
                
            } 
        }  
    }
}



if (!function_exists('send_push'))
{   
    function send_push($user_id = '',$user_type = '',$push_title = '',$push_message = '',$message_type = '',$device_type = '',$device_token='')
    {
        $ci=& get_instance();
        if($user_id != '' && $user_type != '')
        {
            if($device_type != '' && $device_token !='')
            {
                $row['device_type'] = $device_type;
                $row['device_token'] = $device_token;
            }
            else
            {
                $ci->db->select(array('device_type','device_token'));
                $row = (array)$ci->db->get_where($user_type,array('id' => $user_id))->row();
            }
            

            $key = $ci->config->item('fcm_key');
           // define('API_ACCESS_KEY', $key);
            $token = $row['device_token']; 

            if($row['device_type'] == 'ios')
            {
                $fcmFields = array(
                    'priority' => 'high',
                    'to' => $token,
                    'sound' => 'default',
                    'notification' => array( 
                        "title"=> $push_title,
                        "body"=> $push_message,
                        "type"=> $message_type,
                        "sound"=> "echo_ringtone.mp3",
                        )
                    );
            }
            else
            {
                $fcmFields = array(
                    'priority' => 'high',
                    'to' => $token,
                    'sound' => 'default',
                    'data' => array( 
                        "title"=> $push_title,
                        "body"=> $push_message,
                        "type"=> $message_type,
                        )
                    );
            }
            
            $headers = array('Authorization: key=' . $key,'Content-Type: application/json');
             
            $ch = curl_init();
            curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
            curl_setopt( $ch,CURLOPT_POST, true );
            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fcmFields ) );
            $result = curl_exec($ch );
            curl_close( $ch );
            // echo $result . "\n\n";

            $insert_data['user_type'] = $user_type;
            $insert_data['user_id'] = $user_id;
            $insert_data['title'] = $push_title;
            $insert_data['description'] = $push_message;
            $insert_data['read_status_user'] = 0;
            $insert_data['created_date'] = date('Y-m-d H:i:s');
            $ci->db->insert('notification',$insert_data);

        }
    }
}

if (!function_exists('send_push_bulk1'))
{   
    function send_push_bulk1($user_ids = '',$user_type = '',$push_title = '',$push_message = '',$message_type = '')
    {
        $ci=& get_instance();
        if($user_ids != '' && $user_type != '')
        {
            if($user_ids != 'all')
            {
               $ci->db->where_in('id',$user_ids); 
            }
            // else
            // {
            //     $user_ids = array(6,11,21,94);
            //     $ci->db->where_in('id',$user_ids); 
            // }
            
            $ci->db->select('device_token');
            $users = $ci->db->get_where($user_type,array('status' => 1 , 'trash' => 0))->result_array();
            
            $tokens = array();
            foreach($users as $row)
            {
                if($row['device_token'] != '' && strlen($row['device_token']) > 150 )
                {
                    $tokens[] = $row['device_token'];
                }
            }

            $receivers = array();
            if(count($tokens) > $ci->config->item('bulk_sending_limit'))
            {
                $i = 1;
                $j = 0;

                $receivers = array();
                foreach($tokens as $receiver)
                {
                    $receivers[$j][] = $receiver;
                    if($i % $ci->config->item('bulk_sending_limit') == 0)
                    {
                        $j++;
                    }
                    $i++;
                }
            }
            else
            {
                $receivers[0] = $tokens;
            }
            
            
            
            $key = $ci->config->item('fcm_key');

            foreach($receivers as $batch_row)
            {
                //print_r($batch_row);exit;
                $fcmFields = array(
                    'priority' => 'high',
                    'registration_ids' => $batch_row,
                    // 'sound' => 'echo_ringtone.mp3',
                    'data' => array( 
                        "title"=> $push_title,
                        "body"=> $push_message,
                        "type"=> $message_type,
                        "sound" => "echo_ringtone.mp3",
                        ),
                    'notification' => array( 
                        "title"=> $push_title,
                        "body"=> $push_message,
                        "type"=> $message_type,
                        "sound" => "echo_ringtone.mp3",
                        )
                    );
               

                
                $headers = array('Authorization: key=' . $key,'Content-Type: application/json');
             
                $ch = curl_init();
                curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                curl_setopt( $ch,CURLOPT_POST, true );
                curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fcmFields ) );
                $result = curl_exec($ch );
                curl_close( $ch );
                echo $result . "\n\n";
                exit;
            }
                
        }
    }
}



if (!function_exists('send_push_with_data'))
{   
    function send_push_with_data($user_id = '',$user_type = '',$push_title = '',$push_message = '',$message_type = '',$data = [])
    {

        $ci=& get_instance();
        if($user_id != '' && $user_type != '')
        {
            $ci->db->select(array('device_type','device_token'));
            $row = (array)$ci->db->get_where($user_type,array('id' => $user_id))->row();

            $key = $ci->config->item('fcm_key');
           // define('API_ACCESS_KEY', $key);
            $token = $row['device_token']; 
            // print_r($row);
            // exit;
            if($row['device_type'] == 'ios')
            {
                $fcmFields = array(
                    'priority' => 'high',
                    'to' => $token,
                    'sound' => 'default',
                    'notification' => array( 
                        "title"=> $push_title,
                        "body"=> $push_message,
                        "type"=> $message_type,
                        "data"=>$data,
                        "sound" => "echo_ringtone.mp3",
                        )
                    );
            }
            else
            {
                $fcmFields = array(
                    'priority' => 'high',
                    'to' => $token,
                    'sound' => 'default',
                    'data' => array( 
                        "title"=> $push_title,
                        "body"=> $push_message,
                        "type"=> $message_type,
                        "data"=>$data,
                        )
                    );

            }

            // echo "<pre>";
            // print_r($fcmFields);
            // exit;

            $headers = array('Authorization: key=' . $key,'Content-Type: application/json');
             
            $ch = curl_init();
            curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
            curl_setopt( $ch,CURLOPT_POST, true );
            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fcmFields ) );
            $result = curl_exec($ch );
            curl_close( $ch );

            $insert_data['user_type'] = $user_type;
            $insert_data['user_id'] = $user_id;
            $insert_data['title'] = $push_title;
            $insert_data['description'] = $push_message;
            $insert_data['read_status_user'] = 0;
            $insert_data['created_date'] = date('Y-m-d H:i:s');
            $ci->db->insert('notification',$insert_data);
            // echo $result . "\n\n";
        }
    }
}


if (!function_exists('send_sms'))
{   
    function send_sms($mobile_no = "" , $message = "")
    {
        if($mobile_no != "" && $message != "")
        {
            $country_code = substr($mobile_no, 0, 3);
            if($country_code == '254')
            {
                $ci=& get_instance();

                $url = "https://mysms.celcomafrica.com/api/services/sendsms/?apikey=".$ci->config->item('sms_key')."&partnerID=".$ci->config->item('partner_id')."&message=".urlencode($message)."&shortcode=".$ci->config->item('shortcode')."&mobile=".$mobile_no;

                $result = file_get_contents($url);
                $result = json_decode($result,true);
                return $result; 
            }
            else 
            {
                $apiKey = urlencode("Wag0sVRcAO8-8VlNjfM6FY1mRLSWsWNMTEXOhbs6tz");
                
                $numbers = $mobile_no;

                if(strlen($mobile_no) == 10)
                {
                    $numbers = '91'.$mobile_no;
                }
                $sender = urlencode('TXTLCL');
                $message = rawurlencode($message);
                
                $data = array('apikey' => $apiKey, 'numbers' => $numbers, 'sender' => $sender, 'message' => $message);
                
                $ch = curl_init('https://api.textlocal.in/send/');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);

                return $result; 
            }
            
        }
        else
        {
            return false;
        }
    }
}

if (!function_exists('send_push_bulk'))
{   
    function send_push_bulk($user_id = '',$user_type = '',$device_type = '',$device_token ='',$push_title = '',$push_message = '',$message_type = '')
    {
        $ci=& get_instance();
        if($user_id != '' && $user_type != '')
        {           

            $key = $ci->config->item('fcm_key');
            define('API_ACCESS_KEY', $key);
            $token = $device_token; 

            if($device_type == 'ios')
            {
                $fcmFields = array(
                    'priority' => 'high',
                    'to' => $token,
                    'sound' => 'default',
                    'notification' => array( 
                        "title"=> $push_title,
                        "body"=> $push_message,
                        "type"=> $message_type,
                        "sound" => "echo_ringtone.mp3",
                        )
                    );
            }
            else
            {
                $fcmFields = array(
                    'priority' => 'high',
                    'to' => $token,
                    'sound' => 'default',
                    'data' => array( 
                        "title"=> $push_title,
                        "body"=> $push_message,
                        "type"=> $message_type,
                        )
                    );
            }

            $headers = array('Authorization: key=' . API_ACCESS_KEY,'Content-Type: application/json');
             
            $ch = curl_init();
            curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
            curl_setopt( $ch,CURLOPT_POST, true );
            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fcmFields ) );
            $result = curl_exec($ch );
            curl_close( $ch );
            //echo $result . "\n\n";
        }
    }
}


if (!function_exists('create_email_template'))
{
    function create_email_template($template)
    {
        $ci=& get_instance();
        $base_url = BASE_URL();
        $template = str_replace('##SITEURL##', $base_url, $template);
        $template = str_replace('#23262F', '#ffffff', $template);
        $template = str_replace('##SITENAME##', $ci->config->item('site_name'), $template);
        $template = str_replace('##SITEEMAIL##', $ci->config->item('site_email'), $template);
        $template = str_replace('##COPYRIGHTS##', $ci->config->item('copyrights'), $template);
        $template = str_replace('##EMAILTEMPLATEHEADERLOGO##', $ci->config->item('email_template_header_logo'), $template);
        $template = str_replace('##EMAILTEMPLATEFOOTERLOGO##', $ci->config->item('email_template_footer_logo'), $template);
        $template = str_replace('##CURRENCY##', $ci->config->item('currency'), $template);         
        return $template;
    }
}

if (!function_exists('generate_qr_code'))
{
    function generate_qr_code($string)
    {
        $url = '';
        if($string != '')
        {
            $unique_code = base64_encode($string);
            
            $googleapis = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=".$unique_code."&choe=UTF-8";
            $file = file_get_contents($googleapis);

            $path = "/var/www/html/assets/images/qrcode/".$unique_code.".png";
            file_put_contents($path, $file);

            $url = "assets/images/qrcode/".$unique_code.".png";
        }
        return $url;
    }
}

if (!function_exists('upload_image2'))
{
    function upload_image2($image_name,$folder_name,$id)
    {
        $ci=& get_instance();
        if(!is_dir('assets/images/'.$folder_name.'/')){
            mkdir('assets/images/'.$folder_name, 0777,true);         
            chmod('assets/images/'.$folder_name, 0777);
        }
        if(!is_dir('assets/images/'.$folder_name.'/'.$id.'/')){
            mkdir('assets/images/'.$folder_name.'/'.$id, 0777,true);         
            chmod('assets/images/'.$folder_name.'/'.$id, 0777);
        }
        $config['upload_path']   = 'assets/images/'.$folder_name.'/'.$id;
        $config['allowed_types'] = '*';
        $config['max_size']      = 20240;
        $config['encrypt_name'] = TRUE;
        $ci->load->library('upload', $config);
        if (!$ci->upload->do_upload($image_name)) {
            $error            = $ci->upload->display_errors();
            $return['status'] = 0;
            $return['error']  = $error;
        } else {
            $upload_data      = $ci->upload->data();
            $file_name        = $upload_data['file_name'];
            $return['status'] = 1;
            $return['path']   = 'assets/images/'.$folder_name.'/'.$id.'/'.$file_name;
        }
        return $return;
    }
}

if (!function_exists('upload_image'))
{    
    function upload_image($image_name,$folder_name,$id)
    {
        $CI = get_instance();
        if(!is_dir('assets/uploads/'.$folder_name.'/')){
            mkdir('assets/uploads/'.$folder_name, 0777,true);         
            chmod('assets/uploads/'.$folder_name, 0777);
        }
        if(!is_dir('assets/images/'.$folder_name.'/'.$id.'/')){
            mkdir('assets/images/'.$folder_name.'/'.$id, 0777,true);         
            chmod('assets/images/'.$folder_name.'/'.$id, 0777);
        }
        $config['upload_path']   = 'assets/images/'.$folder_name.'/'.$id;
        $config['allowed_types'] = '*';
        $config['max_size']      = 20240;
        $config['encrypt_name'] = TRUE;
        //$config['image_lib'] = 'gd2';
        $CI->load->library('upload', $config);
        if (!$CI->upload->do_upload($image_name)) {
            $error            = $CI->upload->display_errors();
            $return['status'] = 0;
            $return['error']  = $error;
        } else {
            $upload_data      = $CI->upload->data();
            $file_name        = $upload_data['file_name'];
            $return['status'] = 1;
            $return['path']   = 'assets/images/'.$folder_name.'/'.$id.'/'.$file_name;
        }
        $quality = 70;
        $source_url = $return['path'];
        $destination_url = $return['path'];
        $info = getimagesize($source_url);

        //print_r($info);exit;

        if ($info['mime'] == 'image/jpeg')
        $image = imagecreatefromjpeg($source_url);

        elseif ($info['mime'] == 'image/gif')
        $image = imagecreatefromgif($source_url);

        elseif ($info['mime'] == 'image/png')
        $image = imagecreatefrompng($source_url);

        imagejpeg($image, $destination_url, $quality);
        return $return;
    }
}

if (!function_exists('move_image'))
{
    function move_image($driver_id,$img_path_temp)
    {
        if(!is_dir('assets/images/driver/'.$driver_id.'/')){
            mkdir('assets/images/driver/'.$driver_id, 0777,true);         
            chmod('assets/images/driver/'.$driver_id, 0777);
        }

        $destination='/var/www/html/assets/images/driver/'.$driver_id;
        $org_image='/var/www/html/'.$img_path_temp;

        $img_name=basename($org_image);

        if(rename( $org_image , $destination.'/'.$img_name ))
        {
            return 'assets/images/driver/'.$driver_id.'/'.$img_name;
        } 
        else 
        {
            return "";
        }
        @unlink($org_image);
    }
}

if (!function_exists('validate_post_fields'))
{
    function validate_post_fields($post_fields)
    {
        $error = array();
        foreach ($post_fields as $field => $value) 
        {
            if(!isset($field) || $value == '' || is_null($value) || $value == 'undefined')
            {
                $error[]= $field ." paramter missing"; 
            }
        }
        return $error;
    }
}

if (!function_exists('validate_file_fields'))
{
    function validate_file_fields($post_fields)
    {
        $error = array();
        foreach ($file_fields as $field => $value) 
        {
            if($_FILES[$field]['name'] == '' ||  $_FILES[$field]['name'] == null)
            {
                $error[]= $field ." File Is Missing"; 
            }
        }
        return $error;
    }

}

if (!function_exists('string_encrypt'))
{
    function string_encrypt($string) 
    {
        $ci=& get_instance(); 
        $key = $ci->config->item('custom_encryption_key');
        $result = '';
        for($i=0, $k= strlen($string); $i<$k; $i++) 
        {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key))-1, 1);
            $char = chr(ord($char)+ord($keychar));
            $result .= $char;
        }
        return base64_encode($result);
    }
}

if (!function_exists('string_decrypt'))
{
    function string_decrypt($string) 
    {
        $ci=& get_instance(); 
        $key = $ci->config->item('custom_encryption_key');
        $result = '';
        $string = base64_decode($string);
        for($i=0,$k=strlen($string); $i< $k ; $i++) 
        {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key))-1, 1);
            $char = chr(ord($char)-ord($keychar));
            $result.=$char;
        }
        return $result;       
    }
}

if (!function_exists('check_valid_card'))
{
    function check_valid_card($number) 
    {
          $number=preg_replace('/\D/', '', $number);
          $number_length=strlen($number);
          $parity=$number_length % 2;
          $total=0;
          for ($i=0; $i<$number_length; $i++) {
            $digit=$number[$i];
            if ($i % 2 == $parity) {
              $digit*=2;
              if ($digit > 9) {
                $digit-=9;
              }
            }
            $total+=$digit;
          }
          return ($total % 10 == 0) ? TRUE : FALSE;
    }
}
   

if (!function_exists('get_card_type'))
{
    function get_card_type($str, $format = 'string')
    {
        if (empty($str)) {
            return false;
        }

        $matchingPatterns = [
            'visa' => '/^4[0-9]{12}(?:[0-9]{3})?$/',
            'mastercard' => '/^5[1-5][0-9]{14}$/',
            'amex' => '/^3[47][0-9]{13}$/',
            'diners' => '/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/',
            'discover' => '/^6(?:011|5[0-9]{2})[0-9]{12}$/',
            'jcb' => '/^(?:2131|1800|35\d{3})\d{11}$/',
            'any' => '/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|(?:2131|1800|35\d{3})\d{11})$/'
        ];

        $ctr = 1;
        $type = 'other';
        foreach ($matchingPatterns as $key=>$pattern) {
            if (preg_match($pattern, $str)) {
                 $type = $key ;
                 break;
            }
            $ctr++;
        }
        return $type;
    }
}

if (!function_exists('pay_post'))
{   
    function pay_post($card_array = [])
    {
        if(!empty($card_array))
        {
            $ci=& get_instance();

            //$ci->load->library(); 

            $res['payment_status'] = 'success';
            $res['reference_id'] = rand(11111111,99999999);
            $data['status'] = true;
            $data['data']=$res;

        }else
        {

            $data['status'] = false;
            $data['data']=[];
        }
        return $data;
    }
}

if (!function_exists('get_distance'))
{   
    function get_distance($pickup_origins = "",$dropoff_origins = "")
    {
        //error_reporting(0);   

        if($pickup_origins != "" && $dropoff_origins != "")
        {
            $ci=& get_instance();  
            $key = $ci->config->item('google_map_key');        
            $distance_url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$pickup_origins."&destinations=".$dropoff_origins."&mode=driving&key=".$key;
            // echo $distance_url; exit;
            $distance = (array)json_decode(file_get_contents($distance_url));
            $distance_type = $ci->config->item('distance_type');     

            if(isset($distance['rows'][0]->elements[0]->distance->value))
            {
                
                $meters = $distance['rows'][0]->elements[0]->distance->value;
                if($distance_type == 'km')
                {
                    $total_distance = $meters / 1000;
                }
                else
                {
                    $total_distance = ($meters / 1609.34);
                }            
                $duration = $distance['rows'][0]->elements[0]->duration->value;
                
                $data['dropoff_location'] = '';
                if(isset($distance['destination_addresses'][0]))
                {
                    $data['dropoff_location'] =  $distance['destination_addresses'][0];
                }
                $data['distance'] =  (Float)$total_distance;
                $data['duration_in_second'] =  !isset($duration)?0:$duration;
                $data['duration_in_minute'] =  (Int)($duration / 60);    
            }
            else
            {
                $data['dropoff_location'] = '';
                $data['distance'] =  0;
                $data['duration_in_second'] =  0;
                $data['duration_in_minute'] =  0;  
            }   

            // if(isset($_POST['debug']) && $_POST['debug'] == 1)
            // {
                // print_r($data);exit;
            // }  
                    
            return $data;

        }
        else
        {
            $data['distance'] =  0;
            $data['duration_in_second'] =  0;
            $data['duration_in_minute'] =  0;            
            return $data;
        }        
    }
}
if (!function_exists('distance'))
{
    function distance($lat1, $lon1, $lat2, $lon2, $unit)
    {
        if (($lat1 == $lat2) && ($lon1 == $lon2))
        {
            return 0;
        }
        else 
        {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);

            if ($unit == "K") {
              return ($miles * 1.609344);
            } else if ($unit == "N") {
              return ($miles * 0.8684);
            } else {
              return $miles;
            }
        }
    }
}


if (!function_exists('validate_location'))
{   
    function validate_location($lat = "",$lng = "")
    {
        if($lat != "" && $lng != "" &&  $lat != undefined && $lng != undefined && $lat != null && $lng != null)
        {
            return true;
        }
        else
        {
            return false;   
        }        
    }
}


