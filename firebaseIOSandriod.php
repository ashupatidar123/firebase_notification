<?php

	public function push_notification($data,$userid)
    {
        $andWhere = "userid=$userid";
        $userData = current($this->model->fetchQuery('fcm_id','qa_users',$joinTbl=NULL,$joinId=NULL,$joinLR=NULL,$andWhere));

        $fcm_id = $userData['fcm_id'];

        define('API_ACCESS_KEY', 'AAAAReSe04Y:APA91bG4MBvCZFQ74wobB3aMOlPY9h7dmOIa2BOUhTWz1uJaL-azPSuXE4FHE4h_3sPI7WW0jSs-XZfMaioOiPORAjUtBmH-_8uRcdwyD5qeCp1QmYMOiRqxU6oXkFAvDvX4UHBmj_o7');

        $paylod = array();
        $msg = array();
        
        if($data["device_type"] == 'ios')
        {
            $paylod["title"] = $data["title"];
            $paylod["body"]  = $data["body"];
            if(isset($data["sub_text"])){
                $paylod["sub_text"] = $data["sub_text"];
            }
            if(isset($data["image_url"])){
                $paylod["image_url"] = $data["image_url"];
            }
            if(isset($data["link"])){
              $paylod["link"] = $data["link"];
            }
        }
        else
        {
          //android
            $paylod = array(
              "title"=>$data["title"],
              "body"=>$data["body"],
              "sub_text"=>$data["sub_text"],
            );
        }
        if($data["device_type"] == 'ios')
        {
            $msg["notification"] = $paylod;
            $msg["priority"]     = "high";
        }
        else
        {
            $msg["data"] = $paylod;    
        }

        $msg["registration_ids"] = array($fcm_id);
        //print_r($msg);die;
        $headers = array( 
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );
        
        $ch = curl_init();
        //curl_setopt($ch, CURLOPT_URL, 'https://gcm-http.googleapis.com/gcm/send');
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        // Set request method to POST       
        curl_setopt($ch, CURLOPT_POST, true);
        // Set custom request headers       
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // Get the response back as string instead of printing it       
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Set JSON post data
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($msg));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Actually send the request    
        $result = curl_exec($ch);
        //print_r($result);
        // Handle errors 
        if (curl_errno($ch)) {
            echo 'GCM error: ' . curl_error($ch);
        }
        // Close curl handle
        curl_close($ch);
        /*print_r($result);
        die();*/
        //return json_decode($result);
        return $result;
        // Debug GCM response   
       // echo $apiKey;    
       // echo "<pre>"; 
        //print_r((array)json_decode($result)); 
       // die();
    }



    function notificationFire_post()
    {

        $user_id     = $this->input->post('user_id');
        $device_type  = $this->input->post('device_type');
        
        $checkData  = array('user_id'=>$user_id,'device_type'=>$device_type);
        $required_parameter = array('user_id','device_type');
        $chk_error = $this->check_required_value($required_parameter,$checkData);
        if ($chk_error) {
            $resp = array('code'=>'501','message'=>'Missing '.ucwords($chk_error['param']));
            @$this->response($resp); exit;
        }

        $data = array(
            'body'=>'Test Body',
            'title'=>'Title',
            'sub_text'=>'sub_text',
            'device_type'=>$device_type            
        );

        $returnArr = $this->push_notification($data,$user_id);
        $character = json_decode($returnArr);
        print_r($character); die();
        
        if($character->success == 1){
          $is_send = 1;
          $resp = array ('status' => "true", 'message' => "success", 'type' => $is_send , 'response' => $data);
        }
        else{
          $is_send = 0;
          $resp = array ('status' => "true", 'message' => "failure", 'type' => $is_send , 'response' => $data);
        }
      
      $this->response($resp);
    }

?>