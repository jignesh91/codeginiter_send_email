<?php

function getKey($seckey){
	$hashedkey = md5($seckey);
	$hashedkeylast12 = substr($hashedkey, -12);

	$seckeyadjusted = str_replace("FLWSECK-", "", $seckey);
	$seckeyadjustedfirst12 = substr($seckeyadjusted, 0, 12);

	$encryptionkey = $seckeyadjustedfirst12.$hashedkeylast12;
	return $encryptionkey;

}

function encrypt3Des($data, $key)
{
  	$encData = openssl_encrypt($data, 'DES-EDE3', $key, OPENSSL_RAW_DATA);
    return base64_encode($encData);
}

function add_money_via_flutterwave($data = array()){ 
    
    
    ini_set('display_errors',1);

    $CI = get_instance();
    $PBFPubKey = $CI->config->item('FPublicKey');
    $SecretKey = $CI->config->item('FSecretKey');
    
    // $data = array('PBFPubKey' => $PBFPubKey,
    // 'cardno' => '5438898014560229',
    // 'currency' => 'KES',
    // 'country' => 'KE',
    // 'cvv' => '789',
    // 'amount' => '1.5',
    // 'expiryyear' => '19',
    // 'expirymonth' => '09',
    // 'suggested_auth' => 'pin',
    // 'pin' => '3310',
    // 'email' => 'tester@flutter.co',
    // 'IP' => $_SERVER['REMOTE_ADDR'],
    // 'txRef' => 'MXX-ASC-4578',
    // "redirect_url"=> "https://www.peppea.com/panel/flutterwave/payment_response",
    // 'device_fingerprint' => '69e6b7f0sb72037aa8428b70fbe03986c');

    // echo "<pre>";
    // print_r($data);
    // exit;
    
    $SecKey = $SecretKey;
    
    $key = getKey($SecKey); 
    
    $dataReq = json_encode($data);
    //print_r($data);exit;

    
    $post_enc = encrypt3Des( $dataReq, $key );


    //var_dump($dataReq);
    
    $postdata = array(
     'PBFPubKey' => $PBFPubKey,
     'client' => $post_enc,
     'alg' => '3DES-24');
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, "https://api.ravepay.co/flwv3-pug/getpaidx/api/charge");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata)); //Post Fields
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 200);
    curl_setopt($ch, CURLOPT_TIMEOUT, 200);
    
    
    $headers = array('Content-Type: application/json');
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $request = curl_exec($ch);

    $result = json_decode($request, true);
    //print_r($result);exit;

    //print_r($result);exit;
    
    // if ($request) {
    //     $result = json_decode($request, true);
    //     echo "<pre>";
    //     print_r($result);
    // }else{
    //     if(curl_error($ch))
    //     {
    //         echo 'error:' . curl_error($ch);
    //     }
    // }
    
    curl_close($ch);

    if(isset($result['status']) && $result['status'] == 'success')
    {
    	$gateway_response['data']['payment_status'] = 'success';
    	$gateway_response['data']['reference_id'] = $result['data']['txRef'];
    	if(isset($result['data']['authModelUsed']) && ($result['data']['authModelUsed'] == 'ACCESS_OTP' || $result['data']['authModelUsed'] == 'VBVSECURECODE' ))
    	{
    		$gateway_response['data']['auth'] = 1;
    		$gateway_response['data']['url'] = str_replace(' ','%20',$result['data']['authurl']);
    	}
    	else
    	{
    		$gateway_response['data']['auth'] = 0;
    	}
    }
    else
    {
    	$gateway_response['data']['payment_status'] = 'failed';
    	$gateway_response['data']['reference_id'] = '';
    }

    return $gateway_response;
}

