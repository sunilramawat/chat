<?php

namespace App\Http\Controllers\Repository;
use App\User;
use App\Models\Photo;
use App\Models\Post;
use App\Models\Room;
use App\Models\Partner;
use App\Models\Like;
use App\Models\Favourite;
use App\Models\PendingMatches;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Models\Gender;
use App\Models\Faq;
use App\Models\Answer;
use App\Models\UserAnswer;
use App\Models\ReportList;
use App\Models\Religion;
use App\Models\Race;
use App\Models\PartnerType;
use App\Models\Region;
use App\Models\Subscription;
use App\Models\Transaction;
use Twilio\Rest\Client;
use App\Http\Controllers\Utility\CustomVerfication;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Utility\SendEmails;
use Carbon\Carbon;	
use Virgil\Crypto\VirgilCrypto;
use Virgil\Sdk\Web\Authorization\JwtGenerator;
use Auth;
use DB;
Class UserRepository extends User{

	public function check_user($data){
		if(isset($data['unique_id'])){
			$user_list = User::Where('unique_id',@$data['unique_id'])
				->where('user_status','!=',0)->first();
		}

		return $user_list;				
	}

	public function check_unactive_user($data){
		
		$user_list = User::where('unique_id',@$data['unique_id'])->first();
				
		return $user_list;				
	}

	public function register($data){

		$CustomVerfication = new CustomVerfication();
		$SendEmail = new SendEmails();
		$code = $data['password'];
		$rescod  = "";
		
		
		$create_user = new User();
		$create_user->unique_id = @$data['unique_id']?@$data['unique_id']:'';
		$create_user->user_type = @$data['user_type']?$data['user_type']:1;
		$create_user->username = @$data['username'];
		$create_user->added_date = date ( 'Y-m-d H:i:s' );
		$create_user->is_approved = '1';
		$create_user->user_status = '1';
		$create_user->password = hash::make($code);
		$create_user->is_active_profile = @$data['is_terms_accepted']?$data['is_terms_accepted']:0;
		//$create_user->is_email_verified = '0';
		//$create_user->is_phone_verified = '0';
        $create_user->last_login= date ( 'Y-m-d H:i:s' );
        $create_user->token_id = mt_rand(); 
		$create_user->created_at = date ( 'Y-m-d H:i:s' );
		$create_user->updated_at = date ( 'Y-m-d H:i:s' );
		
		//$create_user->email 	= @$data['email'] ? $data['email']: '';
		//$create_user->password 	= hash::make(@$data['password']) ? hash::make(@$data['password']): '';
	
		$create_user->device_id = @$data['device_id']?$data['device_id']:'';
		$create_user->device_type = @$data['device_type']?$data['device_type']:0;
		$create_user->fcm_token = @$data['fcm_token']?$data['fcm_token']:'';
		
		$create_user->save();
		//echo '<pre>'; print_r($create_user); exit;
		$userid = $create_user->id; 
		$message = "Your chat verification Code is ". $code;
		
		if(isset($data['phone'])){
			$phone = $data['country_code'].''.$data['phone'];
            $verify_type = 1;
            $create_user->activation_code = $code;
            $user = User::find($userid);
            /*$sidname = getenv("CHAT_ENV").$userid; 
            if(empty($user['sid'])){
            	$chat_sid_create = $CustomVerfication->chat_user($sidname);
            	$user = User::find($userid);
				$user->sid = $chat_sid_create ;
				$user->save();
            }*/
            
			//$verify = $CustomVerfication->phoneVerification($message,$phone);
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

            //$SendEmail->sendUserRegisterEmail($email,$name,$code,$data['forgot_type'],$userid);
        	
        }
        $create_user->password = $code;
		return $create_user;
	}


	public function social_register($data){
		if(@$data['facebook_id']){
			$code = @$data['facebook_id'];
		}elseif(@$data['google_id']){
			$code = @$data['google_id'];
		}elseif(@$data['apple_id']){
			$code = @$data['apple_id'];
		}



		$CustomVerfication = new CustomVerfication();
		$SendEmail = new SendEmails();
		$rescod  = "";
		
		if(!isset($data['id'])){
			$create_user = new User();
		}else{
			$create_user = User::find($data['id']);
		}
		//$create_user->email 	= @$data['email'] ? $data['email']: '';
		//$create_user->password 	= hash::make(@$data['password']) ? hash::make(@$data['password']): '';


		$create_user->username = @$data['username'];
		$create_user->bio = @$data['bio'];
		$create_user->website = @$data['website'];
		$create_user->fb_link = @$data['fb_link'];
		$create_user->linkedin_link = @$data['linkedin_link'];
		$create_user->twitter_link = @$data['twitter_link'];
		$create_user->Instagram = @$data['Instagram'];
		$create_user->rank = @$data['rank']?$data['rank']:0;
		$create_user->followers = @$data['followers']?@$data['followers']:0;
		$create_user->followings = @$data['followings']?$data['followings']:0;
		$create_user->posts = @$data['posts']?$data['posts']:0;
		$create_user->user_type =  @$data['user_type']?$data['user_type']:1;
		
		$create_user->facebook_id = @$data['facebook_id'];
		$create_user->google_id = @$data['google_id'];
		$create_user->apple_id = @$data['apple_id'];
		$create_user->first_name = @$data['first_name'];
		$create_user->last_name = @$data['last_name'];
		$create_user->phone = @$data['phone'];
		$create_user->added_date = date ( 'Y-m-d H:i:s' );
		$create_user->user_status = 1;
		$create_user->is_approved = '0';
		$create_user->activation_code = $code;
		$create_user->password = hash::make($code);
		$create_user->is_email_verified = '0';
		$create_user->is_phone_verified = '0';
        $create_user->last_login= date ( 'Y-m-d H:i:s' );
        $create_user->token_id = mt_rand(); 
		$create_user->created_at = date ( 'Y-m-d H:i:s' );
		$create_user->updated_at = date ( 'Y-m-d H:i:s' );
		
		$create_user->save();
		$userid = $create_user->id; 
		
		
        return $userid;
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
					->where('user_status','!=',2)
					->first();
					//print_r($query); exit;
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
		        if($find == 1){
		        	$user->is_phone_verified  = 1;
		        	$user->user_status  = 1;
		        	
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
		if(!empty($data['unique_id']))
		{
			$query = User::where('unique_id', $data['unique_id'])->first();
			
		}
		

		return $query;
	}

	public function  clear_user_token($data){

		$clear_token = User::where('device_id',$data)->first();
		$clear_token->device_id = "";
		$clear_token->save();  
	}

	public function get_user_detail($data)
	{
		//echo '<pre>';print_r($data); exit;
		$token_id =  mt_rand();
		$query = User::find($data['id']);
		$query->token_id    = $token_id;
        $query->last_login  = date ( 'Y-m-d H:i:s' );
    	$query->device_id   = $data['device_id'];
        $query->device_type = $data['device_type'];
        $query->fcm_token = @$data['fcm_token'];
        //$query->chat_token = @$data['chat_token'];

        if(@$data['first_name'] != ''){
        	$query->first_name  = @$data['first_name'];
    	}
    	if(@$data['last_name'] != ''){
       	 $query->last_name 	= @$data['last_name'];
    	}
    	if(@$data['photo'] != ''){
      		$query->photo 		= @$data['photo'];
      	}
      
        $query->save();

    	

        $userdata['username'] = @$query['username']?$query['username']:'';
		
        $userdata['unique_id']= $data['unique_id'];
		$userdata['userid'] 		 = $query['id'];
       	$userdata['last_login']  = date ( 'Y-m-d H:i:s' );
        $userdata['device_id'] 	 = $query['device_id']?strval($query['device_id']):'';
        $userdata['device_type'] = $query['device_type']? intval($query['device_type']):'';
        $userdata['device_token']= $query['device_token']?$query['device_token']:'';
        $userdata['fcm_token']= $query['fcm_token']?$query['fcm_token']:'';
        $userdata['is_subscribe']= $query['is_subscribe']?$query['is_subscribe']:0;
        $userdata['active_subscription']= $query['active_subscription']?$query['active_subscription']:0;
        $userdata['itunes_autorenewal']= $query['itunes_autorenewal']?$query['itunes_autorenewal']:0;
        $userdata['access_token']= @$data['token'];
        $userdata['user_status'] = $query['user_status']?$query['user_status']:'';
        $userdata['is_terms_accepted']= $query['is_active_profile']?$query['is_active_profile']:0;
        $userdata['chat_token']= $data['chat_token'];
       
      
	        


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

			$message = "Your Chat verification code is ". $code;

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
		
		
		$userdata['id'] = $user->id;
        $userdata['username'] = @$user['username']?$user['username']:'';
       
       	$userdata['last_login']  = date ( 'Y-m-d H:i:s' );
        $userdata['device_id'] 	 = $user['device_id']?$user['device_id']:'';
        $userdata['device_type'] = $user['device_type']?intval($user['device_type']):'';
        $userdata['device_token']= $user['device_token']?$user['device_token']:'';
        //$userdata['access_token']= $user['token'];
        $userdata['user_status'] = $user['user_status']?$user['user_status']:'';
        $userdata['is_terms_accepted']= $user['is_active_profile']?$user['is_active_profile']:0;


       	
		return $userdata;

	}
	public function getotheruserById($data){
		$userdata['id'] = $user->id;
        $userdata['username'] = @$user['username']?$user['username']:'';
        $userdata['phone'] = @$user['phone']?$user['phone']:'';
        $userdata['country_code'] = @$user['country_code']?$user['country_code']:'';
        $userdata['photo'] = @$user['photo']?$user['photo']:'';
		$userdata['bio'] = @$user['bio']?$user['bio']:'';
		$userdata['website'] = @$user['website']?$user['website']:'';
		$userdata['fb_link'] = @$user['fb_link']?$user['fb_link']:'';
		$userdata['linkedin_link'] = @$user['linkedin_link']?$user['linkedin_link']:'';
		$userdata['twitter_link'] = @$user['twitter_link']?$user['twitter_link']:'';
		$userdata['instagram'] = @$user['instagram']?$user['instagram']:'';
		$userdata['rank'] = @$user['rank']?$user['rank']:0;
		$userdata['followers'] = @$user['followers']?@$user['followers']:0;
		$userdata['followings'] = @$user['followings']?$user['followings']:0;
		$userdata['posts'] = @$user['posts']?$user['posts']:0;
		$userdata['user_type'] =  @$user['user_type']?$user['user_type']:1;

       	$userdata['last_login']  = date ( 'Y-m-d H:i:s' );
        $userdata['device_id'] 	 = $user['device_id']?$user['device_id']:'';
        $userdata['device_type'] = $user['device_type']? intval($user['device_type']):'';
        $userdata['first_name']  = $user['first_name']?$user['first_name']:'';
        $userdata['last_name'] 	 = $user['last_name']?$user['last_name']:'';
        $userdata['device_token']= $user['device_token']?$user['device_token']:'';
        //$userdata['access_token']= $user['token'];
        $userdata['user_status'] = $user['user_status']?$user['user_status']:'';
        $userdata['is_active_profile']= $user['is_active_profile']?$user['is_active_profile']:0;

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
		$is_verify  = 0;
		if($query == 0){
			
		
			$user->username 	= 	@$data['username'] ? $data['username'] : $user->username;
			$user->is_active_profile 	= 	@$data['is_terms_accepted'] ? $data['is_terms_accepted'] : $user->is_terms_accepted;
			$user->photo 	= 	@$data['photo'] ? $data['photo'] : $user->photo;
			//print_r($user); exit;
			$user->save();

			$userData['code'] = 200;
			$userData['id'] = $user->id;
			$userData['unique_id'] = $user->unique_id;
	        //$userData['user_type'] = $user->user_type ? $user->user_type : '';
	        //$userData['email'] = $user->email ? $user->email : '';
	        $userData['photo'] = $user->photo ? $user->photo : '';
	        $userData['device_id'] = $user->device_id ? $user->device_id :'';
	        $userData['device_type'] = $user->device_type ? intval($user->device_type) : '';
	        $userData['username'] = $user->username ? $user->username : '';
			$userData['is_terms_accepted'] 			= 	 $user->is_active_profile?$user->is_active_profile : 0 ;
			$userData['last_login']  = date ( 'Y-m-d H:i:s' );
		    $userData['device_token']= $user->device_token ? $user->device_token : '';
	        //$userdata['access_token']= $user['token'];
	        $userData['user_status'] = $user->user_status ? $user->user_status : '';
	        
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


	public function create_post($data){
		//print_r($data); exit;
		if($data['description'] !=  ''){
			
			$post->u_id = @$data['userid'] ? $data['userid']: '';
			$post->post_type = @$data['post_type'] ? $data['post_type']: 0;
			if(@$data['post_type'] == 3){
				$post->poll_one = @$data['poll_one'] ? $data['poll_one']: '';
				$post->poll_two = @$data['poll_two'] ? $data['poll_two']: '';
				$post->poll_three = @$data['poll_three'] ? $data['poll_three']: '';
				$post->poll_four = @$data['poll_four'] ? $data['poll_four']: '';
			}
			if(@$data['post_type'] == 2){
				$post->stock_name  = @$data['stock_name'] ? $data['stock_name']: '';
				$post->stock_target_price  = @$data['stock_target_price'] ? $data['stock_target_price']: '';
				$post->time_left   = @$data['time_left'] ? $data['time_left']: '';
				$post->term   = @$data['term'] ? $data['term']: '';
				$post->trend   = @$data['trend'] ? $data['trend']: '';
				$post->recommendation   = @$data['recommendation'] ? $data['recommendation']: '';

			}
			$post->posted_time = date ( 'Y-m-d H:i:s' );
			$post->description = @$data['description'] ? $data['description']: '';
			$post->created_at =  date ( 'Y-m-d H:i:s' );
			$post->updated_at =  date ( 'Y-m-d H:i:s' );
			if(@$data['imgUrl'] !=  ''){
				$post->imgUrl = @$data['imgUrl'];
			}
			print_r($post);
			$post->save();
			$lastid = $post->id;
			$userData['code'] = 200;
			$userData['p_id'] = @$lastid;
			$userData['imgUrl'] = @$post->imgUrl;
			$userData['post_type'] = @$post->post_type;
			if(@$data['post_type'] == 3){
				$userData['poll_one'] = @$post->poll_one;
				$userData['poll_two'] = @$post->poll_two;
				if(@$post->poll_three != ''){
					$userData['poll_three'] = @$post->poll_three;
				}
				if(@@$post->poll_four != ''){
					$userData['poll_four'] = @$post->poll_four;
				}
			}
			$userData['created_at'] = @$post->created_at;
			$userData['updated_at'] = @$post->updated_at;
			$userData['u_id'] = @$post->u_id;
	

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
		$sender_name = $user['first_name'];
		$message =  $sender_name." your email account has been activated.";
		$data['userid'] = $userId;
		$data['name'] = $user['first_name'];
		$data['message'] = $message;
		$data['n_type'] = 1;
		$notify = array ();
		$notify['receiver_id'] = $userId;
		$notify['relData'] = $data;
		$notify['message'] = $message;
		//print_r($notify); exit;
		$test =  $this->sendPushNotification($notify); 
		return $user;
	}

	public function update_password($data){
		//print_r($data); exit;	
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


	
	public function create_room($arg,$userId){
		//print_r($userId); exit;
		$checkroom = Room::where('sender_id', $userId)->where('receiver_id', $arg['receiver_id'])->first();


		if(empty($checkroom)){
			$room = new Room();
			$room->sender_id = $userId;
			$room->receiver_id = $arg['receiver_id'];
			$room->status = 1;
			$room->chatroom = $userId.'-'.$arg['receiver_id'].'-room';
			//echo '<pre>'; print_r($like); exit;
			$room->save();

			$room1 = new Room();
			$room1->sender_id = $arg['receiver_id'];
			$room1->receiver_id =  $userId;
			$room1->status = 0;
			$room1->chatroom = $userId.'-'.$arg['receiver_id'].'-room';
			// pending notification code
			$checkroom['chatroom'] = $room1->chatroom;
			//echo '<pre>'; print_r($like); exit;
			$room1->save();
			$retult = 1;
			
			
			$n_type = 2;
			//$this->notification_save($all_invited_uservalue,$notify,$message,$sender_name,$n_type,$receiver_name,$fcm_token);
		}else{
			//print_r($checkroom); exit;
			$retult = 1;
			if ($arg['status'] == 2) { //Accept 
				if($checkroom['status'] == 0){
					Room::where('sender_id', $userId)
						->where('receiver_id', $arg['receiver_id'])
		       			->update([
		           			'status' => $arg['status']
	        			]);

	        		Room::where('receiver_id', $userId)
					->where('sender_id', $arg['receiver_id'])
		       		->update([
		           			'status' => $arg['status']
	    			]);	
		       	}else{//  request already sended 
		       		$retult = 2;
		       	}
		
				
			}else if ($arg['status'] == 3) { //Declined/Delete
					 		
				if($checkroom['status'] == 0){
					$deletelike =  Room::where('sender_id', $userId)
						->where('receiver_id', $arg['receiver_id'])->delete();	 

					$deletelike =  Room::where('receiver_id', $userId)
					->where('sender_id', $arg['receiver_id'])->delete();
		       	}else{
		       		$retult = 0;
		       	}
				
			}else if ($arg['status'] == 4) { //Block
				//if($checkroom['status'] == 0){
					Room::where('sender_id', $userId)
						->where('receiver_id', $arg['receiver_id'])
		       			->update([
		           			'status' => 5
	        			]);
		       		if ($arg['status'] == 4) {//block 
		        		Room::where('receiver_id', $userId)
						->where('sender_id', $arg['receiver_id'])
			       		->update([
			           			'status' => $arg['status']
		    			]);	
			       	}
			    /*}else{
		       		$retult = 1;
		       	}*/
				
				
			}else{ // Unblock Section 
				//if($checkroom['status'] == 5){
					$deletelike =  Room::where('sender_id', $userId)
							->where('receiver_id', $arg['receiver_id'])->delete();	 

					$deletelike =  Room::where('receiver_id', $userId)
						->where('sender_id', $arg['receiver_id'])->delete();
					$retult = 1;
				

			}
			
			
			
		}	
		if ($arg['status'] == 1 || $arg['status'] == 2 ) {
			$user = User::find($userId);
			
			$sender_name = $user['username'];

			$receiver_detail = User::find($arg['receiver_id']);
			$receiver_name = $receiver_detail['username'];
			$fcm_token = $receiver_detail['fcm_token'];
			$checkroom = Room::where('sender_id', $userId)->where('receiver_id', $arg['receiver_id'])->first();

			$message =  $sender_name." has invited to you join his room";
			$data['userid'] = $userId;
			$data['chatroom'] = $checkroom['chatroom'];
			$data['name'] = $user['username'];
			$data['message'] = $message;
			$data['n_type'] = 2;
			$notify = array ();
			$notify['receiver_id'] = $arg['receiver_id'];
			$notify['relData'] = $data;
			$notify['message'] = $message;
			//print_r($notify); exit;
			$test =  $this->sendPushNotification($notify); 
		}	
		
		/*$like_count= Like::where('post_id', $arg['post_id'])->count();
		
		$postData 	=	Post::where('id', $arg['post_id'])->first();
		$postData->like_count 	= 	$like_count ? $like_count : 0;
		//print_r($postData); exit;
		$postData->save();*/
		return $retult;
	}

	public function userNotify($arg,$userId){
		//print_r($userId); exit;
			
		$user = User::find($userId);
		$sender_name = $user['username'];

		$receiver_detail = User::find($arg['receiver_id']);
		$receiver_name = $receiver_detail['username'];
		$fcm_token = $receiver_detail['fcm_token'];
	
		$message =  $sender_name." has sent you message.";
		$data['userid'] = $userId;
		$data['name'] = $user['username'];
		$data['message'] = $message;
		$data['chat_room'] = $arg['chat_room'];
		$data['n_type'] = 1;
		$notify = array ();
		$notify['chat_room'] = $arg['chat_room'];
		$notify['receiver_id'] = $arg['receiver_id'];
		$notify['relData'] = $data;
		$notify['message'] = $message;
		//print_r($notify); exit;
		$test =  $this->sendPushNotification($notify); 
		$n_type = 1;
			//$this->notification_save($all_invited_uservalue,$notify,$message,$sender_name,$n_type,$receiver_name,$fcm_token);
		
		return 1;
	}

	public function favourite($arg,$userId){
		$checklike = favourite::where('f_user_id', $userId)->where('post_id', $arg['post_id'])->first();
		if(empty($checklike)){
			$favourite = new favourite();
			$favourite->f_user_id = $userId;
			$favourite->post_id = $arg['post_id'];
			//echo '<pre>'; print_r($like); exit;
			$favourite->save();
			$retult = 1;
		}else{
			$deletelike =  favourite::where('f_id',$checklike['f_id'])->delete();	
			$retult = 0;
		}		
		
		$favourite_count= favourite::where('post_id', $arg['post_id'])->count();
		
		$postData 	=	Post::where('id', $arg['post_id'])->first();
		$postData->favourite_count 	= 	$favourite_count ? $favourite_count : 0;
		//print_r($postData); exit;
		$postData->save();
		return $retult;
	}



	public function home_list($data,$arg){
		$model 		= "App\Models\User";
		//$post_type = @$data['post_type'];
		$arg['id'];
		$query = $model::query();
			

			/*if(isset($partner_type)){
				//echo $selected_date ; exit;
				$query =$query->where('post_type','=',@$post_type);
			}*/

				
		$query = $query->select('users.id as userid','users.unique_id as unique_id','users.username as username','rooms.*')
				->where('is_active_profile',1)
				->where('rooms.sender_id',$arg['id'])
				->where('rooms.status','<=',2)
				->leftjoin('rooms','users.id','rooms.receiver_id')
				->orderBy('rooms.r_id', 'DESC')
				->paginate(10,['*'],'page_no');

		$query->total_count = $model::where('users.id',$arg['id'])
				->count();
		$partner = $query;
			//print_r($partner); exit;
		
		
		return $partner;
	}


	public function block_list($data,$arg){
		$model 		= "App\Models\User";
		//$post_type = @$data['post_type'];
		$arg['id'];
		$query = $model::query();
			

			/*if(isset($partner_type)){
				//echo $selected_date ; exit;
				$query =$query->where('post_type','=',@$post_type);
			}*/

				
		$query = $query->select('users.id as userid','users.unique_id as unique_id','users.username as username','rooms.*')
				->where('is_active_profile',1)
				->where('rooms.sender_id',$arg['id'])
				->where('rooms.status',5)
				->leftjoin('rooms','users.id','rooms.receiver_id')
				->orderBy('rooms.r_id', 'DESC')
				->paginate(10,['*'],'page_no');

		$query->total_count = $model::where('users.id',$arg['id'])
				->count();
		$partner = $query;
			//print_r($partner); exit;
		
		
		return $partner;
	}

	public function partner_detail($data){
		$partner = Partner::where('id', $data)
		->leftjoin('categories','partners.category','categories.c_id')
		->leftjoin('sub_categories','partners.sub_category','sub_categories.sc_c_id')
		->first();
		//print_r($partner); exit;

		$userData['id'] = $partner['id'];
		$userData['photo'] = @$partner['photo']? URL('/public/images/'.$partner['photo']):'';
        $userData['name'] = $partner['name'] ? $partner['name'] : '';
        $userData['desc'] = $partner['desc'] ? $partner['desc'] : '';
        $userData['type_text'] = $partner['type_text'] ? $partner['type_text'] : '';
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

	public function check_username($data,$userId){
		$checkEmail = User::where('username', $data['username'])->first();
		////////////
		//print_r($userId); exit;
		//print_r($checkEmail); exit;
		$userData =array();
		$userData['is_username_available'] = 0;	
		if(!isset($checkEmail['id'])){
			$userData['is_username_available'] = 0;
		}else{
			
	   		$userData['is_username_available'] = 1;
	   	}

		return $userData;
	}

	public function check_unique_id($data){
		$checkEmail = User::where(DB::raw('BINARY `unique_id`'), $data['unique_id'])->first();
		////////////
		//print_r($userId); exit;
		//print_r($checkEmail); exit;
		$userData =array();
		$userData['is_unique_id_available'] = 0;	
		if(!isset($checkEmail['id'])){
			$userData['is_unique_id_available'] = 1;
		}else{
			
	   		$userData['is_unique_id_available'] = 0;
	   	}

		return $userData;
	}


	public function update_device($data,$userId){
		$getuniqueId = User::where('id', $userId)->first();
		
		////////////
		//print_r($userId); exit;
		//print_r($checkEmail['id']); exit;
	    if(@$data['update_virgil'] == 1){
			// App Key (you got this Key at Virgil Dashboard)
			$privateKeyStr = "MC4CAQAwBQYDK2VwBCIEIJ5mi96oZJ8v13CIgLiz1QmwTYSqrKrWOv1V7CxY5uKw";
			$appKeyData = base64_decode($privateKeyStr);

			// VirgilCrypto imports a private key into a necessary format
			$crypto = new VirgilCrypto();
			$privateKey = $crypto->importPrivateKey($appKeyData);

			// use your App Credentials you got at Virgil Dashboard:
			$appId = "1794e250dc3b490faa06d0b2b70b2ccd"; // App ID
			$appKeyId = "d74992324ba97221cf633675e66f8344";              // App Key ID
			$ttl = 86400; // 24 hour (JWT's lifetime)

			// setup JWT generator with necessary parameters:
			$jwtGenerator = new JwtGenerator($privateKey->getPrivateKey(), $appKeyId, $crypto, $appId, $ttl);

			// generate JWT for a user
			// remember that you must provide each user with his unique JWT
			// each JWT contains unique user's identity (in this case - Alice)
			// identity can be any value: name, email, some id etc.
			$identity = $getuniqueId['unique_id'];
			 $token = $jwtGenerator->generateToken($identity);
			
			// as result you get users JWT, it looks like this: "eyJraWQiOiI3MGI0NDdlMzIxZjNhMGZkIiwidHlwIjoiSldUIiwiYWxnIjoiVkVEUzUxMiIsImN0eSI6InZpcmdpbC1qd3Q7dj0xIn0.eyJleHAiOjE1MTg2OTg5MTcsImlzcyI6InZpcmdpbC1iZTAwZTEwZTRlMWY0YmY1OGY5YjRkYzg1ZDc5Yzc3YSIsInN1YiI6ImlkZW50aXR5LUFsaWNlIiwiaWF0IjoxNTE4NjEyNTE3fQ.MFEwDQYJYIZIAWUDBAIDBQAEQP4Yo3yjmt8WWJ5mqs3Yrqc_VzG6nBtrW2KIjP-kxiIJL_7Wv0pqty7PDbDoGhkX8CJa6UOdyn3rBWRvMK7p7Ak"
			// you can provide users with JWT at registration or authorization steps
			// Send a JWT to client-side
			$token->__toString();

			$userData =array();
			$userData['chat_token'] = $token->__toString(); 
		}else{
			$checkEmail = User::where('id', $userId)
	       		->update([
	           'device_token' => @$data['device_token'] ,'device_type' => @$data['device_type'], 'fcm_token' => @$data['fcm_token'],
	           'device_id' => @$data['device_id']
        ]);	
		}
		////////////////////////////////////////////////////
		$userData['code'] = 200;
		$userData['device_token'] = @$data['device_token'];
		
		return $userData;
	}

	public function chat_user_sid_update($sid,$userId){
		$checkEmail = User::where('id', $userId)
	       		->update([
	           'sid' => @$sid 
        ]);	
		////////////
		//print_r($userId); exit;
		//print_r($checkEmail['id']); exit;
		$userData =array();
		$userData['code'] = 200;
		$userData['sid'] = $sid;
		
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
		//echo $receiver_id; exit;
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
		//echo '<pre>';print_r($check_user); exit;

		if (empty($receiver_id)) {
			exit;
		}
		if ($check_user['device_type'] == 0) { //ios
			$check_user['device_id'] = trim($check_user['device_id']);
			if($check_user['device_id'] != ''){
				if(!empty($message)){
					//$this->iphone_push($check_user['device_token'], $message,  $data, $badge);
					//echo 'yesy';
					//print_r($data); exit;
					//$this->sendApns_P8($check_user['fcm_token'], $message,  $data, 0);
					$this->ios_fcm_push($check_user['fcm_token'], $message,  $data, $badge);
				}
			}
			//$this->android_push($check_user['device_id'], $message,  $data, $badge=0);
		}else{ //android
			//dd($check_user);
			if($check_user['device_id'] != ''){
				if(!empty($message)){
					//echo '<br>'.$check_user['device_id'].'<br>';
					$this->android_fcm_push($check_user['device_token'], $message,  $data, $badge);
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
		$API_ACCESS_KEY  = 'AAAADjFDNBY:APA91bEJCK1OZA795f3UO8VHj3hX_1_PbQqhJX1sL16sitWZkOLtbU3WgMPT9FN88HDQK7cWsFqZR84_aloPGm5j7en2sYQ3ORkdbNg7JYMQcU0KI6E42zp29Np_nMHAdrmp3AuRofr5';
	   	
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
		//echo 'complete'; exit;
		curl_close($ch);
		return 1;
		}else{
		 // echo 'not'; exit;
		curl_close($ch);
		return 0;
		}
	}

	// iphone FCM 
	public function ios_fcm_push($id, $message, $relData, $badge){
		
		$url = "https://fcm.googleapis.com/fcm/send";
		$token =  $id; 
		//Client key
		//prd($relData['notification_title']);
		$serverKey = 'AAAADjFDNBY:APA91bEJCK1OZA795f3UO8VHj3hX_1_PbQqhJX1sL16sitWZkOLtbU3WgMPT9FN88HDQK7cWsFqZR84_aloPGm5j7en2sYQ3ORkdbNg7JYMQcU0KI6E42zp29Np_nMHAdrmp3AuRofr5';
		$title = "GhostTech";
 		if(isset($relData['notification_title'])){
			$title = $relData['notification_title'];
		}
		
		$body = $message;
		$msg['data']= array(
		'message' => $message,
		'relData' => $relData,
		'badge' => (int)$badge,
		);



			
		
		$notification = array('title' =>$title , 'body' => $body, 'sound' => 'default', 'badge' => $badge);
		$arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high','data'=>$msg['data']['relData']);
		/*$arrayToSend = array('aps'=>array(
		 	'relData' => $relData,
		 	'alert' => $message, 
		 	'badge' => intval(0), 'sound' => 'default' 
		 ),'to' => $token, 'priority'=>'high');*/
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


	public function android_fcm_push($id, $message, $relData, $badge){
		
		$url = "https://fcm.googleapis.com/fcm/send";
		$token =  $id; 
		//Client key
		//prd($relData['notification_title']);
		$serverKey = 'AAAADjFDNBY:APA91bEJCK1OZA795f3UO8VHj3hX_1_PbQqhJX1sL16sitWZkOLtbU3WgMPT9FN88HDQK7cWsFqZR84_aloPGm5j7en2sYQ3ORkdbNg7JYMQcU0KI6E42zp29Np_nMHAdrmp3AuRofr5';
		$title = "Chat";
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
		//print_r($response); //exit;
		//Close request
		if ($response === FALSE) {
		die('FCM Send Error: ' . curl_error($ch));
		}
		curl_close($ch);	
	}
	
	

	// Iphone APNS
	public function iphone_push($id, $message, $relData, $badge) {
		//header('Content-type: text/html; charset=utf-8');
		 echo $deviceToken = $id.'<br>';
		//$deviceToken = '5673719219f37a51aaa253126b892095c9d778feed081629939cd163a7cb5e33';
		// Put your private key's passphrase here:
		$deviceToken  = $id;
		$deviceToken  = trim($deviceToken);  
		$deviceToken  = '5673719219f37a51aaa253126b892095c9d778feed081629939cd163a7cb5e33';  
		$passphrase  = '';
		// //////////////////////////////////////////////////////////////////////////////
		//$ctx         = stream_context_create();
		/*$ctx = $streamContext = stream_context_create([
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false
            ]
        ]);*/
        $ctx = stream_context_create();
        //echo app_path(); exit;
		echo $pem_path = app_path().'/xz.pem';
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
			echo 'Connected to APNS' . PHP_EOL;
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
	    //print_r($result); 
	    //echo '<br>';
	    if (! $result)
			echo 'Message not delivered' . PHP_EOL;
		else
			echo 'Message successfully delivered' . PHP_EOL;
			
		//Close the connection to the server
		@socket_close($fp);
		fclose($fp);
			return;
	}

	public function sendApns_P8($deviceIds,$message,$optionalData,$badge){
		//echo app_path() ;  exit;
        //print_r([$deviceIds,$message,$optionalData]); exit;
        //$pem_path = app_path().'/AuthKey_RR5BW56AWA.p8';
	        $keyfile = app_path().'/AuthKey_V2F8B99VGF.p8';  # <- Your AuthKey file
	        $keyid = 'V2F8B99VGF';     # <- Your Key ID
	        $teamid = 'P9JJSZH9LF';    # <- Your Team ID (see Developer Portal)
	        $bundleid = 'com.mjav.ghosttech';               # <- Your Bundle ID
	        $url = 'https://api.push.apple.com'; # <- production url, or use 
		    //$url = 'https://api.sandbox.push.apple.com'; # <- development url, or use 

 
        //print_r($optionalData) exit;
        $pload = [];//isset($optionalData) ? $optionalData : [];
        
        $payload = array();
        $n_type = $optionalData['n_type'];
        $payload['aps'] = array('noti_type' => $n_type,'alert' => $message, 'badge' => intval(0), 'sound' => 'default','pload'=>$pload, 'n_type' => $n_type  );
        $payload = json_encode($payload);

 		//print_r($payload); exit;

        $key = openssl_pkey_get_private('file://'.$keyfile);

 

        $header = ['alg'=>'ES256','kid'=>$keyid];
        $claims = ['iss'=>$teamid,'iat'=>time()];

 

        // $header_encoded = base64($header);
        // $claims_encoded = base64($claims);
        $header_encoded = rtrim(strtr(base64_encode(json_encode($header)), '+/', '-_'), '=');
        $claims_encoded = rtrim(strtr(base64_encode(json_encode($claims)), '+/', '-_'), '=');

 

        $signature = '';
        openssl_sign($header_encoded . '.' . $claims_encoded, $signature, $key, 'sha256');
        echo $jwt = $header_encoded . '.' . $claims_encoded . '.' . base64_encode($signature);

 

        // only needed for PHP prior to 5.5.24
        if (!defined('CURL_HTTP_VERSION_2_0')) {
            define('CURL_HTTP_VERSION_2_0', 3);
        }

 		//echo $deviceIds.'<br>' ; 

        if(is_array($deviceIds)){
            foreach ($deviceIds as $k => $v) {
                $http2ch = curl_init();
                curl_setopt_array($http2ch, array(
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
                    CURLOPT_URL => "$url/3/device/$v",
                    CURLOPT_PORT => 443,
                    CURLOPT_HTTPHEADER => array(
                        "apns-topic: {$bundleid}",
                        "authorization: bearer $jwt"
                    ),
                    CURLOPT_POST => TRUE,
                    CURLOPT_POSTFIELDS => $payload,
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HEADER => 1
                ));

                $result = curl_exec($http2ch);
                print_r($result);
                if ($result === FALSE) {
                    echo "Error for given device : ".$v;
                    //$status = curl_getinfo($http2ch, CURLINFO_HTTP_CODE);
                    //throw new Exception("Curl failed: ".curl_error($http2ch));
                }
            }
        }else{
            $http2ch = curl_init();
            curl_setopt_array($http2ch, array(
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
                CURLOPT_URL => "$url/3/device/$deviceIds",
                CURLOPT_PORT => 443,
                CURLOPT_HTTPHEADER => array(
                    "apns-topic: {$bundleid}",
                    "authorization: bearer $jwt"
                ),
                CURLOPT_POST => TRUE,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HEADER => 1
            ));

            $result = curl_exec($http2ch);
            print_r($result); exit;
            if ($result === FALSE) {
                echo "Error for one device : ".$deviceIds;
                $status = curl_getinfo($http2ch, CURLINFO_HTTP_CODE);
                throw new Exception("Curl failed: ".curl_error($http2ch));
            }            
        }        
        return true;            
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

	public function user_search($data,$arg){
		//print_r($arg); exit;
		$user = User::where('unique_id',$data['unique_id'])
				->where('is_active_profile',1)
				->where('user_status',1)
				->where('id','!=',$arg['id'])
				->first();
		//print_r($user); exit;
		//$User_list = array();
		if(!empty($user)){
		//foreach($user as $list){
			$user_array['userid'] 			=  	@$user->id ? $user->id : '';
			$user_array['username'] 	=  	@$user->username ? $user->username : '';
			$user_array['unique_id'] 	=  	@$user->unique_id ? $user->unique_id : '';
			$checkroom = Room::where('sender_id', $arg['id'])->where('receiver_id', $user->id)->first();
			if(!empty($checkroom)){
				$user_array['r_id'] 	=  	@$checkroom['r_id'];
				$user_array['sender_id'] 	=  	@$checkroom['sender_id'];
				$user_array['receiver_id'] 	=  	@$checkroom['receiver_id'];
				$user_array['status'] 	=  	@$checkroom['status'];
				$user_array['chatroom'] 	=  	@$checkroom['chatroom'];
			}else{
				$user_array['r_id'] 	=  	0;
				$user_array['sender_id'] 	=  	0;
				$user_array['receiver_id'] 	=  	0;
				$user_array['status'] 	=  	-1;
				$user_array['chatroom'] = '';
			}
			//array_push($User_list,$user_array);
		//}
		}else{
			$user_array['id'] = '';
		}
		//echo '<pre>'; print_r($user_array); exit;
		
		return $user_array;
	}



	public function notification_match_detail($arg,$user_id){
		$modal     =  "App\Models\PendingMatches";
		$query = $modal::query();

		$user =$query->select('customer.*','pending_matches.*')
				->leftjoin('users as customer','pending_matches.reciver_id','customer.id')
				->where('pending_matches.sender_id','=',@$user_id)
				->where('pending_matches.chat_channel','=',$arg)
				->orderBy('pending_matches.id', 'DESC')->first();
			////////////

			$userData =array();	
			//$userData['myMatch'] = array();
				$userData['isFromSubCategory'] = 0;
			if(isset($user['id'])){
				if($user['is_pending'] == 1){
					$userData['isFromSubCategory'] = 1;
				}
				//	print_r($user); exit;
				$userData['id'] = $user['id'];
				$category = Categories::where('c_id',$user['cat_id'])->first();
				$subcategory = SubCategories::where('sc_id',$user['sub_cat_id'])->first();
				//print_r($subcategory); exit; 
				$photo = Photo::where('p_u_id',  $user['reciver_id'])->where('is_default', 1)->first();
				$userData['c_id'] = @$category['c_id']?$category['c_id']:0;
				$userData['c_name'] = @$category['c_name']?$category['c_name']:'';
				$userData['sc_c_id'] = @$subcategory['sc_id']?$subcategory['sc_id']:0;
				$userData['sc_name'] = @$subcategory['sc_name']?$subcategory['sc_name']:'';
				if(isset($user['phone'])){
					$userData['p_photo'] = @$photo->p_photo? URL('/public/images/'.$photo->p_photo):'';
					$userData['p_id'] = @$photo->p_id? $photo->p_id:0;
			        $userData['first_name'] = $user['first_name'] ? $user['first_name'] : '';
			        $userData['age'] 	= 	$user['age'].' Years';
					$userData['race'] 	= 	$user['race'] ? $user['race'] : 0;

					$userData['occupation_status'] = 	$user['occupation_status'] ? $user['occupation_status'] : 1;
					$userData['occupation'] = 	$user['occupation'] ? $user['occupation'] : '';
					$userData['descr'] = 	$user['description'] ? $user['description'] : '';
					$userData['is_pending'] = 0;
					$userData['sender_id'] = 	$user['sender_id'] ? $user['sender_id'] :'';
					$userData['reciver_id'] = 	$user['reciver_id'] ? $user['reciver_id'] :'';
					$userData['chat_channel'] = 	$user['chat_channel'] ? $user['chat_channel'] :'';
				}else{
					$userData['is_pending'] = 1;

				}
				
			}
			return $userData;
	}

	public function logout($data){

		$rescod = "";
		//print_r($data); exit;
		if ($data) {
        
			$user =  User::findorfail($data);
			$user->device_id = "";
			$user->device_type = 2;
			$user->save();

			$user = Auth::user()->token();
        	//$user->revoke();
        	$rescod = 642;

    	}else{

        	$rescod = 461;

    	}
		return $rescod;
	}

	public function deleteAccount($data){

		$deleteuser =  User::where('id',$data['userid'])
		->delete();
	
		return 1;
	}


	//subscriptionsList => It is used for get Subscription plan List
	public function subscriptionsList(){
        $query = Subscription::select('id','country','itunes_product_id','android_product_id','price')->where('country', 'US')->get();
        if(!empty($query)){
        	//$query =  $query->toArray();
        	$query->code = 200;
        }else{
        	$query->code = 400;
        }
        return $query;
    }

	//pendingSubscriptionPlan =>  It is used for save the purchased plan which is pending
 	public function pendingSubscriptionPlan($arg,$userId)
    { 
    	$data = $arg;
		$u_id =  $userId;
		$itunesReceipt = $data['itunes_receipt'];

        $receiptData = '{"receipt-data":"'.$itunesReceipt.'","password":"0139bef4d1ec412c9a1cd04db786346f"}';

        $endpoint =  'https://sandbox.itunes.apple.com/verifyReceipt';
        //$endpoint = 'https://buy.itunes.apple.com/verifyReceipt';
		$query = Transaction::where('user_id','=',$u_id )
        ->leftjoin('subscriptions','transactions.subscription_id','subscriptions.id')
        ->where('payment_status','=',1)
        ->where('expired_at', '>', NOW())
        ->orderBy('expired_at','DESC')
        ->first();
       	//print_r($query); exit;
        
        $ch = curl_init($endpoint);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $receiptData);

        $errno = curl_errno($ch);

        //print_r($errno); exit;

        if($errno==0){

            $response = curl_exec($ch);

            $receiptInfo = json_decode($response,true);
        	//echo '<pre>'; print_r($receiptInfo); exit;

            if(!empty($receiptInfo)){

                if(isset($receiptInfo['status']) && $receiptInfo['status']==0){

                    $latestReceiptInfo = $receiptInfo['latest_receipt_info'];

                    $latestTransactioninfo = $latestReceiptInfo[count($latestReceiptInfo)-1];

                    //echo'<pre>';print_r($latestTransactioninfo);

                   /* $SubscriptionModel = TableRegistry::get('Subscriptions'); //use Cake\ORM\TableRegistry;

                    $subscriptionData = $SubscriptionModel

                    ->find()

                    ->select(['id','price'])

                    ->where(['itunes_product_id'=>$latestTransactioninfo['product_id']])

                    ->first();  */ 
                    $find_other_user = Transaction::where('user_id','!=',$u_id )
			        ->where('itune_original_transaction_id','=',$latestTransactioninfo['original_transaction_id'])
			        ->first();

	                //print_r($find_other_user); exit;
                    
                    if(empty($find_other_user)){
	                    $transactionData = new Transaction();
						$transactionData->user_id = $u_id;
						$transactionData->subscription_id = 1;
						$transactionData->total_amount 	=  $data['amount'];
						$transactionData->currency 	=  $data['currency'];
						$transactionData->payment_status 	=  1;
						$transactionData->itune_original_transaction_id = $latestTransactioninfo['original_transaction_id'];
						$transactionData->itunes_receipt = $itunesReceipt;
						$transactionData->orderId = $latestTransactioninfo['transaction_id'];
						$transactionData->packageName = $latestTransactioninfo['product_id'];
						$transactionData->productId = $latestTransactioninfo['product_id'];
						$transactionData->purchaseTime =  date('Y-m-d H:i:s',strtotime($latestTransactioninfo['purchase_date']));
						$transactionData->purchaseState =  1;
						$transactionData->created_at =  date('Y-m-d H:i:s',strtotime($latestTransactioninfo['purchase_date']));
						$transactionData->expired_at =  date('Y-m-d H:i:s',strtotime($latestTransactioninfo['expires_date']));
						$transactionData->device_type = 0;
						$transactionData->purchaseToken = 'Iphone';
						if ($result = $transactionData->save()){
	                        $transaction_last_id = $transactionData->id;
	                      	$user = User::where('id', $u_id)
						       		->update([
						           'itunes_autorenewal' => 1 ,'is_subscribe' => 1,'active_subscription' => 1,
						           'last_transaction_id' => $transaction_last_id
					        ]);	
	                     
	                       	$is_success = 221;
						    //print_r($query); exit;


	                    }else{
	                        $is_success = 423;

	                    }
	                }else{
	                	$is_success = 424;
	                }

                }else{
                	$user = User::where('id', $u_id)
					       		->update([
					           'itunes_autorenewal' => 0 
				        ]);	

                     $is_success = 424;
                }

            }

        }

        return $is_success;
    } 

	//subscriptions -> It is used for get Subscription Type
	public function subscriptions()
	{ 
	
		$uid = Auth::user()->id;
		$query = User::select('subscriptions.itunes_product_id','subscriptions.android_product_id','subscriptions.name','users.is_subscribe','users.itunes_autorenewal','users.active_subscription','users.last_transaction_id')
		->leftjoin('subscriptions','users.active_subscription','subscriptions.id')
		->where('users.id',$uid)
		->first();
		if($query->active_subscription == 1){
			$query->code = 200;
		}else{
			$query->code = 650;

		}
        return $query;
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

	

	///androidSubscreption
	public function androidSubscreption($arg,$userId) {

        $request = $this->request;
        $postData = $arg;
		$u_id =  $userId;
        

       
        $requestStatus = 1;

        if( !isset($postData['orderId']) ) { $requestStatus = 0; }

        if( !isset($postData['productId']) ) { $requestStatus = 0; }

        if( !isset($postData['packageName']) ) { $requestStatus = 0; }

        if( !isset($postData['autoRenewing']) ) { $requestStatus = 0; }

        if( !isset($postData['purchaseToken']) ) { $requestStatus = 0; }

        if( !isset($postData['purchaseTime']) ) { $requestStatus = 0; }

        



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



                require_once app_path().'/GoogleClientApi/Google_Client.php';

                require_once app_path().'/GoogleClientApi/auth/Google_AssertionCredentials.php';



			  $CLIENT_ID = '659600511706-4c4guuub8ba4u9pot4dd10qk2pm29747.apps.googleusercontent.com';
			  //$CLIENT_ID = '500178777931-57oe6pro6q5oeq8v6vh184qedbba2meo.apps.googleusercontent.com';

			                //'110053402852490647256';

			  $SERVICE_ACCOUNT_NAME = 'prifir@pc-api-5741470531428325667-740.iam.gserviceaccount.com';
			            $KEY_FILE = app_path().'/pc-api-5741470531428325667-740-c4be254b0e01.p12';

			            $KEY_PW   = 'notasecret';



            $key = file_get_contents($KEY_FILE);

            $client = new \Google_Client();

            $client->setApplicationName("Chat");



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
                //print_r($token); exit;
                    

                $expireTime = "";

                $amount = 0;

                if( isset($token->access_token) && !empty($token->access_token) ) {

                    $appid = $postData['packageName'];

                    $productID = $postData['productId'];

                    $purchaseToken = $postData['purchaseToken'];



                    $ch = curl_init();

                    $VALIDATE_URL = "https://www.googleapis.com/androidpublisher/v3/applications/";

                    $VALIDATE_URL .= $appid."/purchases/subscriptions/".$productID."/tokens/".$purchaseToken;

                    $res = $token->access_token;
                    //print_r($res); exit;



                    $ch = curl_init();

                    curl_setopt($ch,CURLOPT_URL,$VALIDATE_URL."?access_token=".$res);

                    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

                    $result = curl_exec($ch);

                    $result = json_decode($result, true);

                    //print_r($result); exit;

                    

                    if(isset($result["startTimeMillis"])) {

                        $startTime = date('Y-m-d H:i:s', $result["startTimeMillis"]/1000. - date("Z"));

                        //$amount = $result["priceAmountMicros"]/1000000;

                    }

                    if(isset($result["expiryTimeMillis"])) {

                        $expireTime = date('Y-m-d H:i:s', $result["expiryTimeMillis"]/1000. - date("Z"));

                        $amount = $result["priceAmountMicros"]/1000000;

                    }

                }

                if(!empty($result)){
	                $date = new \DateTime();

	                $date->setTimestamp($postData['purchaseTime']/1000);

	                $dateStart = $date->format('Y-m-d H:i:s');

	                $transactionData = new Transaction();
					$transactionData->user_id = $u_id;
					$transactionData->subscription_id = 1;
					$transactionData->total_amount 	= $postData['amount'];
					$transactionData->currency 	= $postData['currency'];
					$transactionData->payment_status 	=  1;
					$transactionData->itune_original_transaction_id = $postData['orderId'];
					$transactionData->itunes_receipt = $result["orderId"];
					$transactionData->orderId = $result["orderId"];
					$transactionData->packageName = $postData['packageName'];
					$transactionData->productId = $productID;
					$transactionData->purchaseState =  1;//@$postData['purchaseState'];
					$transactionData->created_at =  $dateStart;
					$transactionData->expired_at =  $expireTime;
					$transactionData->device_type = 2;
					$transactionData->purchaseToken = $postData['purchaseToken'];
					if ($result = $transactionData->save()){
	                    $transaction_last_id = $transactionData->id;
	                  	$user = User::where('id', $u_id)
					       		->update([
					           'itunes_autorenewal' => 1 ,'is_subscribe' => 1,'active_subscription' => 1,
					           'last_transaction_id' => $transaction_last_id
				        ]);	
	                 
	                   	$is_success = 221;
					    //print_r($query); exit;


	                }else{
	                    $is_success = 423;

	                }
	            }else{
	            	$is_success = 429;
	            }

        } else {

             $is_success = 424;

        }
        return $is_success;
        
    }

    /*public function cronJobForaddList(){
    	//$model 		= "App\Models\Post";	


    	$getpatient = Post::where('current_physican_id','=',$data['Id'])
						->where('user_type','=',2)->where('user_status','=',1)->get();
    }*/

	//cronJobForSubscreption 
	public function cronJobForSubscreption() { //use for  cron
   

        $Result['code'] = '200';

        $request = $this->request;

        $requestStatus = 1;

        if($requestStatus==1) { 

             $currentDate = date('Y-m-d H:i:s');

            //$transactionsTable = TableRegistry::get('Transactions');

           /* $subData = $transactionsTable->find()

                        ->where(['expired_at < '=>$currentDate])

                        ->ToArray();*/
            $subData = Transaction::where('expired_at', '<', $currentDate)
	        ->get();
	        echo $currentDate;
	        //echo '<pre>'; print_r($subData); 
            if(!empty($subData) && count($subData)) {

                //---- get auth token ---------------

                require_once app_path().'/GoogleClientApi/Google_Client.php';

                require_once app_path().'/GoogleClientApi/auth/Google_AssertionCredentials.php';

                $CLIENT_ID = '100377813809460893738';

                    //'110053402852490647256';

                $SERVICE_ACCOUNT_NAME = 'h-subscriptions@h.h.gserviceaccount.com';
                $KEY_FILE = app_path().'/GoogleClientApi/h-39e53e5c539b.p12';

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





                //---- cron job work  ---------------------



                foreach ($subData as $key => $val) {

                    if( $val->device_type==2 ) {  // android
	                	

                        $expireTime = "";

                        $amount = 0;

                        if( isset($token->access_token) && !empty($token->access_token) ) {

                            $appid = $val->packageName;

                            $productID = $val->productId;

                            $purchaseToken = $val->purchaseToken;



                            $VALIDATE_URL = "https://www.googleapis.com/androidpublisher/v3/applications/";

                            $VALIDATE_URL .= $appid."/purchases/subscriptions/".$productID."/tokens/".$purchaseToken;

                            $res = $token->access_token;



                            $ch = curl_init();

                            curl_setopt($ch,CURLOPT_URL,$VALIDATE_URL."?access_token=".$res);

                            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

                            $result = curl_exec($ch);

                            $result = json_decode($result, true);

	                        if(isset($result["expiryTimeMillis"])) {
	                        	echo '<pre>'; print_r($result);

                                $expireTime = date('Y-m-d H:i:s', $result["expiryTimeMillis"]/1000. - date("Z"));
                                echo  $expireTime;
                                $amount = $result["priceAmountMicros"]/1000000;

                            	echo 'SUNIL'.$val->user_id; 

                                if($expireTime > date('Y-m-d H:i:s')) {
                                	echo 'Renew Test Sunil';
                                   /* Transaction::where('id',  $val->user_id)
							       		->update([
							           'expired_at' => $expireTime,
							           'payment_status' => 1
						        	]);	*/

                                    User::where('id',  $val->user_id)
                                    	->where('is_subscribe',0)
							       		->update([
							           'is_subscribe' => 1
						        	]);	

                                 

                                } else {

                                    echo 'Expire Test Sunil Aadroid';
                                    /*Transaction::where('id',  $val->user_id)
							       		->update([
							           'payment_status' => 2
						        	]);	*/
        

                                            
							       	User::where('id',  $val->user_id)
                                    	->where('is_subscribe',1)
							       		->update([
							           'is_subscribe' => 0
						        	]);	


                                    
                                } 



                            }

                        }

                    } else if( $val->device_type==1 ) {   // iphone

                        $itunesReceipt = $val->purchase_token;  


                        $password = "0139bef4d1ec412c9a1cd04db786346f";        

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
                                	echo '<pre>'; print_r($receiptInfo);
                                    echo 'SUNIL'.$val->user_id;

                                    $query = $transactionsTable->query();

                                    $result = $query->update()

                                            ->set(['expired_at' => $expireTime , 'status' => 1])

                                            ->where(['id' => $val->user_id])

                                            ->execute();

                                       User::where('id',  $val->user_id)
                                    	->where('is_subscribe',0)
							       		->update([
							           'is_subscribe' => 1,
							           'active_subscription' => 1
						        	]);	     

                                   /* $salonQuery = $userTable->query();

                                    $salonQuery->update()

                                                    ->set(['active_subscription' => 1])

                                                    ->where(['id' => $val->user_id, 'active_subscription' => 0])

                                                    ->execute();*/

                                } else {

                                    $query = $transactionsTable->query();

                                    /*$result = $query->update()

                                            ->set(['payment_status' => 2])

                                            ->where(['id' => $val->id])

                                            ->execute();*/

                                    User::where('id',  $val->user_id)
                                    	->where('is_subscribe',1)
							       		->update([
							           'is_subscribe' => 0,
							           'active_subscription' => 0
						        	]);	

                                      echo 'Expire Test Sunil IOS';
                                    $salonQuery = $userTable->query();


                                } 
                            }
                        }       
                    }
                }

            } 
        }   

        exit;   
    }
} 

