<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Repository\UserRepository;
use App\User;
use App\Http\Controllers\Utility\DataService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; 
use DB;
use Virgil\Crypto\VirgilCrypto;
use Virgil\Sdk\Web\Authorization\JwtGenerator;


Class ApiService{

	public  function checkemail_phone($arg)
	{

		$data = new DataService();
		
		if(isset($arg['unique_id'])){
			$requestStatus = 1;
		}else{
			$requestStatus = 0;
		}

		if($requestStatus == 1){

			$UserRepostitory   = new UserRepository();
    	 	$User   =  $UserRepostitory->check_user($arg);
			if(@$User['user_status'] != 0){

				if($User['unique_id'] == @$arg['unique_id']){
					$data->error_code = 499; 

				}else{

					$data->error_code = 499;
				}

				if(@$arg['password']){

                    if($User['password'] == @$arg['password']){
                        
                        $data->error_code =	203;
                    }
				}
			
			}else{

    	 	//print_r($User); exit;
				$unactive_user   =  $UserRepostitory->check_unactive_user($arg);
    	 	     
				if($unactive_user){
					$arg['id'] = $unactive_user['id']; 

					$UserRepostitory   = new UserRepository();
    		 		$User   =  $UserRepostitory->register($arg);
    				
				}else{
	
					$UserRepostitory   = new UserRepository();
    	 			$User   =  $UserRepostitory->register($arg);
    	 		}
    	 		$data->unique_id =  $User->unique_id; 
    	 		$data->device_id =  $User->device_id; 
    	 		$data->device_type =  $User->device_type; 
    	 		$data->fcm_token =  $User->fcm_token; 

    	 		$data->password =  $User->password; 
    	 		//print_r($data); exit;
    	 		
    	 		if($User){
    	 			$data->error_code =	203;
    	 		}
			}
		}else{
			$data->error_code = 403; 

		}
		return $data;
	}


	public function verifyUser($arg){

		$data  = new DataService();
		
		/*	if($arg['email'] != ""){

			$Checkuser = Auth::attempt( ['email' => $arg['email'], 'password' => $arg['password']] );

		}else if($arg['phone'] != ""){

			$Checkuser = Auth::attempt( ['phone' => $arg['phone'], 'password' => $arg['password']] );		
		}*/

		/*if($Checkuser){*/
			
			if( isset($arg['code']) && isset($arg['phone'])){
				
				$UserRepostitory = new UserRepository();
				$getuser = $UserRepostitory->getuser($arg);
				
				if($getuser){
					//print_r($getuser); exit;
					//$user = Auth::user(); 
					//$getuser['token'] = $user->createToken('hopple')->accessToken;
					//$getuser['id'] = $user->id;

					if($getuser['code'] == 205){

						unset($getuser['code']);
						$data->error_code = 205;
						$data->data = $getuser; 

					}else if($getuser['code'] == 422){
							
						$data->error_code = $getuser['code'];
					
					}
				}else{
					$data->error_code = $getuser['code'];
				}
				
	    	 	
			}else{

				$data->error_code =	403;

			}

		/*}else{

			$data->error_code =	422; 


		}*/
		
		return $data;
		
		

	}

	public function login($arg){
		//print_r($arg); exit;
		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		if(Auth::attempt(['unique_id'=> @$arg['unique_id'],'password'=> $arg['password']] )){ 
			$user = Auth::user(); 
			DB::table('oauth_access_tokens')->where('user_id',$user['id'])->delete();
			if(Auth::user()->user_status == 0){
				$data->error_code = 209; 
			}else{

				//echo rand(); exit;
				
				// App Key (you got this Key at Virgil Dashboard)
				$privateKeyStr = "MC4CAQAwBQYDK2VwBCIEIJ5mi96oZJ8v13CIgLiz1QmwTYSqrKrWOv1V7CxY5uKw";
				$appKeyData = base64_decode($privateKeyStr);

				// VirgilCrypto imports a private key into a necessary format
				$crypto = new VirgilCrypto();
				$privateKey = $crypto->importPrivateKey($appKeyData);

				// use your App Credentials you got at Virgil Dashboard:
				$appId = "1794e250dc3b490faa06d0b2b70b2ccd"; // App ID
				$appKeyId = "d74992324ba97221cf633675e66f8344";              // App Key ID
				$ttl = 86400; // 1 hour (JWT's lifetime)

				// setup JWT generator with necessary parameters:
				$jwtGenerator = new JwtGenerator($privateKey->getPrivateKey(), $appKeyId, $crypto, $appId, $ttl);

				// generate JWT for a user
				// remember that you must provide each user with his unique JWT
				// each JWT contains unique user's identity (in this case - Alice)
				// identity can be any value: name, email, some id etc.
				$identity = strtolower($arg['unique_id']);
				$token = $jwtGenerator->generateToken($identity);
				
				// as result you get users JWT, it looks like this: "eyJraWQiOiI3MGI0NDdlMzIxZjNhMGZkIiwidHlwIjoiSldUIiwiYWxnIjoiVkVEUzUxMiIsImN0eSI6InZpcmdpbC1qd3Q7dj0xIn0.eyJleHAiOjE1MTg2OTg5MTcsImlzcyI6InZpcmdpbC1iZTAwZTEwZTRlMWY0YmY1OGY5YjRkYzg1ZDc5Yzc3YSIsInN1YiI6ImlkZW50aXR5LUFsaWNlIiwiaWF0IjoxNTE4NjEyNTE3fQ.MFEwDQYJYIZIAWUDBAIDBQAEQP4Yo3yjmt8WWJ5mqs3Yrqc_VzG6nBtrW2KIjP-kxiIJL_7Wv0pqty7PDbDoGhkX8CJa6UOdyn3rBWRvMK7p7Ak"
				// you can provide users with JWT at registration or authorization steps
				// Send a JWT to client-side
				 $token->__toString();

				 $arg['chat_token'] = $token->__toString();

				
				//$user = Auth::user()->token();
        		//$user->revoke();
				 $arg['token'] = $user->createToken('hopple')->accessToken;
				 
				if(isset($arg['unique_id']) && isset($arg['password'])){
					$requestStatus = 1;		
				}else{

					$requestStatus = 0;
				}
		        if( !isset($arg['password']) ) { $requestStatus = 0; }
		        if( !isset($arg['device_id']) ) { $requestStatus = 0; }
		        if( !isset($arg['device_type']) ) { $requestStatus = 0; }
		       
		        $validpassword = 0;

		        
		 		if( $requestStatus==1 ) {
					$forgot_pass = 0;

					$user = $UserRepostitory->login($arg);
					//print_r($user['id']); exit;
					//$code  = $UserRepostitory->logout($user['id']);
					/*echo '<pre>';
					print_r($user); exit;*/
					if(!empty($user)){

						$pass = password_verify($arg['password'], $user['password']);

						$refreshToken = DB::table('oauth_access_tokens')
										->where('user_id',$user['id'])
										->whereNull('name')
										->update(['revoked' => 1]);
										

						if($pass == 1){

							$validpassword =0;	
						}else{

							$validpassword =1;
						}

						if($validpassword == 0){
							
							$clear_token = $UserRepostitory->clear_user_token($user['device_id']);

							if($user['user_status'] == 0){
		                    	$is_reset = 1;   
							}

							//Check user reactivated by admin 
							if($user['user_status']  ==  2){
								
								$data->error_code = 498;
								$data->email = $user['email'];
			                    $data->phone = $user['phone'];
							
							}else if($user['isdelete'] ==  1){
								
								$data->error_code = 643;
								$data->email = $user['email'];
			                    $data->phone = $user['phone'];
							
							}else{ //Login users

								$arg['id'] = $user['id'];
								//echo '<pre>'; print_r($arg); exit;
								$userDetail  = $UserRepostitory->get_user_detail($arg);
								$data->error_code = 200;
								$data->data = $userDetail; 
							}

						}else{

							$data->error_code = 411;
						}


					}else{

						$data->error_code = 401;

					}
				}else{


		 			$data->error_code = 402; 
		 		}
		 	}	
	 	}else{

	 		$data->error_code = 401;//UnAuthorizated users
	 	}	

 		return $data;
	}


	public  function socialLogin($arg)
	{

		$data = new DataService();
		
		if(isset($arg['facebook_id'])){
			$requestStatus = 1;
		}elseif(isset($arg['google_id'])){
			$requestStatus = 1;
		}elseif(isset($arg['apple_id'])){
			$requestStatus = 1;
		}else{
			$requestStatus = 0;
		}

		if($requestStatus == 1){
			//echo rand(); exit;
			$UserRepostitory   = new UserRepository();
    	 	$User   =  $UserRepostitory->check_user($arg);
			if(@$User['id'] != ""){
				DB::table('oauth_access_tokens')->where('user_id',$User['id'])->delete();
				$arg['token'] = $User->createToken('hopple')->accessToken;
				if(Auth::loginUsingId($User['id'])){
					$user = Auth::user();
					$refreshToken = DB::table('oauth_access_tokens')
					->where('user_id',$User['id'])
					->whereNull('name')
					->update(['revoked' => 1]);
					$clear_token = $UserRepostitory->clear_user_token($User['device_id']);

	 				$arg['id'] = $user['id'];
	 				//print_r($arg); exit;
					$userDetail  = $UserRepostitory->get_user_detail($arg);
					//$data->error_code = 200;
					$data->data = $userDetail; 
    	 			$data->error_code =	200;

				}
			}else{
				//echo rand(); exit;
				
				$UserRepostitory   = new UserRepository();
	 			$User   =  $UserRepostitory->social_register($arg);
	 			$User   =  $UserRepostitory->check_user($arg);
	 			$arg['token'] = $User->createToken('hopple')->accessToken;
    	 		$arg['id'] = $User;
				if($User){
	    	 		if(Auth::loginUsingId($User['id'])){
						$user = Auth::user();
						$refreshToken = DB::table('oauth_access_tokens')
						->where('user_id',$User['id'])
						->whereNull('name')
						->update(['revoked' => 1]);
						$clear_token = $UserRepostitory->clear_user_token($User['device_id']);

		 				$arg['id'] = $user['id'];
		 				//print_r($arg); exit;
						$userDetail  = $UserRepostitory->get_user_detail($arg);
						//$data->error_code = 200;
						$data->data = $userDetail; 
	    	 			$data->error_code =	200;

					}
    	 		}
			}
		}else{
			$data->error_code = 403; 

		}
		return $data;
	}


	public function createPost($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ // FOR GET METHOD

			$gallery = $UserRepostitory->view_post($arg);
		
			$data->error_code = 218;
			$data->data = $gallery; 
		
		}else if($method == 2){ // FOR POST METHOD

			if(Auth::user()->id){
				
				$arg['userid'] =  Auth::user()->id;
				$gallery = $UserRepostitory->create_post($arg);
		
				if($gallery['code'] == 200){

					unset($gallery['code']);
					$data->error_code = 218;
					$data->data = $gallery;


				}else{
					unset($gallery['code']);
					$data->error_code = 633;
				}  
		
			}else{

				$data->error_code =  414;  //UnAuthorize user
			}

		}else{ // FOR DELETE METHOD


			$gallery = $UserRepostitory->delete_post($arg);
			$data->error_code = 214; 
		}
		
		return $data;
	}

	public function forgotPassword($arg){

		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		if(isset($arg['email']) || isset($arg['phone'])){
			$getuser = $UserRepostitory->login($arg);	
			if(isset($getuser->id) && $getuser->id > 0 ){
			
				$update_password = $UserRepostitory->forgot_password($arg,$getuser);	

				if($update_password){

					$data->error_code = 601;
				
				}else{

					$data->error_code = 470;
				}

			}else{

				$data->error_code = 430;

			}

		}
		//print_r($data); die;
		return $data;
	}


	public function category_list(){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$category_list  = $UserRepostitory->category_list(1);

		if($category_list){
			$data->error_code = 641;
			$data->data = $category_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}

	public function gender_list(){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$gender_list  = $UserRepostitory->gender_list(1);

		if($gender_list){
			$data->error_code = 641;
			$data->data = $gender_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}

	public function race_list(){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$race_list  = $UserRepostitory->race_list(1);

		if($race_list){
			$data->error_code = 641;
			$data->data = $race_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}

	public function religion_list(){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$religion_list  = $UserRepostitory->religion_list(1);

		if($religion_list){
			$data->error_code = 641;
			$data->data = $religion_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}

	public function report_list(){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$report_list  = $UserRepostitory->report_list(1);

		if($report_list){
			$data->error_code = 641;
			$data->data = $report_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}
	

	public function partner_type(){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$partner_type  = $UserRepostitory->partner_type(1);

		if($partner_type){
			$data->error_code = 641;
			$data->data = $partner_type;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}

	public function region(){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$region  = $UserRepostitory->region(1);

		if($region){
			$data->error_code = 641;
			$data->data = $region;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}

	public function profile($method,$arg){

		$data = 	new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ //FOR GET METHOD
			$getuser  =   $UserRepostitory->getuserById($arg);
			if($getuser){
				$data->error_code = 207;
				$data->data = $getuser;
			
			}else{

				$data->error_code = 631;
			}
		}else if($method == 0){  // FOR POST METHOD
		
	
			$arg['Id'] = Auth::user()->id;

			$update = $UserRepostitory->getupdateprofile($arg);
			

				//print_r($update); exit;	
			if($update['code'] == 200){

				unset($update['code']);
				$data->error_code = 217;
				$data->data = $update; 

			}else if($update['code'] == 410){
					
				$data->error_code = $update['code'];
			
			}else{

				$data->error_code = 632;
			}  
		}

	
		return $data;
	}

	public function user_detail($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ //FOR GET METHOD

			$getuser  =   $UserRepostitory->getotheruserById($arg);
			if($getuser){
				$data->error_code = 207;
				$data->data = $getuser;
			
			}else{

				$data->error_code = 631;
			}
		}
	
		return $data;
	}

	

	public function subcategory_list($arg){
		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$subcategory_list  = $UserRepostitory->subcategory_list($arg['c_id']);

		if($subcategory_list){
			$data->error_code = 641;
			$data->data = $subcategory_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}

	
	public function pref_profile($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ //FOR GET METHOD

			$getuser  =   $UserRepostitory->getuserById($arg);
			if($getuser){
				$data->error_code = 207;
				$data->data = $getuser;
			
			}else{

				$data->error_code = 631;
			}
		}else if($method == 0){  // FOR POST METHOD
		
	
			$arg['Id'] = Auth::user()->id;

			$update = $UserRepostitory->pref_profile($arg);
			

				//print_r($update); exit;	
			if($update['code'] == 200){

				unset($update['code']);
				$data->error_code = 217;
				$data->data = $update; 

			}else if($update['code'] == 410){
					
				$data->error_code = $update['code'];
			
			}else{

				$data->error_code = 632;
			}  
		}

	
		return $data;
	}

	public function visibilty_profile($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 0){  // FOR POST METHOD
		
	
			$arg['Id'] = Auth::user()->id;

			$update = $UserRepostitory->visibilty_profile($arg);
			

				//print_r($update); exit;	
			if($update['code'] == 200){

				unset($update['code']);
				$data->error_code = 217;
				$data->data = $update; 

			}else if($update['code'] == 410){
					
				$data->error_code = $update['code'];
			
			}else{

				$data->error_code = 632;
			}  
		}

	
		return $data;
	}




	public function mark_default($arg){
		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		if(isset($arg['p_id']) || isset($arg['is_default'])){
			$getphoto = $UserRepostitory->mark_default($arg);	
			if(isset($getphoto->p_id) && $getphoto->p_id > 0 ){
			
				$update_default = $UserRepostitory->mark_default($arg);	
				if($update_default->code == 200){
					$data->error_code = 646;
					$data->data = $update_default; 
				}else{

					$data->error_code = 471;
				}

			}else{

				$data->error_code = 431;

			}

		}
		//print_r($data); die;
		return $data;
	}


	public function  getdoctor(){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		$getdoctor  =   $UserRepostitory->getdoctor();

		if($getdoctor){
			$data->error_code = 300;
			$data->data = $getdoctor->toArray();
		
		}else{

			$data->error_code = 630;

		}

		return  $data;
	}


	

	public function gallery($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ // FOR GET METHOD

			$gallery = $UserRepostitory->view_gallery($arg);
		
			$data->error_code = 218;
			$data->data = $gallery; 
		
		}else if($method == 2){ // FOR POST METHOD

			if(Auth::user()->id){
				
				$arg['userid'] =  Auth::user()->id;
				$gallery = $UserRepostitory->gallery($arg);
		
				if($gallery['code'] == 200){

					unset($gallery['code']);
					$data->error_code = 218;
					$data->data = $gallery;


				}else{
					unset($gallery['code']);
					$data->error_code = 633;
				}  
		
			}else{

				$data->error_code =  414;  //UnAuthorize user
			}

		}else{ // FOR DELETE METHOD


			$gallery = $UserRepostitory->delete_gallery($arg);
			$data->error_code = 214; 
		}
		
		return $data;
	}
	
	


	public function resetPassword($arg){


		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		
		if(isset($arg['password']) && isset($arg['id'])){

			$getuser =  $UserRepostitory->update_password($arg);

			if($getuser){
				if($getuser->is_forgot == 0){
					$data->error_code = 638;
				}else{
					$data->error_code = 645;
				}

			}else{

				$data->error_code = 639;
			}


		}else{

			$data->error_code = 403;
		}	
		

		return $data;
	}

	

	public function matchFind($method,$arg,$userId){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ //FOR GET METHOD

			$getuser  =   $UserRepostitory->getMatch($arg,$userId);
			//print_r($getuser['temp_error']); exit;
			$data->temp_error = $getuser['temp_error'];
			if($getuser){
				$data->error_code = 207;
				if($data->temp_error == 1){
					$data->error_code = 304;
				}
				$data->data = $getuser;
			
			}else{

				$data->error_code = 631;
			}
		}else if($method == 0){  // FOR POST METHOD
		
	
			$arg['Id'] = Auth::user()->id;

			$update = $UserRepostitory->getupdateprofile($arg);
			

				//print_r($update); exit;	
			if($update['code'] == 200){

				unset($update['code']);
				$data->error_code = 217;
				$data->data = $update; 

			}else if($update['code'] == 410){
					
				$data->error_code = $update['code'];
			
			}else{

				$data->error_code = 632;
			}  
		}else{ // FOR DELETE METHOD

			$matchdelete = $UserRepostitory->delete_match($arg);
			if($matchdelete == 1){
				$data->error_code = 215;
			}else{
				$data->error_code = 432;
			} 
		}

	
		return $data;
	}



	public function pendingmatchFind($method,$arg,$userId){

		$data = new DataService();
		$UserRepostitory = new UserRepository();


		if($method == 1){ //FOR GET METHOD

			$getuser  =   $UserRepostitory->getPendingMatch($arg,$userId);
			if($getuser){
				$data->error_code = 207;
				$data->data = $getuser;
			
			}else{

				$data->error_code = 631;
			}
		}
		return $data;
	}


	public function check_username($method,$arg,$userId){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ //FOR GET METHOD
			//echo $userId ; exit;
			$getuser  =   $UserRepostitory->check_username($arg,$userId);
			if($getuser){
				$data->error_code = 207;
				$data->data = $getuser;
			
			}else{

				$data->error_code = 631;
			}
		}
		return $data;
	}

	public function check_unique_id($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ //FOR GET METHOD
			//echo $userId ; exit;
			$getuser  =   $UserRepostitory->check_unique_id($arg);
			if($getuser){
				$data->error_code = 207;
				$data->data = $getuser;
			
			}else{

				$data->error_code = 631;
			}
		}
		return $data;
	}


	public function update_device($method,$arg,$userId){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ //FOR POST METHOD

			$getuser  =   $UserRepostitory->update_device($arg,$userId);
			if($getuser){
				$data->error_code = 207;
				$data->data = $getuser;
			
			}else{

				$data->error_code = 631;
			}
		}
		return $data;
	}


	public function user_search($unique_id){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		$arg['id'] 	   = Auth::user()->id;
		$user_list  = $UserRepostitory->user_search($unique_id,$arg);
		//print_r($user_list); exit;
		if(@$user_list['userid'] !=''){
			$data->error_code = 641;
			$data->data = $user_list;
		
		}else{

			$data->error_code = 640;

		}
		
		
		return  $data;
	}

	public function chat_user_sid_update($sid,$userId){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		
		$getuser  =   $UserRepostitory->chat_user_sid_update($sid,$userId);
		if($getuser){
			$data->error_code = 207;
			$data->data = $getuser;
		
		}else{

			$data->error_code = 631;
		}
		return $data;
	}





	public function chip($arg){

		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		
		if( isset($arg['chip_name']) && isset($arg['unique_id']) ){
			$arg['id'] = Auth::user()->id;
			
			$checkunique = $UserRepostitory->checkchip($arg);
			if($checkunique == 0){
				$getchip =  $UserRepostitory->chip($arg);
				//print_r($getchip);
				if($getchip){
					$data->error_code = 210;
					$data->data =$getchip;

				}else{
					$data->error_code = 640;
				}
			}else{// this user already register this chip 
				$data->error_code = 644;
			}


		}else{

			$data->error_code = 403;
		}	
		

		return $data;
	}

	public function create_room($arg){
		$userid =  Auth::user()->id;
		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		if(isset($arg['receiver_id']) ){
			$checkunique = $UserRepostitory->create_room($arg,$userid);
			if($checkunique == 1){
				$data->error_code = 219;


			}else if($checkunique == 2){
				$data->error_code = 302;


			}else{
				$data->error_code = 433;
			} 

		}else{

			$data->error_code = 403;
		}	
		

		return $data;
	}
	

	public function favourite($arg){
		$userid =  Auth::user()->id;
		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		if(isset($arg['post_id']) ){
			$checkunique = $UserRepostitory->favourite($arg,$userid);
			if($checkunique == 1){
				$data->error_code = 220;
			}else{
				$data->error_code = 432;
			} 

		}else{

			$data->error_code = 403;
		}	
		

		return $data;
	}


	public function userNotify($request){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		$userid =  Auth::user()->id;
		$partner_list  = $UserRepostitory->userNotify($request,$userid);

		if($partner_list){
			$data->error_code = 649;
			$data->data = $partner_list;
		
		}else{

			$data->error_code = 649;

		}
		
		
		return  $data;
	}

	public function home_list($request){
		$data = new DataService();
		$UserRepostitory = new UserRepository();
		$arg['id'] 	   = Auth::user()->id;
		$post_list  = $UserRepostitory->home_list($request,$arg);

		if($post_list){
			$data->error_code = 647;
			$data->data = $post_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}

	public function block_list($request){
		$data = new DataService();
		$UserRepostitory = new UserRepository();
		$arg['id'] 	   = Auth::user()->id;
		$post_list  = $UserRepostitory->block_list($request,$arg);

		if($post_list){
			$data->error_code = 647;
			$data->data = $post_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}

	public function partner_detail($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ //FOR GET METHOD

			$getuser  =   $UserRepostitory->partner_detail($arg);
			if($getuser){
				$data->error_code = 213;
				$data->data = $getuser;
			
			}else{

				$data->error_code = 631;
			}
		}
	
		return $data;
	}


	



	public function question($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ //FOR GET METHOD
			$getuser  =   $UserRepostitory->getquestion($arg);
			if($getuser){
				$data->error_code = 300;
				$data->data = $getuser;
			
			}else{

				$data->error_code = 631;
			}
		}
	
		return $data;
	}


	public function answer($arg){
		$userid =  Auth::user()->id;
		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		if( isset($arg['answer']) && isset($arg['q_id']) ){
			$checkunique = $UserRepostitory->answer($arg,$userid);
			if($checkunique == 1){
				$data->error_code = 301;
			}else{
				$data->error_code = 433;
			} 

		}else{

			$data->error_code = 403;
		}	
		

		return $data;
	}

		public function answer_delete($arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		$gallery = $UserRepostitory->answer_delete($arg);
		$data->error_code = 302; 
		
		
		return $data;
	}


	public function notification_match_detail($method,$arg,$user_id){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ //FOR GET METHOD
			$getuser  =   $UserRepostitory->notification_match_detail($arg,$user_id);
			if($getuser){
				$data->error_code = 303;
				$data->data = $getuser;
			
			}else{

				$data->error_code = 631;
			}
		}
	
		return $data;
	}

	public function logout(){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		$arg 	   = Auth::user()->id;
		$code  = $UserRepostitory->logout($arg);

		if($code == 642){
			$data->error_code = 642;
		
		}else{

			$data->error_code = 461;

		}

		return  $data;
	}

	public function deleteAccount(){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		$arg['userid'] =  Auth::user()->id;
		$gallery = $UserRepostitory->deleteAccount($arg);
		$data->error_code = 447; 


		return $data;
	}

	public function subscriptions(){
		//$userid =  Auth::user()->id;
		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		
		$scription = $UserRepostitory->subscriptions();
		if($scription->code == 200){
			$scription->error_code = 220;
		}else{
			$scription->error_code = 650;
		} 
		
		return $scription;
	}

	public function subscriptionsList(){
		//$userid =  Auth::user()->id;
		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		
		$scription = $UserRepostitory->subscriptionsList();
		if($scription->code == 200){
			$scription->error_code = 220;
		}else{
			$scription->error_code = 433;
		} 
		
		return $scription;
	}

	public function pendingSubscriptionPlan($arg,$userId){
		$data = new DataService();
		$UserRepostitory = new UserRepository();
		$spendingSubscription_list  = $UserRepostitory->pendingSubscriptionPlan($arg,$userId);
		//print_r($spendingSubscription_list); exit;
		if($spendingSubscription_list){
			$data->error_code = $spendingSubscription_list;
			//$data->data = $spendingSubscription_list;
		
		}else{

			$data->error_code = $spendingSubscription_list;

		}
		
		
		return  $data;
	}

	public function androidSubscreption($arg,$userId){
		$data = new DataService();
		$UserRepostitory = new UserRepository();
		$spendingSubscription_list  = $UserRepostitory->androidSubscreption($arg,$userId);
		//print_r($spendingSubscription_list); exit;
		if($spendingSubscription_list){	
			$data->error_code = $spendingSubscription_list;
			//$data->data = $spendingSubscription_list;
		
		}else{

			$data->error_code = $spendingSubscription_list;

		}
		
		
		return  $data;
	}

	public function cronJobForSubscreption(){
		$data = new DataService();
		$UserRepostitory = new UserRepository();
		$spendingSubscription_list  = $UserRepostitory->cronJobForSubscreption();

		if($spendingSubscription_list){
			$data->error_code = 221;
			$data->data = $spendingSubscription_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}
}