function bulk_miles_purchase_via_flutterwave($data = array()){ 
    
    
    ini_set('display_errors',1);

    $CI = get_instance();
    $PBFPubKey = $CI->config->item('FPublicKey');
    $SecretKey = $CI->config->item('FSecretKey');
    
    $SecKey = $SecretKey;
    
    $key = getKey($SecKey); 
    
    $dataReq = json_encode($data);
    
    $post_enc = encrypt3Des( $dataReq, $key );

    //var_dump($dataReq);
    
    $postdata = array(
     'PBFPubKey' => $PBFPubKey,
     'client' => $post_enc,
     'alg' => '3DES-24');
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, "https://api.ravepay.co/flwv3-pug/getpaidx/api/charge");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata)); //Post Fields
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 200);
    curl_setopt($ch, CURLOPT_TIMEOUT, 200);
    
    
    $headers = array('Content-Type: application/json');
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $request = curl_exec($ch);

    $result = json_decode($request, true);
    //_pre($result);
    
    curl_close($ch);

    if(isset($result['status']) && $result['status'] == 'success')
    {
        $gateway_response['data']['payment_status'] = 'success';
        $gateway_response['data']['reference_id'] = $result['data']['txRef'];
        if(isset($result['data']['authModelUsed']) && ($result['data']['authModelUsed'] == 'ACCESS_OTP' || $result['data']['authModelUsed'] == 'VBVSECURECODE' ))
        {
            $gateway_response['data']['auth'] = 1;
            $gateway_response['data']['url'] = str_replace(' ','%20',$result['data']['authurl']);
        }
        else
        {
            $gateway_response['data']['auth'] = 0;
        }
    }
    else
    {
        $gateway_response['data']['payment_status'] = 'failed';
        $gateway_response['data']['reference_id'] = '';
    }

    return $gateway_response;
}

function co_bulk_miles_purchase_via_flutterwave($data = array()){ 
    
    
    ini_set('display_errors',1);

    $CI = get_instance();
    $PBFPubKey = $CI->config->item('FPublicKey');
    $SecretKey = $CI->config->item('FSecretKey');
    
    $SecKey = $SecretKey;
    
    $key = getKey($SecKey); 
    
    $dataReq = json_encode($data);
    
    $post_enc = encrypt3Des( $dataReq, $key );

    //var_dump($dataReq);
    
    $postdata = array(
     'PBFPubKey' => $PBFPubKey,
     'client' => $post_enc,
     'alg' => '3DES-24');
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, "https://api.ravepay.co/flwv3-pug/getpaidx/api/charge");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata)); //Post Fields
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 200);
    curl_setopt($ch, CURLOPT_TIMEOUT, 200);
    
    
    $headers = array('Content-Type: application/json');
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $request = curl_exec($ch);

    $result = json_decode($request, true);
    
    curl_close($ch);

    if(isset($result['status']) && $result['status'] == 'success')
    {
        $gateway_response['data']['payment_status'] = 'success';
        $gateway_response['data']['reference_id'] = $result['data']['txRef'];
        if(isset($result['data']['authModelUsed']) && ($result['data']['authModelUsed'] == 'ACCESS_OTP' || $result['data']['authModelUsed'] == 'VBVSECURECODE' ))
        {
            $gateway_response['data']['auth'] = 1;
            $gateway_response['data']['url'] = str_replace(' ','%20',$result['data']['authurl']);
        }
        else
        {
            $gateway_response['data']['auth'] = 0;
        }
    }
    else
    {
        $gateway_response['data']['payment_status'] = 'failed';
        $gateway_response['data']['reference_id'] = '';
    }

    return $gateway_response;
}

