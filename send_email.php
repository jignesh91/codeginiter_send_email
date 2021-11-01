// set config variable
$config['smtp_user'] = "email";
$config['smtp_pass'] = "password";

// add below code in controller method in which you want to send email
$subject = $this->config->item('site_name').' - "(Subject Name)" ';
$path = BASE_URL().'(path/filename.html)';
$template = file_get_contents($path);                     
$template = $this->create_email_template($template);

$template = str_replace('##FULLNAME##', $post_fields['fullname'], $template);
$template = str_replace('##EMAIL##', $_POST['email'], $template);

$this->send_mail($_POST['email'],$subject,$template);

// create_email_template function for replace some basic content in email template
function create_email_template($template)
{
    $ci=& get_instance();
    $base_url = BASE_URL();
    $template = str_replace('##SITEURL##', $base_url, $template);
    $template = str_replace('##SITENAME##', $ci->config->item('site_name'), $template);
    $template = str_replace('##SITEEMAIL##', $ci->config->item('site_email'), $template);
    $template = str_replace('##COPYRIGHTS##', $ci->config->item('copyrights'), $template);
    $template = str_replace('##EMAILTEMPLATEHEADERLOGO##', $ci->config->item('email_template_header_logo'), $template);
    $template = str_replace('##EMAILTEMPLATEFOOTERLOGO##', $ci->config->item('email_template_footer_logo'), $template);

    return $template;
}
// send_email on single email
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

// send email on multiple emails

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
