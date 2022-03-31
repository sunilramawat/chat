<?php

namespace App\Http\Controllers\Repository;

use App\User;
use App\Models\Photo;
use App\Models\Partner;
use App\Models\Report;
use App\Models\PendingMatches;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Models\Gender;
use App\Models\ReportList;
use App\Models\Religion;
use App\Models\Race;
use App\Models\PartnerType;
use App\Models\Region;
use App\Models\Subscription;
use App\Http\Controllers\Utility\CustomVerfication;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Utility\SendEmails;
use Carbon\Carbon;
use Auth;
Class UserRepository extends User{

	public function check_user($data){
		if(isset($data['email'])){
			$user_list = User::Where('email',@$data['email'])
				->where('user_status','!=',0)->first();
		}else{
			$user_list = User::Where('phone',@$data['phone'])
				->where('user_status','!=',0)->first();
		}

		return $user_list;				
	}

	public function check_unactive_user($data){
		if(isset($data['email'])){
			$user_list = User::where('email',@$data['email'])->first();
		}else{
			$user_list = User::where('phone',@$data['phone'])->first();
		}
		//echo '<pre>'; print_r($user_list); die;
		return $user_list;				
	}

	public function register($data){

		$CustomVerfication = new CustomVerfication();
		$SendEmail = new SendEmails();
		$code = 1234;//$CustomVerfication->generateRandomNumber(4);
		$rescod  = "";
		
		if(!isset($data['id'])){
			$create_user = new User();
		}else{
			$create_user = User::find($data['id']);
		}
		//$create_user->email 	= @$data['email'] ? $data['email']: '';
		//$create_user->password 	= hash::make(@$data['password']) ? hash::make(@$data['password']): '';

		$create_user->first_name = @$data['first_name'];
		$create_user->last_name = @$data['last_name'];
		$create_user->phone = @$data['phone'];
		$create_user->added_date = date ( 'Y-m-d H:i:s' );
		$create_user->user_type = 1 ;
		$create_user->user_status = '0';
		$create_user->is_approved = '0';
		$create_user->user_status = '0';
		$create_user->activation_code = $code;
		$create_user->is_email_verified = '0';
		$create_user->is_phone_verified = '0';
        $create_user->last_login= date ( 'Y-m-d H:i:s' );
        $create_user->token_id = mt_rand(); 
		$create_user->created_at = date ( 'Y-m-d H:i:s' );
		$create_user->updated_at = date ( 'Y-m-d H:i:s' );
		
		$create_user->save();
		$userid = $create_user->id;
		$message = "Your Hopple verification Code is ". $code;
		
		if(isset($data['phone'])){
			$phone = $data['phone'];
            $verify_type = 1;
            $create_user->activation_code = $code;
			//$verify = $CustomVerfication->phoneVerification($message,$data['phone']);
            //$verify = $CustomVerfication->phoneVerification($message,"+917340337597");

		}else{
            $verify_type = 2;
        }

        $data['forgot_type'] = 1;

        if(@$data['email'] != ''){

            $email = $create_user->email;
            $name = $create_user->name;
            $code =  $code;

            //$url =  url("activation/".$code);
			//$newpassword = $url;

            $SendEmail->sendUserRegisterEmail($email,$name,$code,$data['forgot_type'],$userid);
        	
        }

		return $create_user;
	}

	public function getuser($data){
		if(!empty($data['code'])){
			
			if(isset($data['email'])){
				$query = User::where('activation_code','=',$data['code'])
					->where('email',@$data['email'])
					->first();
			}else{
				$find = 0;
				$query = User::where('activation_code','=',$data['code'])
					->where('phone',@$data['phone'])
					->first();
				if(!empty($query)){
					$find = 1;	
				}else{
					$query = User::where('activation_code','=',$data['code'])
					->where('phone_tmp',@$data['phone'])
					->first();
					$find = 2; // to blank phone_tmp and  update in phone
				}

			}	
			if(!empty($query)){
				$user = User::find($query->id);
				//$user->password = Hash::make($data['password']);
		        //$user->activation_code = '';
		        //$user->user_status = 1;
				if(isset($data['email'])){
		            $user->is_email_verified = 1;
		        }else{
		           // $user->is_phone_verified = 1;
		        }
		        if($find == 2){
		        	$user->phone = $data['phone'];
		        	$user->phone_tmp = '';
		        }

	        	$user->save();


	        	$userData['code'] = 205;
	        	//$userData['email'] = $user->email; 
	        	//$userData['password'] = $user->password; 
	        	$userData['id'] = $user->id; 
	        	$userData['phone'] = $user->phone; 
	        	//$userData['access_token'] = $data['token']; 
		        
			}else{

				$userData['code'] = 422;	

	        }

		}else{

			$userData['code'] = 422;	

		}

		return $userData;
	}

	public function login($data){
		if(!empty($data['phone']))
		{
			$query = User::where('phone',$data['phone'])->first();
		}elseif (!empty($data['email'])) {
		
			$query = User::where('email',$data['email'])->first();			
		
		}else{
		
			$query = User::where('phone',$data['phone'])->where('email',$data['email'])->first();
		
		}
		

		return $query;
	}

	public function  clear_user_token($data){

		$clear_token = User::where('device_id',$data)->first();
		$clear_token->device_id = "";
		$clear_token->save();  
	}

	public function get_user_detail($data){

		$token_id =  mt_rand();
		$query = User::find($data['id']);
		$query->token_id    = $token_id;
        $query->last_login  = date ( 'Y-m-d H:i:s' );
    	$query->device_id   = $data['device_id'];
        $query->device_type = $data['device_type'];
        $query->save();

    	/* $userdata['id']            = $query['id'];
        $userdata['user_type']     = $query['user_type'];
        $userdata['email']         = @$query['email']	?	$query['email']:'';
        $userdata['phone']         = @$query['phone']	?	$query['phone']:'';
        //$userdata['access_token']  = sha1(md5('eureka'.$query['id'].'!@#$%'.$token_id).")(*&^%$");
        $userdata['access_token']  = $data['token'];
       
        $userdata['last_login']    = date ( 'Y-m-d H:i:s' );
        $userdata['photo']         = @$query['photo'] ? URL('/public/images/'.@$query['photo']) :URL('/public/images/profile.png');
        $userdata['name']    	   = $query['name']	?	$query['name']:'';
        //$userdata['is_reset']      = $is_reset;*/


        	$userdata['id'] = $query['id'];
	       	$userdata['last_login']    	= date ( 'Y-m-d H:i:s' );
        	//$userdata['username'] 		= $query['username'] 		? 	$query['username'] : '';;
	        //$userdata['user_type'] 		= $query['user_type']		? 	$query['user_type'] : '';
	        //$userdata['country_code'] 	= $query['country_code'] 	? 	$query['country_code'] : '';
	        //$userdata['email'] 			= $query['email'] 			? 	$query['email'] : '';
	        //$userdata['phone'] 			= $query['phone'] 			? 	$query['phone'] : '';
	        $userdata['device_id'] 		= $query['device_id'] 		? 	$query['device_id'] :'';
	        $userdata['device_type'] 	= $query['device_type'] 	? 	$query['device_type'] : '';
	        $userdata['first_name'] 	= $query['first_name'] 			? 	$query['first_name'] : '';
	        $userdata['last_name'] 	    = $query['last_name'] 			? 	$query['last_name'] : '';
	        $userdata['device_token'] 	= $query['device_token'] 	? 	$query['device_token'] : '';
	        //$userdata['address'] 		= $query['address'] 		? 	$query['address'] : '';
	        $userdata['lat'] 			= $query['lat'] 			? 	$query['lat'] : '';
	        $userdata['lng'] 			= $query['lng'] 			? 	$query['lng'] : '';
	        //$userdata['zip'] 			= $query['zip'] 			? 	$query['zip'] : '';
	        //$userdata['gender'] 		= $query['gender'] 			? 	$query['gender'] : '';
	        $userdata['access_token']  	= $data['token'];

	        $userdata['user_status']= $query['user_status'] 	? 	$query['user_status'] : '';
	        $userdata['is_setpreferences']= $query['is_setpreferences'] 	? 	$query['is_setpreferences'] : 0;
	        $userdata['is_active_profile']= $query['is_active_profile'] 	? 	$query['is_active_profile'] : 0;
		       	//$userdata['photo'] = $query['photo'] ? URL('/public/images/'.$query['photo']):'';
	        	
	    	
	    
	        //$userdata['photo'] = $query['photo'] ? URL('/public/images/'.$query['photo']) : '';
	       
	        


		return $userdata;
	}

	public function forgot_password($data,$user){

		$data['forgot_type'] = 1;
		$SendEmail = new SendEmails();
		$getuser = User::find($user->id);
		$PhoneVerification = new CustomVerfication();
		$rescod = "";
		if($data['forgot_type'] == 1){

			if(@$data['phone'] != ''){
		        $pass = 1234;  //mt_rand (1000, 9999) ;
                $getuser->forgot_password_code = $pass;
                $getuser->activation_code  = $pass;

            }else{

                $pass = mt_rand (1000, 9999) ;
                $getuser->forgot_password_code = $pass;
            }

            $getuser->forgot_password_date = date ( 'Y-m-d H:i:s' );
            unset($getuser->password);

            //print_r($getuser);die;
            $getuser->save();


            if(@$data['email'] != ''){
                $email = $getuser->email;
                $name = $getuser->name;
                $newpassword =  $pass;
                $SendEmail->sendUserEmailforgot($email,$name,$newpassword,$data['forgot_type']);
            	$rescod = 601;
            	
            }

            $lastId = $getuser->id;
            $country_code = '';
			$code =  $pass ;

			$message = "Your Pump Tracker verification code is ". $code;

			if(@$data ['phone'] != ''){
                //$verify = $PhoneVerification->phoneVerification($message,$data['phone']);
                $rescod = 601;
            }
		}

		return $rescod;
	}

	public function getdoctor(){

		$getdoctor 	=	User::select('id','name')->where('user_type',1)
						->where('user_status',1)->where('is_approved',1)->get();
		return $getdoctor; 
	}

	public function getuserById($data){
		$user 	=	User::find($data);
		$photo = Photo::where('p_u_id', $user->id)->where('is_default', 1)->first();
		$userData['id'] = $user->id;
		$userData['p_photo'] = @$photo->p_photo? URL('/public/images/'.$photo->p_photo):'';
        $userData['email'] = $user->email ? $user->email : '';
        $userData['device_id'] = $user->device_id ? $user->device_id : '';
        $userData['device_type'] = $user->device_type ? $user->device_type : '';
        $userData['first_name'] = $user->first_name ? $user->first_name : '';
        $userData['last_name'] = $user->last_name ? $user->last_name : '';
        $userData['lat'] = @$user->lat ? $user->lat :'';
        $userData['lng'] = @$user->lng ? $user->lng :'';
        $userData['is_approved'] = $user->is_approved ? $user->is_approved : '';
       	$userData['activation_code'] = $user->activation_code ? $user->activation_code : '';
       	$userData['reset_key'] = $user->reset_key ? $user->reset_key : '';
		$userData['email'] 	= 	 $user->email ? $user->email: '';
		$userData['phone'] 	= 	$user->phone ? $user->phone : '';
		$userData['d_o_b'] 	= 	$user->d_o_b ? $user->d_o_b : '';
		$userData['age'] 	= 	$user->age ? intval($user->age) : 0;
		$userData['gender']	= 	$user->gender ? $user->gender : 0;
		$userData['race'] 	= 	$user->race ? $user->race : 0;
		$userData['religion']	= 	$user->religion ? $user->religion : '';
		$userData['height'] 	= 	$user->height ? $user->height : '';
		$userData['occupation'] = 	$user->occupation ? $user->occupation : '';
		$userData['descr'] = 	$user->description ? $user->description : '';
		$userData['willing_to_dutch'] 	= 	$user->willing_to_dutch ? $user->willing_to_dutch : 0;
		$userData['non_smoker'] = 	$user->non_smoker ? $user->non_smoker : 0;
		$userData['is_subscribe'] 	= 	$user->is_subscribe ? $user->is_subscribe : 0;
		$userData['pref_gender']	= 	$user->pref_gender ? $user->pref_gender : 0;
		$userData['pref_agegroup']	= 	$user->pref_agegroup ? $user->pref_agegroup : 0;
		$userData['pref_min']	= 	$user->pref_min ? $user->pref_min : 0;
		$userData['pref_max']	= 	$user->pref_max ? $user->pref_max : 0;
		$userData['pref_race'] 	= 	$user->pref_race ? $user->pref_race : 0;
		$userData['pref_religion']	= 	$user->pref_religion ? $user->pref_religion : 0;
		$userData['pref_willing_to_dutch'] = $user->pref_willing_to_dutch ? $user->pref_willing_to_dutch : 0;
		$userData['pref_non_smoker'] = $user->pref_non_smoker ? $user->pref_non_smoker : 0;
			
	   	$userData['occupation_status']	= 	$user->occupation_status;
		$userData['religion_status']		= 	$user->religion_status? $user->religion_status :0;
		$userData['height_status'] 		= 	$user->height_status;
		$userData['pref_willing_to_dutch_status'] 	= 	 $user->pref_willing_to_dutch_status;
		$userData['pref_non_smoker_status'] 			= 	 $user->pref_non_smoker_status;
		$userData['is_setpreferences'] 			= 	 $user->is_setpreferences ? $user->is_setpreferences : 0;
		$userData['is_active_profile'] 			= 	 $user->is_active_profile ? $user->is_active_profile : 0;
		$userData['cat_id'] 			= 	 $user->cat_id  ? $user->cat_id  : 0;
		$userData['sub_cat_id'] 			= 	 $user->sub_cat_id   ? $user->sub_cat_id   : 0;
		$userData['is_email_verified'] 			= 	 $user->is_email_verified   ? $user->is_email_verified   : 0;
		//print_r($data);die;
		//	print_r($user);die;
       	// $userData['user_type'] = $user->user_type;
        //$userData['phone'] = $user->phone ? $user->phone : '';
        //$userData['address'] = @$user->address ? $user->address : '';
        //$userData['zip'] = @$user->zip ? $user->zip :'';
       //	$userData['forgot_password_code'] = $user->forgot_password_code ? $user->forgot_password_code : '';
        /*if($user->user_type == 2){
        
        }*/
        
        //$userData['photo'] = @$user->photo ? URL('/public/images/'.@$user->photo) : URL('/public/images/profile.png');
        //$userData['license_photo'] = $user->license_photo ? URL('/public/images/'.@$user->license_photo):'';

       	
		return $userData;

	}
	public function getotheruserById($data){
		$user 	=	User::find($data);
		//$photo = Photo::where('p_u_id', $user->id)->where('is_default', 1)->first();
		$getphotolist =  Photo::where('p_u_id',$user->id)->get();	
		
		$PhotoData = array();
		$PhotoArr = array();
		foreach($getphotolist as $list){

			$PhotoData['p_id'] 		=  @$list->p_id ? $list->p_id : '';
			$PhotoData['p_u_id'] 	=  @$list->p_u_id ? $list->p_u_id : '';
			$PhotoData['p_photo'] 	=  @$list->p_photo? URL('/public/images/'.$list->p_photo): '';
			$PhotoData['is_default'] 	=  @$list->is_default ? $list->is_default : '';
			array_push($PhotoArr,$PhotoData);
			
		}

		$userData['photo'] = $PhotoArr;
		$userData['id'] = $user->id;
		//$userData['p_photo'] = @$photo->p_photo? URL('/public/images/'.$photo->p_photo):'';
        $userData['email'] = $user->email ? $user->email : '';
        $userData['device_id'] = $user->device_id ? $user->device_id : '';
        $userData['device_type'] = $user->device_type ? $user->device_type : '';
        $userData['first_name'] = $user->first_name ? $user->first_name : '';
        $userData['last_name'] = $user->last_name ? $user->last_name : '';
        $userData['lat'] = @$user->lat ? $user->lat :'';
        $userData['lng'] = @$user->lng ? $user->lng :'';
        $userData['is_approved'] = $user->is_approved ? $user->is_approved : '';
       	$userData['activation_code'] = $user->activation_code ? $user->activation_code : '';
       	$userData['reset_key'] = $user->reset_key ? $user->reset_key : '';
		$userData['email'] 	= 	 $user->email ? $user->email: '';
		$userData['phone'] 	= 	$user->phone ? $user->phone : '';
		$userData['d_o_b'] 	= 	$user->d_o_b ? $user->d_o_b : '';
		$userData['gender']	= 	$user->gender ? $user->gender : 0;
		$userData['race'] 	= 	$user->race ? $user->race : 0;
		$userData['religion']	= 	$user->religion ? $user->religion : '';
		$userData['height'] 	= 	$user->height ? $user->height : '';
		$userData['occupation'] = 	$user->occupation ? $user->occupation : '';
		$userData['descr'] = 	$user->description ? $user->description : '';
		$userData['willing_to_dutch'] 	= 	$user->willing_to_dutch ? $user->willing_to_dutch : 0;
		$userData['non_smoker'] = 	$user->non_smoker ? $user->non_smoker : 0;
		$userData['is_subscribe'] 	= 	$user->is_subscribe ? $user->is_subscribe : 0;
		$userData['pref_gender']	= 	$user->pref_gender ? $user->pref_gender : 0;
		$userData['pref_agegroup']	= 	$user->pref_agegroup ? $user->pref_agegroup : 0;
		$userData['pref_min']	= 	$user->pref_min ? $user->pref_min : 0;
		$userData['pref_max']	= 	$user->pref_max ? $user->pref_max : 0;
		$userData['pref_race'] 	= 	$user->pref_race ? $user->pref_race : 0;
		$userData['pref_religion']	= 	$user->pref_religion ? $user->pref_religion : 0;
		$userData['pref_willing_to_dutch'] = $user->pref_willing_to_dutch ? $user->pref_willing_to_dutch : 0;
		$userData['pref_non_smoker'] = $user->pref_non_smoker ? $user->pref_non_smoker : 0;
			
	   	$userData['occupation_status']	= 	$user->occupation_status;
		$userData['religion_status']		= 	$user->religion_status? $user->religion_status :0;
		$userData['height_status'] 		= 	$user->height_status;
		$userData['pref_willing_to_dutch_status'] 	= 	 $user->pref_willing_to_dutch_status;
		$userData['pref_non_smoker_status'] 			= 	 $user->pref_non_smoker_status;
		$userData['is_setpreferences'] 			= 	 $user->is_setpreferences ? $user->is_setpreferences : 0;
		$userData['is_active_profile'] 			= 	 $user->is_active_profile ? $user->is_active_profile : 0;
		$userData['cat_id'] 			= 	 $user->cat_id  ? $user->cat_id  : 0;
		$userData['sub_cat_id'] 			= 	 $user->sub_cat_id   ? $user->sub_cat_id   : 0;
		$userData['is_email_verified'] 			= 	 $user->is_email_verified   ? $user->is_email_verified   : 0;
		$userData['age'] 	= 	$user->age ? intval($user->age) : 0;
		//print_r($data);die;
		//	print_r($user);die;
       	// $userData['user_type'] = $user->user_type;
        //$userData['phone'] = $user->phone ? $user->phone : '';
        //$userData['address'] = @$user->address ? $user->address : '';
        //$userData['zip'] = @$user->zip ? $user->zip :'';
       //	$userData['forgot_password_code'] = $user->forgot_password_code ? $user->forgot_password_code : '';
        /*if($user->user_type == 2){
        
        }*/
        
        //$userData['photo'] = @$user->photo ? URL('/public/images/'.@$user->photo) : URL('/public/images/profile.png');
        //$userData['license_photo'] = $user->license_photo ? URL('/public/images/'.@$user->license_photo):'';

       	
		return $userData;
	}

	public function getupdateprofile($data){
		
		$user 	=	User::find($data['Id']);
		$query  = 0;
		/*if($user->is_email_verified != 1){

			$user->email 	= 	@$data['email'] ? $data['email']:$user->email;
		} 	

		if($user->is_phone_verified != 1){

        	$user->phone 	= 	@$data['phone'] ? $data['phone']:$user->phone;
		}*/	
		/*if(isset($data['d_o_b'])){
			$dob = Carbon::createFromFormat('d/m/Y', $data['d_o_b']);
			//print_r($dob); exit;
			$age = 0;
			if(!empty($dob)){
				$age = Carbon::parse($dob)->diff(Carbon::now())->y;
			}
		}*/
		if(isset($data['email'])){

			$query = User::where('email',@$data['email'])->where('id','!=',@$data['Id'])->count();

		}else if(isset($data['phone'])){

			$query = User::where('phone',@$data['phone'])->where('id','!=',@$data['Id'])->count();

		}

		$code = 1234;//$CustomVerfication->generateRandomNumber(4);
		$is_verify  = 0;
		if($query == 0){
			
			$user->first_name 	= 	@$data['first_name'] ? $data['first_name'] : $user->first_name;
			$user->last_name 	= 	@$data['last_name'] ? $data['last_name'] : $user->last_name;
			if($user->is_email_verified == 0){
				$SendEmail = new SendEmails();
				$user->email 	= 	@$data['email'] ? $data['email'] : $user->email;
				$is_verify  = 1;
			}
			if($user->phone == $data['phone']){

			}else{
				$user->phone_tmp 	= 	@$data['phone'] ? $data['phone'] : $user->phone;
				$message = "Your Hopple verification Code is ". $code;
		
				if(isset($data['phone'])){
					$code = 1234;//$CustomVerfication->generateRandomNumber(4);
					$phone = $data['phone'];
		            $verify_type = 1;
		            $user->activation_code = $code;
					//$verify = $CustomVerfication->phoneVerification($message,$data['phone']);
		            //$verify = $CustomVerfication->phoneVerification($message,"+917340337597");

				}else{
		            $verify_type = 2;
		        }
			}
			if($is_verify  == 1){

	            $email = @$data['email'];
	            $name = $user->first_name;
	            $code =  $code;
	            $user->activation_code = $code;
	            //$url =  url("activation/".$code);
				//$newpassword = $url;

	            $SendEmail->sendUserRegisterEmail($email,$name,$code,0,$data['Id']);
			}

			$user->age 	= 	@$data['age'] ? $data['age'] : $user->age;
			$user->gender	= 	@$data['gender'] ? $data['gender'] : $user->gender;
			$user->race 	= 	@$data['race'] ? $data['race'] : $user->race;
			$user->religion	= 	@$data['religion'] ? $data['religion'] : $user->religion;
			$user->height 	= 	@$data['height'] ? $data['height'] : $user->height;
			$user->occupation 			= 	@$data['occupation'] ? $data['occupation'] : $user->occupation;
			$user->description 			= 	@$data['descr'] ? $data['descr'] : $user->description;
			$user->willing_to_dutch 	= 	@$data['willing_to_dutch'] ? $data['willing_to_dutch'] : $user->willing_to_dutch;
			$user->non_smoker 			= 	@$data['non_smoker'] ? $data['non_smoker'] : $user->non_smoker;
			$user->is_subscribe 		= 	@$data['is_subscribe'] ? $data['is_subscribe'] : $user->is_subscribe;
			$user->is_setpreferences 		= 	@$data['is_setpreferences'] ? $data['is_setpreferences'] : $user->is_setpreferences;
			$user->is_active_profile 		= 	@$data['is_active_profile'] ? $data['is_active_profile'] : $user->is_setpreferences;
			$user->age 		= 	@$age ? $age : $user->age;

		//			$user->email 		=	@$data['email'] 	? $data['email'] : $user->email;
	        /*$user->lat 		=	@$data['lat'] ? $data['lat'] : $user->lat;
	        $user->lng 		=	@$data['lng'] ? $data['lng'] : $user->lng;*/
	        //$user->zip 		= 	@$data['zip'] ? $data['zip'] : $user->zip; 
	        
	        /*if (@$data['photo'] != "") {
				$extension_photo = $data['photo']->getClientOriginalExtension();
				if(strtolower($extension_photo) == 'jpg' || strtolower($extension_photo) == 'png' || strtolower($extension_photo) == 'jpeg' ) {
					$FileLogo_photo = time() .'123'.'.' .$data['photo']->getClientOriginalExtension();
					$destinationPath_photo = 'public/images';
					$data['photo']->move($destinationPath_photo, $FileLogo_photo);
					$documentFile_photo = $destinationPath_photo . '/' . $FileLogo_photo;
					$user->photo = $FileLogo_photo;
				}
			}*/		
			//print_r($user); exit;
			$user->save();

			$userData['code'] = 200;
			$userData['id'] = $user->id;
	        //$userData['user_type'] = $user->user_type ? $user->user_type : '';
	        $userData['email'] = $user->email ? $user->email : '';
	        //$userData['phone'] = $user->phone ? $user->phone : '';
	        $userData['device_id'] = $user->device_id ? $user->device_id :'';
	        $userData['device_type'] = $user->device_type ? $user->device_type : '';
	        $userData['first_name'] = $user->first_name ? $user->first_name : '';
	        $userData['last_name'] = $user->last_name ? $user->last_name : '';
	        //$userData['address'] = $user->address ? $user->address : '';
	        $userData['lat'] = $user->lat ? $user->lat : '';
	        $userData['lng'] = $user->lng ? $user->lng : '';
	        //$userData['zip'] = $user->zip ? $user->zip : '';
	        //$userData['gender'] = $user->gender ? $user->gender : '';

	        $userData['is_approved'] = $user->is_approved ? $user->is_approved : '';
		     
		    $userData['d_o_b'] 	= 	$user->d_o_b ? $user->d_o_b : '';
		    $userData['age'] 	= 	$user->age ? intval($user->age) : 0;
			$userData['gender']	= 	$user->gender ? $user->gender : 0;
			$userData['race'] 	= 	$user->race ? $user->race : 0;
			$userData['religion']	= 	$user->religion ? $user->religion : 0;
			$userData['height'] 	= 	$user->height ? $user->height : 0;
			$userData['occupation'] = 	$user->occupation ? $user->occupation : '';
			$userData['descr'] = 	$user->description ? $user->description : '';
			$userData['willing_to_dutch'] = $user->willing_to_dutch ? $user->willing_to_dutch : 0;
			$userData['non_smoker'] = $user->non_smoker ? $user->non_smoker : 0;
			$userData['is_subscribe'] = $user->is_subscribe ? $user->is_subscribe : 0;
			$userData['pref_gender']	= 	$user->pref_gender ? $user->pref_gender : 0;
			$userData['pref_agegroup']	= 	$user->pref_agegroup ? $user->pref_agegroup : 0;
			$userData['pref_min']	= 	$user->pref_min ? $user->pref_min : 0;
			$userData['pref_max']	= 	$user->pref_max ? $user->pref_max : 0;
			$userData['pref_race'] 	= 	$user->pref_race ? $user->pref_race : 0;
			$userData['pref_religion']	= 	$user->pref_religion ? $user->pref_religion : 0;
			$userData['pref_willing_to_dutch'] = $user->pref_willing_to_dutch ? $user->pref_willing_to_dutch : 0;
			$userData['pref_non_smoker'] = $user->pref_non_smoker ? $user->pref_non_smoker : 0;
	        //$userData['photo'] = $user->photo ? URL('/public/images/'.$user->photo) : '';
	       	$userData['occupation_status']	= 	$user->occupation_status;
			$userData['religion_status']		= 	$user->religion_status;
			$userData['height_status'] 		= 	$user->height_status;
			$userData['pref_willing_to_dutch_status'] 	= 	 $user->pref_willing_to_dutch_status;
			$userData['pref_non_smoker_status'] 			= 	 $user->pref_non_smoker_status;
			$userData['is_setpreferences'] 			= 	 $user->is_setpreferences;
			$userData['is_active_profile'] 			= 	 $user->is_active_profile;
			$userData['is_email_verified'] 			= 	 $user->is_email_verified   ? $user->is_email_verified   : 0;
	   	
	   	}else{

	   		$userData['code'] = 410;
	   	}

		return $userData;
	}

	public function pref_profile($data){
		
		$user 	=	User::find($data['Id']);
		$query  = 0;
		/*if($user->is_email_verified != 1){

			$user->email 	= 	@$data['email'] ? $data['email']:$user->email;
		} 	

		if($user->is_phone_verified != 1){

        	$user->phone 	= 	@$data['phone'] ? $data['phone']:$user->phone;
		}*/	


		if(isset($data['email'])){

			$query = User::where('email',@$data['email'])->where('id','!=',@$data['Id'])->count();

		}else if(isset($data['phone'])){

			$query = User::where('phone',@$data['phone'])->where('id','!=',@$data['Id'])->count();

		}

		$code = 1234;//$CustomVerfication->generateRandomNumber(4);
		
		if($query == 0){
			
			$user->first_name 	= 	@$data['first_name'] ? $data['first_name'] : $user->first_name;
			$user->last_name 	= 	@$data['last_name'] ? $data['last_name'] : $user->last_name;
			$user->email 	= 	@$data['email'] ? $data['email'] : $user->email;
			
			
			$user->pref_gender	= 	@$data['pref_gender'] ? $data['pref_gender'] : $user->pref_gender;
			$user->pref_agegroup	= 	@$data['pref_agegroup'] ? $data['pref_agegroup'] : $user->pref_agegroup;
			$user->pref_race 	= 	@$data['pref_race'] ? $data['pref_race'] : $user->pref_race;
			$user->pref_religion	= 	@$data['pref_religion'] ? $data['pref_religion'] : $user->pref_religion;
			$user->pref_willing_to_dutch 	= 	@$data['pref_willing_to_dutch'] ? $data['pref_willing_to_dutch'] : $user->pref_willing_to_dutch;
			$user->pref_non_smoker 			= 	@$data['pref_non_smoker'] ? $data['pref_non_smoker'] : $user->non_smoker;
			$user->pref_min 			= 	@$data['pref_min'] ? $data['pref_min'] : $user->pref_min;
			$user->pref_max 			= 	@$data['pref_max'] ? $data['pref_max'] : $user->pref_max;
			$user->is_setpreferences 			= 	@$data['is_setpreferences'] ? $data['is_setpreferences'] : $user->is_setpreferences;
			
			$user->save();

			$userData['code'] = 200;
			$userData['id'] = $user->id;
	        $userData['pref_gender']	= 	$user->pref_gender ? $user->pref_gender : 0;
			$userData['pref_agegroup']	= 	$user->pref_agegroup ? $user->pref_agegroup : 0;
			$userData['pref_min']	= 	$user->pref_min ? $user->pref_min : 0;
			$userData['pref_max']	= 	$user->pref_max ? $user->pref_max : 0;
			$userData['pref_race'] 	= 	$user->pref_race ? $user->pref_race : 0;
			$userData['pref_religion']	= 	$user->pref_religion ? $user->pref_religion : 0;
			$userData['pref_willing_to_dutch'] = $user->pref_willing_to_dutch ? $user->pref_willing_to_dutch : 0;
			$userData['pref_non_smoker'] = $user->pref_non_smoker ? $user->pref_non_smoker : 0;
			$userData['is_setpreferences'] 			= 	 $user->is_setpreferences;
			$userData['is_active_profile'] 			= 	 $user->is_active_profile;
	   		
	   	}else{

	   		$userData['code'] = 410;
	   	}

		return $userData;
	}


	public function visibilty_profile($data){
		
		$user 	=	User::find($data['Id']);
		$query  = 0;
		/*if($user->is_email_verified != 1){

			$user->email 	= 	@$data['email'] ? $data['email']:$user->email;
		} 	

		if($user->is_phone_verified != 1){

        	$user->phone 	= 	@$data['phone'] ? $data['phone']:$user->phone;
		}*/	


		if(isset($data['email'])){

			$query = User::where('email',@$data['email'])->where('id','!=',@$data['Id'])->count();

		}else if(isset($data['phone'])){

			$query = User::where('phone',@$data['phone'])->where('id','!=',@$data['Id'])->count();

		}

		
		if($query == 0){
			
			$user->occupation_status	= 	@$data['occupation_status'] ? $data['occupation_status'] : $user->occupation_status;
			$user->religion_status	= 	@$data['religion_status'] ? $data['religion_status'] : $user->religion_status;
			$user->height_status 	= 	@$data['height_status'] ? $data['height_status'] : $user->height_status;
			$user->pref_willing_to_dutch_status 	= 	@$data['pref_willing_to_dutch_status'] ? $data['pref_willing_to_dutch_status'] : $user->pref_willing_to_dutch_status;
			$user->pref_non_smoker_status 			= 	@$data['pref_non_smoker_status'] ? $data['pref_non_smoker_status'] : $user->pref_non_smoker_status;
			
			$user->save();

			$userData['code'] = 200;
	   	
	   	}else{

	   		$userData['code'] = 410;
	   	}

		return $userData;
	}


	public function gallery($data){

		$filename = time();
		
		//	print_r($data); exit;
		if (@$data['p_photo'] != "") {
			$extension = $data['p_photo']->getClientOriginalExtension();
			if(strtolower($extension) == 'jpg' || strtolower($extension) == 'png' || strtolower($extension) == 'jpeg' ) {
				
				$FileLogo = $filename . '.' .$data['p_photo']->getClientOriginalExtension();
				$destinationPath = 'public/images';
				$data['p_photo']->move($destinationPath, $FileLogo);
				$documentFile = $destinationPath . '/' . $FileLogo;
				$upload = $FileLogo;
			}
		}		

		if($upload){
			$getphotolist =  Photo::where('p_u_id',$data['p_u_id'])->count(); 	
			$photo = new Photo();
			$photo->p_u_id = @$data['p_u_id'] ? $data['p_u_id']: '';
			if($getphotolist == 0){
				$photo->is_default = 1;
			}else{
				$photo->is_default = 2;

			}
			$photo->p_photo = @$upload;
			$photo->save();
			$lastid = $photo->id;
			$userData['code'] = 200;
			$userData['p_id'] = @$lastid;
			$userData['p_photo'] = @$photo->p_photo? URL('/public/images/'.$photo->p_photo):'';
			$userData['p_u_id'] = @$photo->p_u_id;
			$userData['is_default'] = @$photo->is_default;


		}else{

			$userData['code'] = 633;

		}

		return $userData;
	}

	public function view_gallery($data){

		$getphotolist =  Photo::where('p_u_id',$data['p_u_id'])->get();	
		
		$PhotoData = array();
		$PhotoArr = array();
		foreach($getphotolist as $list){

			$PhotoData['p_id'] 		=  @$list->p_id ? $list->p_id : '';
			$PhotoData['p_u_id'] 	=  @$list->p_u_id ? $list->p_u_id : '';
			$PhotoData['p_photo'] 	=  @$list->p_photo? URL('/public/images/'.$list->p_photo): '';
			$PhotoData['is_default'] 	=  @$list->is_default ? $list->is_default : '';
			array_push($PhotoArr,$PhotoData);
			
		}



		return $PhotoArr;
	}

	public function delete_gallery($data){

		$getphotolist =  Photo::where('p_id',$data['p_id'])->delete();	
		return 1;
	}

	public function delete_match($data){
		$getmatch = PendingMatches::where('id','=',$data['id'])
					->first();
		if(!empty($getmatch)){
			$getothermatch = PendingMatches::where('reciver_id','=',$getmatch['sender_id'])
					->where('sender_id','=',$getmatch['reciver_id'])
					->first();
			
			//$deleteMymatch =  PendingMatches::where('id',$getmatch['id'])->delete();
			PendingMatches::where('id', $getmatch['id'])
	       		->update([
	           'is_deleted' => 1
        	]);	
	       	PendingMatches::where('id', $getothermatch['id'])
	       		->update([
	           'is_deleted' => 1
        	]);		
			//$deleteOthermatch =  PendingMatches::where('id',$getothermatch['id'])->delete();
			return 1;
		}else{
			return 0;
		}
	}

	public function get_user_list($data){

		$getpatient = User::where('current_physican_id','=',$data['Id'])
						->where('user_type','=',2)->where('user_status','=',1)->get();

		$patient = array();
		$Patient_list = array();

		foreach($getpatient as $list){


			$patient['id'] 				=  	@$list->id ? $list->id : '';
			$patient['name'] 			=  	@$list->name ? $list->name : '';
			/*$patient['email'] 			=  	@$list->email ? $list->email : '';
			$patient['country_code'] 	= 	@$list->country_code ? $list->country_code : '';
			$patient['phone'] 			= 	@$list->phone ? $list->phone : '';
			$patient['photo'] 			=  	@$list->photo ? $list->photo : '';
			$patient['address'] 		=  	@$list->address ? $list->address : '';
			$patient['zip'] 			=  	@$list->zip ? $list->zip : '';
			$patient['gender'] 			=  	@$list->gender ? $list->gender : '';
			$patient['phone'] 			=  	@$list->phone ? $list->phone : '';*/
			
			array_push($Patient_list,$patient);
			
		}

		return $Patient_list;
	}

	public function update_forgot_code($userId,$code){
		
		$user = User::find($userId);
		$user->reset_key = $code;
		$user->save();
		return $user;
	}

	public function update_activation($userId){
		
		$user = User::find($userId);
		$user->activation_code = "";
		$user->user_status = 1;
		$user->is_email_verified = 1;

		$user->save();
		$data['userid'] = $userId;
		$data['name'] = $user['first_name'];
		$data['n_type'] = 1;
		$sender_name = $user['first_name'];
		$message =  $sender_name." your email account has been activated.";
		$notify = array ();
		$notify['receiver_id'] = $userId;
		$notify['relData'] = $data;
		$notify['message'] = $message;
		//print_r($notify); exit;
		$test =  $this->sendPushNotification($notify); 
		return $user;
	}

	public function update_password($data){
		
		//$user = User::where('reset_key', $data['code'])->where('email', $data['email'])->first();
		$user = User::where('id', $data['id'])->first();
		if($user){
			$forgot_password = 0;
			if($user->password != ''){
				$forgot_password = 1;
			}
			//if($user->reset_key == $data['code']){

				$user->password = hash::make($data['password']);
				$user->user_status = 1;
				$user->activation_code  = '';
				$user->is_phone_verified = 1;

				$user->save();

				$user->is_forgot = $forgot_password; 
			//}
		}
		
		return $user;
	}

	public function category_list($data){

		$category = Categories::where('c_status',1)->paginate(100,['*'],'page_no');
		$gender = Gender::where('status',1)->paginate(100,['*'],'page_no');

		$category_array = array();
		$category_list = array();

		foreach($category as $list){
			$category_array['c_id'] 			=  	@$list->c_id ? $list->c_id : '';
			$category_array['c_name'] 	=  	@$list->c_name ? $list->c_name : '';
			
			array_push($category_list,$category_array);
		}

		//echo '<pre>'; print_r($chip); exit;
		
		return $category;
	}
	public function gender_list($data){

		$gender = Gender::where('status',1)->paginate(100,['*'],'page_no');

		$gender_array = array();
		$gender_list = array();

		foreach($gender as $list){
			$gender_array['id'] 			=  	@$list->id ? $list->id : '';
			$gender_array['gender'] 	=  	@$list->gender ? $list->gender : '';
			
			array_push($gender_list,$gender_array);
		}
		
		//echo '<pre>'; print_r($chip); exit;
		
		return $gender;
	}

	public function race_list($data){

		$race = Race::where('status',1)->paginate(100,['*'],'page_no');

		$race_array = array();
		$race_list = array();

		foreach($race as $list){
			$race_array['id'] 			=  	@$list->id ? $list->id : '';
			$race_array['race'] 	=  	@$list->race ? $list->race : '';
			
			array_push($race_list,$race_array);
		}
		
		//echo '<pre>'; print_r($chip); exit;
		
		return $race;
	}

	public function religion_list($data){

		$religion = Religion::where('status',1)->paginate(100,['*'],'page_no');

		$religion_array = array();
		$religion_list = array();

		foreach($religion as $list){
			$religion_array['id'] 			=  	@$list->id ? $list->id : '';
			$religion_array['religion'] 	=  	@$list->religion ? $list->religion : '';
			
			array_push($religion_list,$religion_array);
		}
		
		//echo '<pre>'; print_r($chip); exit;
		
		return $religion;
	}

	public function report_list($data){

		$report = ReportList::paginate(100,['*'],'page_no');

		$report_array = array();
		$report_list = array();

		foreach($report as $list){
			$report_array['id'] 			=  	@$list->id ? $list->id : '';
			$report_array['gender'] 	=  	@$list->gender ? $list->report : '';
			
			array_push($report_list,$report_array);
		}
		
		//echo '<pre>'; print_r($chip); exit;
		
		return $report;
	}

	public function partner_type($data){

		$partner_type = PartnerType::paginate(100,['*'],'page_no');

		
		//echo '<pre>'; print_r($chip); exit;
		
		return $partner_type;
	}

	public function region($data){

		$region = Region::paginate(100,['*'],'page_no');

		
		//echo '<pre>'; print_r($chip); exit;
		
		return $region;
	}


	public function subcategory_list($data){
		$subcategory = SubCategories::where('sc_c_id',$data)->paginate(100,['*'],'page_no');
		$subcategory_array = array();
		$subcategory_list = array();

		foreach($subcategory as $list){
			$subcategory_array['sc_id'] 	=  	@$list->sc_id ? $list->sc_id : '';
			$subcategory_array['sc_name'] 	=  	@$list->sc_name ? $list->sc_name : '';
			
			array_push($subcategory_list,$subcategory_array);
		}
		//echo '<pre>'; print_r($chip); exit;
		
		return $subcategory;
	}

	public function mark_default($arg){
		$photo = Photo::where('p_id', $arg['p_id'])->first();
		if(!empty($photo)){
			/*$photo->p_id = $arg['p_id'];
			$photo->is_default = $arg['is_default'];
			//echo '<pre>'; print_r($photo); exit;
			$photo->save();*/
			Photo::where('p_u_id', $photo['p_u_id'])
	       		->update([
	           'is_default' => 0
        	]);
			Photo::where('p_id', $arg['p_id'])
	       		->update([
	           'is_default' => $arg['is_default']
        	]);
			$userData = Photo::where('p_id', $arg['p_id'])->first();
				$userData['code'] = 200;
			$userData['p_id'] = @$userData->p_id;
			$userData['p_photo'] = @$userData->p_photo? URL('/public/images/'.$userData->p_photo):'';
			$userData['p_u_id'] = @$userData->p_u_id;
			$userData['is_default'] = @$userData->is_default;
		}else{
			$userData['code'] = 431;
			//print_r($userData); exit;
		}
		return $userData;
	}

	public function getMatch($data,$userId){

		//send push notification
		/*$datass['userid'] = $userId;
		$datass['name'] = $user['first_name'];
		$datass['n_type'] = 2;
		$sender_name = $user['first_name'];
		$message =  $sender_name." find as match.";
		$notify = array ();
		$notify['receiver_id'] = $reciver_id;
		$notify['relData'] = $datass;
		$notify['message'] = $message;
		$test =  $this->sendPushNotification($notify); */
		//echo $userId; dd($data); exit;
		$user 	=	User::find($userId);
		if(isset($data)){

			$my_detail = User::where('id','=',@$userId)->first();
			/*$dob = Carbon::createFromFormat('d/m/Y', $my_detail['d_o_b']);
			$age = 0;
			if(!empty($dob)){
			$age = Carbon::parse($dob)->diff(Carbon::now())->y;
			}*/

		}
		//print_r($userId); exit;
		if(!empty($my_detail['id'])){
			
			$category_id = @$data['c_id'];
			$sub_category_id = @$data['sc_c_id'];
			$gender = @$my_detail['pref_gender'];
			$min = @$my_detail['pref_min'];
			$max = @$my_detail['pref_max'];
			$race  = @$my_detail['pref_race'];
			$religion   = @$my_detail['pref_religion'];
			$willing_to_dutch    = @$my_detail['pref_willing_to_dutch'];
			$non_smoker    = @$my_detail['pref_non_smoker'];
			//print_r($gender); exit;
			$modal     =  "App\Models\PendingMatches";
			$query = $modal::query();
			if($category_id != 0 && $sub_category_id != 0){
				User::where('id', $userId)
	       		->update([
	           'cat_id' => $category_id,
	           'sub_cat_id' => $sub_category_id,
	           'search_added_date' =>  date ( 'Y-m-d H:i:s' )
        		]);

	       		//find own user match 
	       		if($gender != 3){
		       		$find_all_user = User::where('id','!=',@$userId)
		       		->where('cat_id',$category_id)
		       		->where('sub_cat_id',$sub_category_id)
		       		->where('race',$race)
		       		->where('gender',$gender)
		       		->where('religion',$religion)
		       		->where('pref_willing_to_dutch',$willing_to_dutch)
		       		->where('non_smoker',$non_smoker)
		       		->whereBetween('age', [$min, $max])
		       		->orderBy('search_added_date', 'DESC')
		       		//->where('age',$age)
		       		->get();
	       		}else{
	       			$find_all_user = User::where('id','!=',@$userId)
		       		->where('cat_id',$category_id)
		       		->where('sub_cat_id',$sub_category_id)
		       		->where('race',$race)
		       		->where('religion',$religion)
		       		->where('pref_willing_to_dutch',$willing_to_dutch)
		       		->where('non_smoker',$non_smoker)
		       		->whereBetween('age', [$min, $max])
		       		->orderBy('search_added_date', 'DESC')
		       		//->where('age',$age)
		       		->get();
	       		}
	       		//print_r($find_all_user); exit;
	       		$other_user_id = 0;
	       		foreach ($find_all_user as $find_all_userkey => $find_all_uservalue) {
	       			//print_r($find_all_uservalue); exit;
       				$other_gender = @$find_all_uservalue['pref_gender'];
					$other_min = @$find_all_uservalue['pref_min'];
					$other_max = @$find_all_uservalue['pref_max'];
					$other_race  = @$find_all_uservalue['pref_race'];
					$other_religion   = @$find_all_uservalue['pref_religion'];
					$other_willing_to_dutch    = @$find_all_uservalue['pref_willing_to_dutch'];
					$other_non_smoker    = @$find_all_uservalue['pref_non_smoker'];
					
					// find other user preference match wuth my user
					if($other_gender != 3){
						$match_with_own_user = User::where('id','=',@$userId)
			       		->where('cat_id',$category_id)
			       		->where('sub_cat_id',$sub_category_id)
			       		->where('gender',$other_gender)
			       		->where('race',$other_race)
			       		->where('religion',$other_religion)
			       		->where('pref_willing_to_dutch',$other_willing_to_dutch)
			       		->where('non_smoker',$other_non_smoker)
			       		->whereBetween('age', [$other_min, $other_max])
		       			//->where('age',$age)
		       			->first();
	       			}else{
	       				$match_with_own_user = User::where('id','=',@$userId)
			       		->where('cat_id',$category_id)
			       		->where('sub_cat_id',$sub_category_id)
			       		->where('race',$other_race)
			       		->where('religion',$other_religion)
			       		->where('pref_willing_to_dutch',$other_willing_to_dutch)
			       		->where('non_smoker',$other_non_smoker)
			       		->whereBetween('age', [$other_min, $other_max])
		       			//->where('age',$age)
		       			->first();
	       			}
	       			if(isset($match_with_own_user['id'])){
	       				$other_user_id = $find_all_uservalue['id'];
	       			}
	       			# code...
	       		}
	       		//print_r($other_user_id); exit;
	       		// Get other user
	       		if($gender !=3){
		       		$find_user = User::where('id','=',@$other_user_id)
		       		->where('cat_id',$category_id)
		       		->where('sub_cat_id',$sub_category_id)
		       		->where('gender',$gender)
		       		->where('race',$race)
		       		->where('religion',$religion)
		       		->where('pref_willing_to_dutch',$willing_to_dutch)
		       		->where('non_smoker',$non_smoker)
		       		->whereBetween('age', [$min, $max])

		       		//->where('age',$age)
		       		->first();
		       	}else{
		       		$find_user = User::where('id','=',@$other_user_id)
		       		->where('cat_id',$category_id)
		       		->where('sub_cat_id',$sub_category_id)
		       		->where('race',$race)
		       		->where('religion',$religion)
		       		->where('pref_willing_to_dutch',$willing_to_dutch)
		       		->where('non_smoker',$non_smoker)
		       		->whereBetween('age', [$min, $max])
		       		//->where('age',$age)
		       		->first();
		       	}
	       		//print_r($find_user); exit;
	       		if(isset($find_user['id'])){
	       			//if find match then delete pending
	       			$check_pending  = $modal::query()->select('pending_matches.*')
					->where('pending_matches.sender_id','=',@$userId)
					->where('pending_matches.is_pending','=',1)
					->where('pending_matches.is_deleted','=',0)
					->first();
					if($check_pending['id'] != ''){
	       				$deleteoldpending =  $modal::where('id', $check_pending['id'])->delete();
					}
					
					// check pervious matched
					$reciver_id = $find_user['id'];
					$check_pervious  = $modal::query()->select('pending_matches.*')
					->where('pending_matches.sender_id','=',@$userId)
					->where('pending_matches.is_pending','=',0)
					->where('pending_matches.reciver_id','=',$reciver_id)
					->where('pending_matches.is_deleted','=',0)
					->first();
					
					User::where('id', $reciver_id)
			       		->update([
			           'cat_id' => $category_id,
			           'sub_cat_id' => $sub_category_id,
			           'search_added_date' =>  date ( 'Y-m-d H:i:s' )
		        		]);
					//print_r($check_pervious); exit;
					PendingMatches::where('sender_id', $userId)
					->where('is_new',1)
		       		->update([
		           'is_new' => 0,
		          	]);
					if(!isset($check_pervious['id'])){ //not find before in match
					
						$pending_matches = new PendingMatches();
						$pending_matches->sender_id = $userId;
						$pending_matches->reciver_id  = $find_user['id'];
						$pending_matches->cat_id = $category_id;
						$pending_matches->sub_cat_id = $sub_category_id;
						$pending_matches->added_date 	=   date ( 'Y-m-d H:i:s' );
						$pending_matches->is_new 	=   1;
						//echo '<pre>'; print_r( $pending_matches); exit;
						$pending_matches->save();
						$is_new = 1; 
						// check receiver pending request if yes create  match
						$find_receiver_pending  = $modal::query()->select('pending_matches.*')
						->where('pending_matches.sender_id','=',@$reciver_id)
						->where('pending_matches.is_pending','=',1)
						->where('pending_matches.cat_id','=',$category_id)
						->where('pending_matches.sub_cat_id','=',$sub_category_id)
						->where('pending_matches.is_deleted','=',0)
						->first();
						//print_r($find_receiver_pending); exit;
						if(isset($find_receiver_pending['id'])){
							PendingMatches::where('id', $find_receiver_pending['id'])
				       		->update([
				           'is_pending' => 0,
				           'reciver_id' => $userId,
				           ]);
				       		/*User::where('id', $find_receiver_pending['id'])
				       		->update([
				           'cat_id' => $category_id,
				           'sub_cat_id' => $sub_category_id,
				           'search_added_date' =>  date ( 'Y-m-d H:i:s' )
			        		]);*/
						}else{// receivce genrate dummy search and create match and update search time

							$pending_matches = new PendingMatches();
							$pending_matches->sender_id = $find_user['id'];
							$pending_matches->reciver_id  = $userId;
							$pending_matches->cat_id = $category_id;
							$pending_matches->sub_cat_id = $sub_category_id;
							$pending_matches->added_date 	=   date ( 'Y-m-d H:i:s' );
							$pending_matches->is_new 	=   0;
							//echo '<pre>'; print_r( $pending_matches); exit;
							$pending_matches->save();

						

						}

					}else{  /// find old and add blank receiver
						$pending_matches = new PendingMatches();
						$pending_matches->sender_id = $userId;
						$pending_matches->cat_id = $category_id;
						$pending_matches->sub_cat_id = $sub_category_id;
						$pending_matches->added_date 	=   date ( 'Y-m-d H:i:s' );
						$pending_matches->is_pending = 1;
						$pending_matches->is_new 	=   1;
						//echo '<pre>'; print_r( $arg); exit;
						$pending_matches->save();
					}
					//send push notification
					$datass['userid'] = $userId;
					$datass['name'] = $user['first_name'];
					$datass['n_type'] = 2;
					$sender_name = $user['first_name'];
					$message =  $sender_name." find as match.";
					$notify = array ();
					$notify['receiver_id'] = $reciver_id;
					$notify['relData'] = $datass;
					$notify['message'] = $message;
					$test =  $this->sendPushNotification($notify); 
				}else{
					
					PendingMatches::where('sender_id', $userId)
			       		->update([
			           'is_new'	=> 0,
			            ]);

					$check  = $modal::query()->select('pending_matches.*')
					->where('pending_matches.sender_id','=',@$userId)
					->where('pending_matches.is_pending','=',1)
					->orderBy('pending_matches.id', 'DESC')->first();
					if(!isset($check['id'])){
						
						$pending_matches = new PendingMatches();
						$pending_matches->sender_id = $userId;
						$pending_matches->cat_id = $category_id;
						$pending_matches->sub_cat_id = $sub_category_id;
						$pending_matches->is_pending = 1;
						$pending_matches->is_new 	=   1;
						$pending_matches->added_date 	=   date ( 'Y-m-d H:i:s' );
						//echo '<pre>'; print_r( $arg); exit;
						$pending_matches->save();
					}else{ //update
						///////////
						// update is_new  = 0 
						
						//////
						PendingMatches::where('id', $check['id'])
			       		->update([
			           'cat_id' => $category_id,
			           'sub_cat_id' => $sub_category_id,
			           'is_new'	=> 1,
			           'added_date' =>  date ( 'Y-m-d H:i:s' )
		        		]);

						////////
					}
				}
				//$query =$query->where('order_manage.user_id','=',@$category_id);
				
			}
			$user =$query->select('customer.*','pending_matches.*')
				->leftjoin('users as customer','pending_matches.reciver_id','customer.id')
				//->where('pending_matches.reciver_id','!=',@$userId)
				->where('pending_matches.sender_id','=',@$userId)
				->where('pending_matches.is_new','=',1)
				->where('pending_matches.is_deleted','=',0)
				//->leftjoin('jhi_user as  supplier','order_manage.supplier_id','supplier.id')
				->orderBy('pending_matches.id', 'DESC')->first();
				//print_r($user); exit;
			////////////

			$userData =array();	
			$userData['myMatch'] = array();
				$userData['isFromSubCategory'] = 0;
			if(isset($user['id'])){
				if($user['is_pending'] == 1){
					$userData['isFromSubCategory'] = 1;
				}
				if($user['is_new'] == 1){
					$userData['myMatch'][0]['id'] = $user['id'];
					$category = Categories::where('c_id',$user['cat_id'])->first();
					$subcategory = SubCategories::where('sc_id',$user['sub_cat_id'])->first();
					//print_r($subcategory); exit; 
					$photo = Photo::where('p_u_id',  $user['reciver_id'])->where('is_default', 1)->first();
					$userData['myMatch'][0]['c_id'] = @$category['c_id']?$category['c_id']:0;
					$userData['myMatch'][0]['c_name'] = @$category['c_name']?$category['c_name']:'';
					$userData['myMatch'][0]['sc_c_id'] = @$subcategory['sc_id']?$subcategory['sc_id']:0;
					$userData['myMatch'][0]['sc_name'] = @$subcategory['sc_name']?$subcategory['sc_name']:'';
					if(isset($user['phone'])){
						$userData['myMatch'][0]['p_photo'] = @$photo->p_photo? URL('/public/images/'.$photo->p_photo):'';
						$userData['myMatch'][0]['p_id'] = @$photo->p_id? $photo->p_id:'';
				        $userData['myMatch'][0]['first_name'] = $user['first_name'] ? $user['first_name'] : '';
				        $userData['myMatch'][0]['age'] 	= 	$user['age'].' Years';
						$userData['myMatch'][0]['race'] 	= 	$user['race'] ? $user['race'] : 0;

						$userData['myMatch'][0]['occupation_status'] = 	$user['occupation_status'] ? $user['occupation_status'] : 1;
						$userData['myMatch'][0]['occupation'] = 	$user['occupation'] ? $user['occupation'] : '';
						$userData['myMatch'][0]['descr'] = 	$user['description'] ? $user['description'] : '';
						$userData['myMatch'][0]['is_pending'] = 0;
						$userData['myMatch'][0]['sender_id'] = 	$user['sender_id'] ? $user['sender_id'] :'';
						$userData['myMatch'][0]['reciver_id'] = 	$user['reciver_id'] ? $user['reciver_id'] :'';
					}else{
						$userData['myMatch'][0]['is_pending'] = 1;

					}
				}else{
					//echo 'tt'; exit;
					$category = Categories::where('c_id',@$category_id)->first();
					$subcategory = SubCategories::where('sc_id',@$sub_category_id)->first();
					if($category['c_id'] != 0){
						$userData['myMatch'][0]['c_id'] = @$category['c_id']?$category['c_id']:0;
						$userData['myMatch'][0]['c_name'] = @$category['c_name']?$category['c_name']:'';
						$userData['myMatch'][0]['sc_c_id'] = @$subcategory['sc_id']?$subcategory['sc_id']:0;
						$userData['myMatch'][0]['sc_name'] = @$subcategory['sc_name']?$subcategory['sc_name']:'';
						$userData['myMatch'][0]['is_pending'] = 1;
						$userData['isFromSubCategory'] = 0;
					}
				}
			}

			//   My Match List
			$query1 = $modal::query();
			$myMatch =$query1->select('customer.*','pending_matches.*')
				->leftjoin('users as customer','pending_matches.reciver_id','customer.id')
				->where('pending_matches.reciver_id','!=',@$userId)
				->where('pending_matches.sender_id','=',@$userId)
				->where('pending_matches.is_new','=',0)
				->where('pending_matches.is_deleted','=',0)
				//->leftjoin('jhi_user as  supplier','order_manage.supplier_id','supplier.id')
				->groupBy('pending_matches.reciver_id')
				->orderBy('pending_matches.id', 'DESC')->get();
			//print_r($myMatch); exit;
			$mymatch_array = array();
			$userData['previousMatch'] = array();
			//$userData['myMatch'] = array();
			foreach($myMatch as $key => $list){
				$userData['previousMatch'][$key]['id'] = $list['id'];
				$photo = Photo::where('p_u_id',   $list['reciver_id'])->where('is_default', 1)->first();
				$userData['previousMatch'][$key]['p_photo'] = @$photo->p_photo? URL('/public/images/'.$photo->p_photo):'';
				$userData['previousMatch'][$key]['p_id'] = @$photo->p_id? $photo->p_id:'';
				$category = Categories::where('c_id',$list['cat_id'])->first();
				$subcategory = SubCategories::where('sc_id',$list['sub_cat_id'])->first();
				$userData['previousMatch'][$key]['c_name'] = @$category['c_name'];
				$userData['previousMatch'][$key]['sc_name'] = @$subcategory['sc_name'];
		        $userData['previousMatch'][$key]['first_name'] = $list['first_name'] ? $list['first_name'] : '';
		        $userData['previousMatch'][$key]['age'] 	= 	$list['age'].' Years';
				$userData['previousMatch'][$key]['race'] 	= 	$list['race'] ? $list['race'] : 0;
				$userData['previousMatch'][$key]['occupation'] = 	$list['occupation'] ? $list['occupation'] : '';

				$userData['previousMatch'][$key]['occupation_status'] = 	$list['occupation_status'] ? $list['occupation_status'] : 1;
				$userData['previousMatch'][$key]['descr'] = 	$list['description'] ? $list['description'] :'';
				$userData['previousMatch'][$key]['sender_id'] = 	$list['sender_id'] ? $list['sender_id'] :'';
				$userData['previousMatch'][$key]['reciver_id'] = 	$list['description'] ? $list['reciver_id'] :'';
			}
			
	   	
	   	}else{

	   		$userData['code'] = 410;
	   	}
	   	if(count($userData['previousMatch']) == 0 || count(@$userData['myMatch']) == 0){
				$userData['code'] = 647;
		}else{
			$userData['code'] = 200;
				
		}

		return $userData;
	}

	public function getPendingMatch($data,$userId){
		$modal     =  "App\Models\PendingMatches";
		$query = $modal::query();
		$user =$query->select('customer.*','pending_matches.*')
			->leftjoin('users as customer','pending_matches.reciver_id','customer.id')
			->where('pending_matches.sender_id','=',@$userId)
			->where('pending_matches.is_pending','=',1)
			//->leftjoin('jhi_user as  supplier','order_manage.supplier_id','supplier.id')
			->orderBy('pending_matches.id', 'DESC')->first();
		////////////
		$userData =array();
		$userData['is_pending'] = 0;	
		if(isset($user['id'])){
			$userData['code'] = 200;
			$userData['is_pending'] = 1;
			$userData['cat_id'] = $user['cat_id'];
			$userData['sub_cat_id'] = $user['sub_cat_id'];
		}else{

	   		$userData['code'] = 410;
	   	}

		return $userData;
	}

	public function report($arg,$userId){
		$checkreport = Report::where('user_id', $userId)->where('reported_user', $arg['reported_user'])->first();
		if(empty($checkreport)){
			$report = new Report();
			$report->user_id = $userId;
			$report->photo_id = $arg['photo_id'];
			$report->reported_user = intval($arg['reported_user']);
			$report->report_type = $arg['report_type'];
			$report->report_desc = @$arg['report_desc'];
			//echo '<pre>'; print_r($report); exit;
			$report->save();
			return 1;
		}else{
			return 0;
		}		
	}

	public function recommend_list($data){
		$partner = Partner::where('status','=',1)
		->where('is_recommend','=',1)
		->where('is_premium','=',1)
		->paginate(5,['*'],'page_no');
		//echo '<pre>'; print_r($partner); exit;
		return $partner;
	}

	public function partner_list($data){
		$model 		= "App\Models\Partner";	
		$region = @$data['region'];
		$partner_type = @$data['partner_type'];
		$category = @$data['category'];
		$query = $model::query();
			if(isset($region)){
				$query =$query->where('region','=',@$region);
			}

			if(isset($partner_type)){
				//echo $selected_date ; exit;
				$query =$query->where('type','=',@$partner_type);
			}

			if(isset($category)){
				//echo $selected_date ; exit;
				$query =$query->where('category ','=',@$category);
			}


			
			$query = $query->where('status',1)
					->orderBy('id', 'DESC')
					->paginate(10,['*'],'page_no');

			$query->total_count = $model::where('status',1)
					->count();
			$partner = $query;

		/*$partner = Partner::where('status','=',1)->paginate(10,['*'],'page_no');
		$partner_array = array();
		$Partner_list = array();*/

		/*foreach($partner as $list){
			$partner_array['id'] 			=  	@$list->id ? $list->id : '';
			$partner_array['name'] 	=  	@$list->name ? $list->name : '';
			$partner_array['desc'] 	=  	@$list->desc ? $list->desc : '';
			$partner_array['photo'] 		=  	@$list->photo ? $list->photo : '';
			$partner_array['status'] 		=  	@$list->status ? $list->status : '';
			
			array_push($Partner_list,$partner_array);
		}*/
		//echo '<pre>'; print_r($partner); exit;
		
		return $partner;
	}

	public function partner_detail($data){
		$partner = Partner::where('id', $data)
		->leftjoin('categories','partners.category','categories.c_id')
		->leftjoin('sub_categories','partners.sub_category','sub_categories.sc_c_id')
		->first();
		//print_r($partner); exit;
		$userData['id'] = $partner['id'];
		$userData['photo'] = @$partner['photo']? URL('/public/images/'.$partner['p_photo']):'';
        $userData['name'] = $partner['name'] ? $partner['name'] : '';
        $userData['desc'] = $partner['desc'] ? $partner['desc'] : '';
        $userData['category'] = $partner['c_name'] ? $partner['c_name'] : '';
        $userData['sub_category'] = $partner['sc_name'] ? $partner['sc_name'] : '';
        $userData['location'] = $partner['location'] ? $partner['location'] : '';
        $userData['opening'] = $partner['opening'] ? $partner['opening'] : '';
        $userData['closing'] = $partner['closing'] ? $partner['closing'] : '';
        $userData['suitable'] = $partner['suitable'] ? $partner['suitable'] : '';
        $userData['promo_code'] = $partner['promo_code'] ? $partner['promo_code'] : '';
        $userData['promo_detail'] = $partner['promo_detail'] ? $partner['promo_detail'] : '';
        $userData['is_recommend'] = $partner['is_recommend'] ? $partner['is_recommend'] : 0;
        $userData['is_premium'] = $partner['is_premium'] ? $partner['is_premium'] : 0;
         


                    
		return $userData;
	}

	public function check_email($data,$userId){
		$checkEmail = User::where('email', $data['email'])->first();
		////////////
		//print_r($userId); exit;
		//print_r($checkEmail['id']); exit;
		$userData =array();
		$userData['is_email_available'] = 0;	
		if(!isset($checkEmail['id'])){
			$userData['code'] = 200;
			$userData['is_email_available'] = 1;
		}else{
			if($checkEmail['id'] == $userId){
				$userData['code'] = 200;
				$userData['is_email_available'] = 1;
			}else{
	   			$userData['code'] = 410;
	   		}
	   	}

		return $userData;
	}


	public function update_device($data,$userId){
		$checkEmail = User::where('id', $userId)
	       		->update([
	           'device_token' => @$data['device_token'] ,'device_type' => @$data['device_type'],
	           'device_id' => @$data['device_id']
        ]);	
		////////////
		//print_r($userId); exit;
		//print_r($checkEmail['id']); exit;
		$userData =array();
		$userData['code'] = 200;
		$userData['device_token'] = $data['device_token'];
		
		return $userData;
	}


	public function sendPushNotification($notify) {

		$data                       = $notify['relData'];
		$receiver_id                = trim($notify['receiver_id']); 
		$message                    = trim($notify['message']);
	    // $badge                      = trim(@$_POST['badge']);
		if (strlen($message) > 189) {
			$message = substr($message, 0, 185);
			$message = $message . '...';
		}else{
			$message = $message;
		}
		$check_user 	=	User::find($receiver_id);
		
		$badge = 1;
		/*$notificationTable = TableRegistry::get('Notifications');
		$badge = $notificationTable
					->find()
					->where(['n_u_id'=> $receiver_id])
					->where(['n_type != 5'])
					->where(['n_status' => 0])
					->count();
		//print_r($badge);
		if($badge == 0){
		}else{
			$badge = $badge+1;
		}*/
		//prd($data);
		//print_r($check_user['device_type']); exit;

		if (empty($receiver_id)) {
			exit;
		}
		if ($check_user['device_type'] == 0) { //ios
			$check_user['device_id'] = trim($check_user['device_id']);
			if($check_user['device_id'] != ''){
				if(!empty($message)){
					$this->iphone_push($check_user['device_id'], $message,  $data, $badge);
				}
			}
			//$this->android_push($check_user['device_id'], $message,  $data, $badge=0);
		}else{ //android
			//dd($check_user);
			if($check_user['device_id'] != ''){
				if(!empty($message)){
					$this->android_fcm_push($check_user['device_id'], $message,  $data, $badge);
				}
			}
		}
	   
		//return;
	}

	//  FCM
	public function android_push($id, $message, $relData, $badge){
		header('Content-type: text/html; charset=utf-8');
		// API access key from Google API's Console
		//CGT Key
		//prd($id);
		//Client Account
		$API_ACCESS_KEY  = 'AAAAh1ldrwA:APA91bFLkaiO52hj8z04m6tiCOt06wKejWKMR7uQLup9XbJ9fPoTelGjmrKYre-oBhhVRj5UEwspjbL3fMmMq3LMKcAv52exUWrWuFcSqdQQRKcxBrCCBLIB5vPtloUIBVll3zmvg8u7';
	   	
	   //	$id = 'courlEezNQ0:APA91bEPfxQbaJUUD_WakvYMZLyxDpKu6ydF1vXIu6j3QwGcPQFVWTS2H3oAayHRXsIGt39D_XcJ5qVtSJSKfjZpnZJ9zGLtvE9pk5xq_n4s2dIv_yv0XcnMVDvI6XlWq8p-1WXJRcy7';
		$registrationIds = array($id);
		//echo 'come'; exit;
		$msg['data']= array(
		'message' => $message,
		'badge' => (int)$badge,
		'relData' => $relData,
		//'vibrate' => 1,
		
		//'data'=>$data
		);
	   
		$fields = array(
					   'registration_ids' => $registrationIds,
					   'data' => $msg,
					   'title' => 'Eureka',
					   'priority'=>'high',
					   'sound' => 'default',
					   //'relData' => $relData
						);
		//prd($fields);
		$headers         = array(
		'Authorization: key=' . $API_ACCESS_KEY,
		'Content-Type: application/json'
		);
		$ch        = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
					curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
		$result = curl_exec($ch);
		//curl_close($ch);
		$res = json_decode($result,true);
		//print_r($res); exit;
		if($res['success']){
		echo 'complete'; exit;
		curl_close($ch);
		return 1;
		}else{
		  echo 'not'; exit;
		curl_close($ch);
		return 0;
		}
	}

	// iphone FCM 
	public function android_fcm_push($id, $message, $relData, $badge){
		
		$url = "https://fcm.googleapis.com/fcm/send";
		$token =  $id; 
		//Client key
		//prd($relData['notification_title']);
		$serverKey = 'AAAAHp-Z9H8:APA91bHOXWGp1gNrqfuoTBioHB2JXpyRYSwehL7CQ62_hAJ9f-l5lHN1a6_2KR2QhBV1l4usJxeP9LQMRwJbGxbWdnCHQqhLaV-edGYkTdq4qucl7o7qMT3u5nSlLRLGGst18ysyQQYA';
		$title = "Hopple";
 		if(isset($relData['notification_title'])){
			$title = $relData['notification_title'];
		}
		
		$body = $message;
		$msg['data']= array(
		'message' => $message,
		'relData' => $relData,
		'badge' => (int)$badge,
		);
		$notification = array('title' =>$title , 'text' => $body, 'sound' => 'default', 'badge' => $badge);
		//$arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high','data'=>$msg);
		$arrayToSend = array('to' => $token, 'priority'=>'high','data'=>$msg );
		$json = json_encode($arrayToSend);
		//print_r($json);exit;
		$headers = array();
		$headers[] = 'Content-Type: application/json';
		$headers[] = 'Authorization: key='. $serverKey;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST,

		"POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
		//Send the request
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$response = curl_exec($ch);
		//print_r($response); exit;
		//Close request
		if ($response === FALSE) {
		die('FCM Send Error: ' . curl_error($ch));
		}
		curl_close($ch);	
	}
	
	// Iphone APNS
	public function iphone_push($id, $message, $relData, $badge) {
		header('Content-type: text/html; charset=utf-8');
		//  echo $deviceToken = $id; exit;
		// $deviceToken = '4ab26204eea4e9225414dd81e3518a1015da7e353e7f82ebf71eadaafae17fd8';
		// Put your private key's passphrase here:
		$deviceToken  = $id;
		$deviceToken  = trim($deviceToken);  
		$passphrase  = '';
		// //////////////////////////////////////////////////////////////////////////////
		//$ctx         = stream_context_create();
		$ctx = $streamContext = stream_context_create([
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false
            ]
        ]);
		$pem_path = URL('/public/HopplePushCertificatesPemLive.pem');
		stream_context_set_option($ctx, 'ssl', 'local_cert', $pem_path );
		//stream_context_set_option($ctx, 'ssl', 'local_cert', './Meprosh_Development.pem');
		//echo stream_context_set_option($ctx, 'ssl', 'local_cert', $_SERVER['DOCUMENT_ROOT'].$this->webroot.'ck.pem');
		stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
		// Open a connection to the APNS server
		//$fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
		$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
		//print_r($fp);
		if (!$fp)
			exit("Failed to connect: $err $errstr" . PHP_EOL);
			//echo 'Connected to APNS' . PHP_EOL;
			// Create the payload body
			//$resp = $this->cpSTR_to_utf8STR($message);
			//$this->writeResponseLog($resp);
			//$m = (string) $this->cpSTR_to_utf8STR($message);
		//echo strlen($message);
		$title = "Hopple";
 		
 		if(isset($relData['notification_title'])){
			$title = $relData['notification_title'];
		}

		$body['aps'] = array(
		'alert' => html_entity_decode($message, ENT_NOQUOTES, 'UTF-8'),
		'title' => $title,
		'sound' => 'default',
		'badge' => (int)$badge,
		'relData' => $relData,
		
	
		);
		//print_r($body); 
		//$this->writeResponseLog($body);
		//echo $count;
		// Encode the payload as JSON
		$payload = json_encode($body);
		//echo strlen($payload); exit;
		// Build the binary notification
		$msg     = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
		$msg     = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
		// Send it to the server
		$result  = fwrite($fp, $msg, strlen($msg));
	    //print_r($result); exit;
	   /* if (! $result)
			echo 'Message not delivered' . PHP_EOL;
		else
			echo 'Message successfully delivered' . PHP_EOL;*/
			
			//Close the connection to the server
			fclose($fp);
			return;
	}

	//subscriptionsList => It is used for get Subscription plan List
	public function subscriptionsList(){
        
        $query = Subscription::where('country', 'US')->get();
        if(!empty($query)){
        	//$query =  $query->toArray();
        	$query->code = 200;
        }else{
        	$query->code = 400;
        }
        return $query;
    }

	//subscriptions -> It is used for get Subscription Type
	public function subscriptions()
	{ 

        if ($this->request->is('get')){

            $data = $this->request->query;

            $uid = $this->userid;

            $SubscriptionsModel = TableRegistry::get('Subscriptions'); //use Cake\ORM\TableRegistry;

            $querySubscriptions = $SubscriptionsModel

            ->find();

            $TransactionsModel = TableRegistry::get('Transactions'); //use Cake\ORM\TableRegistry;

            $query = $TransactionsModel

            ->find()

            ->contain(['Subscriptions','Users'])

            ->where(['user_id'=>$uid])

            ->where(['payment_status'=>'1'])

            ->where(['NOW()<`expired_at`'])

            ->order(['expired_at'=>'DESC'])

             ->first();

            $timestamp = strtotime(date('Y-m-d H:i:s'));

            if(!empty($query)){

                $query =  $query->toArray();

                if(!empty($query['user']['id'])){

                    $addded_date = strtotime($query['user']['added_date']);

                }else{

                    $addded_date ='';

                }

                if($query['device_type']== 0){

                     $this->set([

                    'data' => array('Subscriptions'=>$querySubscriptions,'timestamp' =>$timestamp,'plan_name'=>$query['subscription']['name'],'plan_id'=>$query['subscription_id'],'added_date'=>$addded_date,'itune_original_transaction_id'=>$query['itune_original_transaction_id'],'itunes_receipt'=>json_decode($query['itunes_receipt'])),

                    'code' => 209,

                    'msg'=> responseMsg(209),

                    '_serialize' => ['code','data','msg']

                 ]);



                }else{     

                    $this->set([

                        'data' => array('Subscriptions'=>$querySubscriptions,'timestamp' =>$timestamp,'plan_name'=>$query['subscription']['name'],'plan_id'=>$query['subscription_id'],'added_date'=>$addded_date,'itune_original_transaction_id'=>$query['itune_original_transaction_id'],),

                        'code' => 209,

                        'msg'=> responseMsg(209),

                        '_serialize' => ['code','data','msg']

                     ]);

                }

            }else{

                $querySubscriptions =  $querySubscriptions->toArray();

                  $this->set([

                    'data' => array('Subscriptions'=>$querySubscriptions,'timestamp' =>$timestamp,'plan_name'=>'','plan_id'=>0,'added_date'=>'','itune_original_transaction_id'=>''),

                    'code' => 209,

                    'msg'=> responseMsg(209),

                    '_serialize' => ['code','data','msg']

                 ]);

            }

        }
    }

	//newSubscriptionPlan => It is used for Add new Subscription Plan (not need)
	public function newSubscriptionPlan()
    { 

        if ($this->request->is('post')){



            $data = $this->request->data;

            //pr($data);

            $u_id = $this->userid;

            $this->loadModel('Transactions');

            $Transactions = TableRegistry::get('Transactions'); 

            $transaction = $this->Transactions->newEntity();

            $transaction = $this->Transactions->patchEntity($transaction, $data);

            $transaction ['user_id'] = $this->userid;

            $created_at = $data['created_at']/1000;

            $transaction ['created_at'] =  date('Y-m-d H:i:s', $created_at);

            $expired_at = $data['expired_at']/1000;

            $transaction ['expired_at'] =date('Y-m-d H:i:s', $expired_at);

            

            //prd($transaction);

            if ($this->Transactions->save($transaction)){

                $this->loadModel('Users');

                $UserModel = TableRegistry::get('Users'); //use Cake\ORM\TableRegistry;

                $user = $UserModel->get($u_id);

                $user->itunes_autorenewal = 0;

                $user->active_subscription = $data['subscription_id'];

                $user->last_transaction_id = $transaction_last_id;

                $UserModel->save($user);

                $this->set([

                    'msg'=> responseMsg(210),

                    'code'  => 200,

                    '_serialize' => ['code','msg']

                ]);

                

            }else{

                 $this->set([

                    'msg'=> responseMsg(418),

                    'code'  => 418,

                    '_serialize' => ['code','msg']

                ]);

            }

        }
    }


	//actionCheckTransactionId => This function is used to check original trasaction id of itunes.
	public function actionCheckTransactionId()
    {   

        $Transactions = TableRegistry::get('Transactions'); 

        if ($this->request->is('post')){

            $data = $this->request->data;

            $userId = $this->userid;

            $itune_original_transaction_id = $data['itune_original_transaction_id'];

            $subscription = $Transactions

            ->find()

            ->where(['itune_original_transaction_id'=> $itune_original_transaction_id])

            ->where(['NOW()>`expired_at`'])

            ->first();  

            if(empty($subscription)){

                 $this->set([

                    'msg'=> responseMsg(210),

                    'data' => '',

                    'code'  => 200,

                    '_serialize' => ['code','msg','data']

                 ]);

            }else{

                $this->set([

                    'msg'=> responseMsg(436),

                    'data' => '',

                    'code'  => 436,

                    '_serialize' => ['code','msg','data']

                 ]);

            }

        }
    }  

	//pendingSubscriptionPlan =>  It is used for save the purchased plan which is pending
 	public function pendingSubscriptionPlan()
    { 

        if ($this->request->is('post')){

            $data = $this->request->data;

            $u_id =  $this->userid;

            $itunesReceipt = $data['itunes_receipt'];

            $receiptData = '{"receipt-data":"'.$itunesReceipt.'","password":"'. Configure::read('ITUNES_PASSWORD').'"}';

            $endpoint =  Configure::read('VERIFY_RECEIPT_URL');



            $userId = $this->userid;

            $this->loadModel('Transactions');

            $TransactionsModel = TableRegistry::get('Transactions'); //use Cake\ORM\TableRegistry;

            $query = $TransactionsModel

            ->find()

            ->contain(['Subscriptions'])

            ->where(['user_id'=>$u_id])

            ->where(['payment_status'=>'1'])

            ->where(['NOW()<`expired_at`'])

            ->order(['expired_at'=>'DESC'])

            ->first();            

            

            $ch = curl_init($endpoint);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLOPT_POST, true);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $receiptData);

            $errno = curl_errno($ch);

            //prd($errno);

            if($errno==0){

                $response = curl_exec($ch);

                $receiptInfo = json_decode($response,true);

                //prd($receiptInfo);

                if(!empty($receiptInfo)){

                    if(isset($receiptInfo['status']) && $receiptInfo['status']==0){

                        $latestReceiptInfo = $receiptInfo['latest_receipt_info'];

                        $latestTransactioninfo = $latestReceiptInfo[count($latestReceiptInfo)-1];

                        //echo'<pre>';print_r($latestTransactioninfo);

                        $SubscriptionModel = TableRegistry::get('Subscriptions'); //use Cake\ORM\TableRegistry;

                        $subscriptionData = $SubscriptionModel

                        ->find()

                        ->select(['id','price'])

                        ->where(['itunes_product_id'=>$latestTransactioninfo['product_id']])

                        ->first();    

                        //prd($subscriptionData);

                        $Transactions = TableRegistry::get('Transactions'); 

                        $transaction = $this->Transactions->newEntity();

                        $transactionData['user_id'] = $this->userid;

                        $transactionData['subscription_id'] = $subscriptionData['id'];

                        $transactionData['total_amount'] = $subscriptionData['price'];

                       

                        $transactionData['payment_status'] = '1';

                        $transactionData['itune_original_transaction_id'] = $latestTransactioninfo['original_transaction_id'];

                        $transactionData['itunes_receipt'] = $itunesReceipt;

                        $transactionData['orderId'] = $latestTransactioninfo['transaction_id'];

                        $transactionData['packageName'] = $latestTransactioninfo['product_id'];

                        $transactionData['productId'] = $latestTransactioninfo['product_id'];

                        $transactionData['purchaseTime'] = date('Y-m-d H:i:s',strtotime($latestTransactioninfo['purchase_date']));

                        $transactionData['purchaseState'] = 1;

                        

                        $transaction = $this->Transactions->patchEntity($transaction, $transactionData);

                        $transaction['created_at'] = date('Y-m-d H:i:s',strtotime($latestTransactioninfo['purchase_date']));

                        $transaction['expired_at'] = date('Y-m-d H:i:s',strtotime($latestTransactioninfo['expires_date']));

                        $transaction['device_type'] = 1;

                        $transaction['purchaseToken'] = 'Iphone';

                        //prd($transaction);

                        if ($result = $this->Transactions->save($transaction)){

                            $transaction_last_id = $result['id'];

                            $UserModel = TableRegistry::get('Users'); //use Cake\ORM\TableRegistry;

                           /* $userModel = $UserModel

                            ->find()

                            ->where(['id'=>$u_id ])

                            ->first(); */

                            $user = $UserModel->get($u_id);

                             

                            $user->itunes_autorenewal = 1;

                            $user->active_subscription = $subscriptionData['id'];

                            $user->last_transaction_id = $transaction_last_id;

                            $UserModel->save($user);

                            $this->set([

                                'msg'=> responseMsg(210),

                                'code'  => 200,

                                '_serialize' => ['code','msg']

                            ]);

                        

                        }else{

                            $this->set([

                                'msg'=> responseMsg(423),

                                'code'  => 423,

                                '_serialize' => ['code','msg']

                            ]);

                        }

                    }

                    else

                    {

                        $UserModel = TableRegistry::get('Users'); //use Cake\ORM\TableRegistry;

                        $userModel = $UserModel

                            ->find()

                            ->where(['id'=>$userId])

                            ->first();  

                        $userModel->itunes_autorenewal = 0;

                        $userModel->save(false);

                        $this->set([

                                'msg'=> responseMsg(424),

                                'code'  => 424,

                                '_serialize' => ['code','msg']

                        ]);

                    }

                }

            }



        }
    } 

	///androidSubscreption
	public function androidSubscreption() {

        $this->loadModel('Transactions');

        $request = $this->request;

        if($request->is('post')) { 

            $postData = $request->data;

            $requestStatus = 1;



            if( !isset($postData['orderId']) ) { $requestStatus = 0; }

            if( !isset($postData['productId']) ) { $requestStatus = 0; }

            if( !isset($postData['packageName']) ) { $requestStatus = 0; }

            if( !isset($postData['autoRenewing']) ) { $requestStatus = 0; }

            if( !isset($postData['purchaseToken']) ) { $requestStatus = 0; }

            if( !isset($postData['purchaseTime']) ) { $requestStatus = 0; }

            if( !isset($postData['offerId']) ) { $requestStatus = 0; }



            if($requestStatus==1) { 



                $user_id = $this->userid;





                /*$subTable = TableRegistry::get('Subscreption'); 

                $subData = $subTable->find()

                            ->where(['user_id'=>$user_id, 'status'=>1])

                            ->first();*/



                /*if(!empty($subData)) {



                    $Result['code'] = '217';

                    $Result['message'] = $this->ErrorMessages($Result['code']);

                    echo json_encode($Result); exit;



                } else {*/



                    require_once WWW_ROOT .'GoogleClientApi/Google_Client.php';

                    require_once WWW_ROOT .'GoogleClientApi/auth/Google_AssertionCredentials.php';



                $CLIENT_ID = '100377813809460893738';

                    //'110053402852490647256';

                $SERVICE_ACCOUNT_NAME = 'hopple-subscriptions@hopple.iam.gserviceaccount.com';
                $KEY_FILE = WWW_ROOT .'GoogleClientApi/eureka-5dcd5-8f60630a9edf.p12';

                $KEY_PW   = 'notasecret';



                $key = file_get_contents($KEY_FILE);

                $client = new \Google_Client();

                $client->setApplicationName("hopple");



                    $cred = new \Google_AssertionCredentials(

                                $SERVICE_ACCOUNT_NAME,

                                array('https://www.googleapis.com/auth/androidpublisher'),

                                $key);  



                    $client->setAssertionCredentials($cred);

                    $client->setClientId($CLIENT_ID);

                   

                    if ($client->getAuth()->isAccessTokenExpired()) {

                        try {

                            $client->getAuth()->refreshTokenWithAssertion($cred);

                        } catch (Exception $e) {

                        }

                    }

                    $token = json_decode($client->getAccessToken());

                        

                    $expireTime = "";

                    $amount = 0;

                    if( isset($token->access_token) && !empty($token->access_token) ) {

                        $appid = $postData['packageName'];

                        $productID = $postData['productId'];

                        $purchaseToken = $postData['purchaseToken'];



                        $ch = curl_init();

                        $VALIDATE_URL = "https://www.googleapis.com/androidpublisher/v2/applications/";

                        $VALIDATE_URL .= $appid."/purchases/subscriptions/".$productID."/tokens/".$purchaseToken;

                        $res = $token->access_token;



                        $ch = curl_init();

                        curl_setopt($ch,CURLOPT_URL,$VALIDATE_URL."?access_token=".$res);

                        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

                        $result = curl_exec($ch);

                        $result = json_decode($result, true);

                        prd($result);

                        

                        if(isset($result["startTimeMillis"])) {

                            $startTime = date('Y-m-d H:i:s', $result["startTimeMillis"]/1000. - date("Z"));

                            //$amount = $result["priceAmountMicros"]/1000000;

                        }

                        if(isset($result["expiryTimeMillis"])) {

                            $expireTime = date('Y-m-d H:i:s', $result["expiryTimeMillis"]/1000. - date("Z"));

                            $amount = $result["priceAmountMicros"]/1000000;

                        }

                    }

                    $date = new \DateTime();

                    $date->setTimestamp($postData['purchaseTime']/1000);

                    $dateStart = $date->format('Y-m-d H:i:s');



                   

                    

                   

                    $SubscriptionModel = TableRegistry::get('Subscriptions'); //use Cake\ORM\TableRegistry;

                    $subscriptionData = $SubscriptionModel

                    ->find()

                    ->select(['id','price'])

                    ->where(['android_product_id'=>$productID])

                    ->first();    



                    $this->loadModel('Users');



                    $Transactions = TableRegistry::get('Transactions'); 

                    $transaction = $this->Transactions->newEntity();

                    $transactionData['user_id'] = $this->userid;

                    $transactionData['subscription_id'] = $subscriptionData['id'];

                    $transactionData['total_amount'] = $amount;

                    $transactionData['created_at'] = $dateStart;

                    $transactionData['expired_at'] = $expireTime;

                    $transactionData['payment_status'] = '1';

                    $transactionData['itune_original_transaction_id'] = $postData['orderId'];

                    $transactionData['itunes_receipt'] = $result["orderId"];

                    $transactionData['orderId'] = $result["orderId"];

                    $transactionData['packageName'] = $postData['packageName'];

                    $transactionData['productId'] = $productID;

                    $transactionData['purchaseToken'] = $postData['purchaseToken'];

                    $transactionData['purchaseState'] = @$postData['purchaseState'];

                    $transaction = $this->Transactions->patchEntity($transaction, $transactionData);

                    $transaction['device_type'] = 0;

                    //prd($transaction);

                    if ($result = $this->Transactions->save($transaction)){

                        $transaction_last_id = $result['id'];

                        $UserModel = TableRegistry::get('Users'); //use Cake\ORM\TableRegistry;

                      

                        $user = $UserModel->get($user_id);

                         

                        $user->itunes_autorenewal = 1;

                        $user->active_subscription = $subscriptionData['id'];

                        $user->last_transaction_id = $transaction_last_id;

                        $UserModel->save($user);



                       /* $subcData = $subTable->newEntity();

                        $subcData->user_id = $user_id;

                        $subcData->offer_id = $postData['offerId'];

                        $subcData->order_id = $postData['orderId'];

                        $subcData->product_id = $postData['productId'];

                        $subcData->device_type = 1;

                        $subcData->purchase_token = $postData['purchaseToken'];

                        $subcData->package_name = $postData['packageName'];

                        $subcData->auto_renewing = $postData['autoRenewing'];

                        $subcData->amount = $amount;

                        $subcData->start_date = $dateStart;

                        $subcData->status = 1;

                        $subcData->created = date('Y-m-d H:i:s');

                        if($expireTime != "" && !empty($expireTime)) {

                            $subcData->end_date = $expireTime; 

                        }

                        

                        $saveData = $subTable->save($subcData);*/

                    $this->set([

                    'msg'=> responseMsg(210),

                    'data' => '',

                    'code'  => 200,

                    '_serialize' => ['code','msg','data']

                 ]);

                    



                }else{

                    $this->set([

                        'msg'=> responseMsg(400),

                        'code'  => 400,

                        '_serialize' => ['code','msg']

                    ]);

                }

            } else {



                 $this->set([

                        'msg'=> responseMsg(400),

                        'code'  => 400,

                        '_serialize' => ['code','msg']

                    ]);

                /*$Result['code'] = '201';

                $Result['message'] = $this->ErrorMessages($Result['code']);*/

            }

        } else {

             $this->set([

                        'msg'=> responseMsg(400),

                        'code'  => 400,

                        '_serialize' => ['code','msg']

                    ]);

            /*$Result['code'] = '202';

            $Result['message'] = $this->ErrorMessages($Result['code']);*/

        }
    }

	//cronJobForSubscreption 
	public function cronJobForSubscreption() { //use for  cron
   

        $Result['code'] = '200';

        $request = $this->request;

            

        $requestStatus = 1;

        if($requestStatus==1) { 

            $currentDate = date('Y-m-d H:i:s');

            $transactionsTable = TableRegistry::get('Transactions');

            $userTable = TableRegistry::get('Users');  

            $subData = $transactionsTable->find()

                        ->where(['expired_at < '=>$currentDate])

                        ->ToArray();



            if(!empty($subData) && count($subData)) {

                //---- get auth token ---------------

                require_once WWW_ROOT .'GoogleClientApi/Google_Client.php';

                require_once WWW_ROOT .'GoogleClientApi/auth/Google_AssertionCredentials.php';

                // OLD CREDENTIAL

                // $CLIENT_ID = '100577647558607823571';

                // $SERVICE_ACCOUNT_NAME = 'salonchservice@api-7166865876201490568-92003.iam.gserviceaccount.com';

                // $KEY_FILE = WWW_ROOT .'GoogleClientApi/Google Play Android Developer-1e24b87cdccd.p12';

                // $KEY_PW   = 'notasecret';

                // $key = file_get_contents($KEY_FILE);



                $CLIENT_ID = '115926532541377965006';

                // 11064478720505982158

                $SERVICE_ACCOUNT_NAME = 'meprosh@api-8962663481780642788-455544.iam.gserviceaccount.com';

                $KEY_FILE = WWW_ROOT .'GoogleClientApi/Google Play Android Developer-1e24b87cdccd.p12';

                $KEY_PW   = 'notasecret';





                $key = file_get_contents($KEY_FILE);



                $client = new \Google_Client();

                $client->setApplicationName("meprosh");





                $cred = new \Google_AssertionCredentials(

                            $SERVICE_ACCOUNT_NAME,

                            array('https://www.googleapis.com/auth/androidpublisher'),

                            $key);  



                $client->setAssertionCredentials($cred);

                $client->setClientId($CLIENT_ID);

                

                if ($client->getAuth()->isAccessTokenExpired()) {

                    try {

                        $client->getAuth()->refreshTokenWithAssertion($cred);

                    } catch (Exception $e) {

                    }

                }

                $token = json_decode($client->getAccessToken());





                //---- cron job work  ---------------------



                foreach ($subData as $key => $val) {

                    if( $val->device_type==0 ) {  // android



                        $expireTime = "";

                        $amount = 0;

                        if( isset($token->access_token) && !empty($token->access_token) ) {



                            $appid = $val->packageName;

                            $productID = $val->productId;

                            $purchaseToken = $val->purchaseToken;



                            $VALIDATE_URL = "https://www.googleapis.com/androidpublisher/v2/applications/";

                            $VALIDATE_URL .= $appid."/purchases/subscriptions/".$productID."/tokens/".$purchaseToken;

                            $res = $token->access_token;



                            $ch = curl_init();

                            curl_setopt($ch,CURLOPT_URL,$VALIDATE_URL."?access_token=".$res);

                            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

                            $result = curl_exec($ch);

                            $result = json_decode($result, true);

                            if(isset($result["expiryTimeMillis"])) {

                                $expireTime = date('Y-m-d H:i:s', $result["expiryTimeMillis"]/1000. - date("Z"));

                                $amount = $result["priceAmountMicros"]/1000000;

                            

                                if($expireTime > date('Y-m-d H:i:s')) {

                                    

                                    $query = $transactionsTable->query();

                                    $result = $query->update()

                                            ->set(['expired_at' => $expireTime , 'status' => 1])

                                            ->where(['id' => $val->user_id])

                                            ->execute();

                                            

                                    $salonQuery = $userTable->query();

                                    $salonQuery->update()

                                                    ->set(['status' => 1])

                                                    ->where(['user_id' => $val->user_id, 'status' => 0])

                                                    ->execute();

                                } else {

                                    $query = $transactionsTable->query();

                                    $result = $query->update()

                                            ->set(['status' => 2])

                                            ->where(['id' => $val->id])

                                            ->execute();

                                            

                                    $salonQuery = $userTable->query();

                                    $salonQuery->update()

                                                    ->set(['status' => 0])

                                                    ->where(['user_id' => $val->user_id, 'status' => 1])

                                                    ->execute();

                                } 



                            }

                        }

                    } else if( $val->device_type==1 ) {   // iphone

                        $itunesReceipt = $val->purchase_token;  

                        //$password = "58c72878cd56401a9c71927679fd9ee5";        

                        $password = "b980bea652414354add919ac7f654b9c";        

                        $receiptData = '{"receipt-data":"'.$itunesReceipt.'","password":"'. $password .'"}';

                        $endpoint = 'https://sandbox.itunes.apple.com/verifyReceipt';

                        // $endpoint = 'https://buy.itunes.apple.com/verifyReceipt';    



                        $ch = curl_init($endpoint);

                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                        curl_setopt($ch, CURLOPT_POST, true);

                        curl_setopt($ch, CURLOPT_POSTFIELDS, $receiptData);

                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

                        $response = curl_exec($ch);

                        $errno = curl_errno($ch);



                        if($errno==0) {



                            $receiptInfo = json_decode($response,true);

                            

                            if( isset($receiptInfo['latest_receipt_info']) && !empty($receiptInfo['latest_receipt_info']) ) {



                                $lastData = end($receiptInfo['latest_receipt_info']);

                                

                                $expireTime = date('Y-m-d H:i:s',strtotime($lastData['expires_date']));



                                if($expireTime > date('Y-m-d H:i:s')) {

                                    

                                    $query = $transactionsTable->query();

                                    $result = $query->update()

                                            ->set(['expired_at' => $expireTime , 'status' => 1])

                                            ->where(['id' => $val->user_id])

                                            ->execute();

                                            

                                    $salonQuery = $userTable->query();

                                    $salonQuery->update()

                                                    ->set(['active_subscription' => 1])

                                                    ->where(['id' => $val->user_id, 'active_subscription' => 0])

                                                    ->execute();

                                } else {

                                    $query = $transactionsTable->query();

                                    $result = $query->update()

                                            ->set(['status' => 2])

                                            ->where(['id' => $val->id])

                                            ->execute();

                                            

                                    $salonQuery = $userTable->query();

                                    $salonQuery->update()

                                                    ->set(['active_subscription' => 0])

                                                    ->where(['id' => $val->user_id, 'active_subscription' => 1])

                                                    ->execute();

                                } 
                            }
                        }       
                    }
                }

            } 
        }   

        exit;   
    }

	public function checkchip($data){
		$checkunique = Chip::where('unique_id', $data['unique_id'])->where('u_id', $data['id'])->first();
		if(!empty($checkunique)){
			$rescod = 1;
		
    	}else{

        	$rescod = 0;

    	}
		return $rescod;
	}


	public function chip($data){

		$chip = new Chip();
		$chip->chip_name = $data['chip_name'];
		$chip->unique_id = $data['unique_id'];
		$chip->u_id 	=  $data['id'];
		$chip->save();
		
		return $chip;
	}

	public function chip_data_list($data,$arg){
		//$checkunique = ChipData::where('unique_id', $data['unique_id'])->where('u_id', $arg['id'])->first();
		$chip = ChipData::where('unique_id',$data['unique_id'])->paginate(10,['*'],'page_no');
		//print_r($chip); exit;
		//$chip = ChipData::where('unique_id',$data['unique_id'])->paginate(20,['*'],'page_no');
		$chip_array = array();
		$Chip_list = array();

		foreach($chip as $list){
			$chip_array['c_id'] 			=  	@$list->c_id ? $list->c_id : '';
			$chip_array['u_id'] 			=  	@$list->u_id ? $list->u_id : '';
			$chip_array['unique_id'] 		=  	@$list->unique_id ? $list->unique_id : '';
			$chip_array['data_date_time'] 	=  	@$list->data_date_time ? $list->data_date_time : '';
			$chip_array['cycle_count'] 		=  	@$list->cycle_count ? $list->cycle_count : '';
			$chip_array['status'] 			=  	@$list->status ? $list->status : '';
			
			array_push($Chip_list,$chip_array);
		}
		//echo '<pre>'; print_r($chip); exit;
		
		return $chip;
	}

	public function logout($data){

		$rescod = "";
	
		if ($data['id']) {
        
			$user =  User::findorfail($data['id']);
			$user->device_id = "";
			$user->device_type = 2;
			$user->save();

			$user = Auth::user()->token();
        	$user->revoke();
        	$rescod = 642;

    	}else{

        	$rescod = 461;

    	}
		return $rescod;
	}


} 