function booking_payment_via_flutterwave($data = array())
{ 
    
    
    ini_set('display_errors',1);

    $CI = get_instance();
    $PBFPubKey = $CI->config->item('FPublicKey');
    $SecretKey = $CI->config->item('FSecretKey');
    
    // $data = array('PBFPubKey' => $PBFPubKey,
    // 'cardno' => '5438898014560229',
    // 'currency' => 'KES',
    // 'country' => 'KE',
    // 'cvv' => '789',
    // 'amount' => '1.5',
    // 'expiryyear' => '19',
    // 'expirymonth' => '09',
    // 'suggested_auth' => 'pin',
    // 'pin' => '3310',
    // 'email' => 'tester@flutter.co',
    // 'IP' => $_SERVER['REMOTE_ADDR'],
    // 'txRef' => 'MXX-ASC-4578',
    // "redirect_url"=> "https://www.peppea.com/panel/flutterwave/payment_response",
    // 'device_fingerprint' => '69e6b7f0sb72037aa8428b70fbe03986c');

    // echo "<pre>";
    // print_r($data);
    // exit;
    
    $SecKey = $SecretKey;
    
    $key = getKey($SecKey); 
    
    $dataReq = json_encode($data);
    
    $post_enc = encrypt3Des( $dataReq, $key );

    //var_dump($dataReq);
    
    $postdata = array(
     'PBFPubKey' => $PBFPubKey,
     'client' => $post_enc,
     'alg' => '3DES-24');
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, "https://api.ravepay.co/flwv3-pug/getpaidx/api/charge");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata)); //Post Fields
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 200);
    curl_setopt($ch, CURLOPT_TIMEOUT, 200);
    
    
    $headers = array('Content-Type: application/json');
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $request = curl_exec($ch);

    $result = json_decode($request, true);

    //print_r($result);exit;
    
    // if ($request) {
    //     $result = json_decode($request, true);
    //     echo "<pre>";
    //     print_r($result);
    // }else{
    //     if(curl_error($ch))
    //     {
    //         echo 'error:' . curl_error($ch);
    //     }
    // }
    
    curl_close($ch);

    if(isset($result['status']) && $result['status'] == 'success')
    {
        $gateway_response['data']['payment_status'] = 'success';
        $gateway_response['data']['reference_id'] = $result['data']['txRef'];
        if(isset($result['data']['authModelUsed']) && ($result['data']['authModelUsed'] == 'ACCESS_OTP' || $result['data']['authModelUsed'] == 'VBVSECURECODE' ))
        {
            $gateway_response['data']['auth'] = 1;
            $gateway_response['data']['url'] = str_replace(' ','%20',$result['data']['authurl']);
        }
        else
        {
            $gateway_response['data']['auth'] = 0;
        }
    }
    else
    {
        $gateway_response['data']['payment_status'] = 'failed';
        $gateway_response['data']['reference_id'] = '';
    }

    //print_r($gateway_response);exit;

    return $gateway_response;
}

function past_due_via_flutterwave($data = array()){ 
    
    
    ini_set('display_errors',1);

    $CI = get_instance();
    $PBFPubKey = $CI->config->item('FPublicKey');
    $SecretKey = $CI->config->item('FSecretKey');
    
    $SecKey = $SecretKey;
    
    $key = getKey($SecKey); 
    
    $dataReq = json_encode($data);
    
    $post_enc = encrypt3Des( $dataReq, $key );

    //var_dump($dataReq);
    
    $postdata = array(
     'PBFPubKey' => $PBFPubKey,
     'client' => $post_enc,
     'alg' => '3DES-24');
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, "https://api.ravepay.co/flwv3-pug/getpaidx/api/charge");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata)); //Post Fields
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 200);
    curl_setopt($ch, CURLOPT_TIMEOUT, 200);
    
    
    $headers = array('Content-Type: application/json');
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $request = curl_exec($ch);

    $result = json_decode($request, true);

    
    
    curl_close($ch);

    if(isset($result['status']) && $result['status'] == 'success')
    {
        $gateway_response['data']['payment_status'] = 'success';
        $gateway_response['data']['reference_id'] = $result['data']['txRef'];
        if(isset($result['data']['authModelUsed']) && ($result['data']['authModelUsed'] == 'ACCESS_OTP' || $result['data']['authModelUsed'] == 'VBVSECURECODE' ))
        {
            $gateway_response['data']['auth'] = 1;
            $gateway_response['data']['url'] = str_replace(' ','%20',$result['data']['authurl']);
        }
        else
        {
            $gateway_response['data']['auth'] = 0;
        }
    }
    else
    {
        $gateway_response['data']['payment_status'] = 'failed';
        $gateway_response['data']['reference_id'] = '';
    }

    return $gateway_response;
}

?>