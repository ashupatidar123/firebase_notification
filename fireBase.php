<?php

	//define one time function 
	public function push_notification($data,$user_id)
    {
        $where = array('id'=>$user_id);
        $userData = (array)  $this->CI->model->getsingle('users', $where);
        $fcm_tokan = $userData['fcm_tokan'];        
        $reg_id = $fcm_tokan;

        $apiKey ="AAAAszRLVXk:APA91bGwwiAQR_Zln9v7oOD13FyIx88_BKl79zmonms29Ie7AveyW07YNX20mRTONWL_UvSuZpSr4te2P0JpOqj0MIumFIiUUx4-otR_01QxhApOW-EfZe9kmAZaL9UJT5PD2R7Pi2cF";
        
        $post = array(
             //'to'  => $reg_id,
             'registration_ids' => array($reg_id),
             'notification' => $data
             //'data' => $data
        );
         //print_r($post); die();
         
         // Set CURL request headers 
        $headers = array( 
            'Authorization: key= ' . $apiKey,
            'Content-Type: application/json'
        );

         // Initialize curl handle       
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');

         // Set request method to POST       
         curl_setopt($ch, CURLOPT_POST, true);

         // Set custom request headers       
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

         // Get the response back as string instead of printing it       
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

         // Set JSON post data
         curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Actually send the request    
         $result = curl_exec($ch);

         // Handle errors
        if(curl_errno($ch)){
            echo 'GCM error: ' . curl_error($ch);
        }
        // Close curl handle
        curl_close($ch);
	}


	// function calling id (login user id)
	$data = array(
		'title'=>'Welcome',
		'body'=>"You have register successfully",
		'user_id'=>$id
	);
	$this->controller->push_notification($data,$id);

?>