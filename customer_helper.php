<?php


if (!function_exists('get_customer_profile'))
{	
	function get_customer_profile($customer_id)
	{
        $ci=& get_instance();
        $data = (array)$ci->db->get_where('customer',array('id'=>$customer_id))->row();        
        $x_api_key = (array)$ci->db->get_where('tokens',array('user_type'=>'customer','user_id'=>$customer_id))->row();
        if(!empty($x_api_key))
        {
            $data['x-api-key'] = $x_api_key['token'];    
        }        
        unset($data['password']);
        return $data;        
	}
}
