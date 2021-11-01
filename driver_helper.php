<?php


if (!function_exists('get_driver_profile'))
{	
	function get_driver_profile($driver_id)
	{
        $ci=& get_instance();
        $data = (array)$ci->db->get_where('driver',array('id'=>$driver_id))->row();        
        //$data['vehicle_info'] = $ci->db->get_where('driver_vehicle',array('driver_id'=>$driver_id))->result();
        $data['vehicle_info'] = $ci->db->query('SELECT driver_vehicle.*,vehicle_type.name as vehicle_type_name FROM `driver_vehicle` join vehicle_type on driver_vehicle.vehicle_type = vehicle_type.id where driver_vehicle.driver_id = '.$driver_id)->result();
        $data['driver_docs'] = $ci->db->get_where('driver_docs',array('driver_id'=>$driver_id))->row();
        $x_api_key = (array)$ci->db->get_where('tokens',array('user_type'=>'driver','user_id'=>$driver_id))->row();
        if(!empty($x_api_key))
        {
            $data['x-api-key'] = $x_api_key['token'];    
        }        
        unset($data['password']);
        return $data;        
	}
}



if (!function_exists('get_driver_data'))
{       
        function get_driver_data($driver_id)
        {
        $ci=& get_instance();
        $data = (array)$ci->db->get_where('driver',array('id'=>$driver_id))->row();        
        $vehicles_data = $ci->db->get_where('driver_vehicle',array('driver_id'=>$driver_id))->result_array();
        $i = 0;
        foreach ($vehicles_data as $row) {
             $data['vehicle_info'][$i] = $row;
             $i++;
        }


        $data['driver_docs'] =(array) $ci->db->get_where('driver_docs',array('driver_id'=>$driver_id))->row();
        unset($data['password']);
        return $data;        
        }
}


