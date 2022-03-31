<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Route;
use Auth;
use Validator;
use App\User;
use App\Models\Chip;
use App\Http\Controllers\Utility\CustomVerfication;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Utility\SendEmails;
use App\Http\Controllers\Repository\UserRepository;
use App\Http\Controllers\Repository\CrudRepository;
use Session;
use \Cache;
use \Artisan;
class LoginController extends Controller
{

	public function homeView(Request $request){
		return view('welcome');  
 	}

   
	  
	public function termsConditionsView(Request $request){
		$this->data['UserId'] = session('UserId');
			$this->data['full_name'] = session('full_name');
			$this->data['email'] = session('email');
			$this->data['user_photo'] = session('user_photo');
		return view('website.terms-conditions',$this->data);    
 	}  
	
	public function privacyPolicyView(Request $request){
		$this->data['UserId'] = session('UserId');
			$this->data['full_name'] = session('full_name');
			$this->data['email'] = session('email');
			$this->data['user_photo'] = session('user_photo');
		return view('website.privacy-policy',$this->data); 
 	} 
	

	public function email(Request $request){
		$CustomVerfication = new CustomVerfication();
		$UserRepostitory   = new UserRepository();
		$SendEmail = new SendEmails();
		//echo $user   =  $request['email']; exit;
		if($request['email']){
			$message =$request['message'];
			//echo 'sunil'; exit; -	
			//$update = $UserRepostitory->update_forgot_code($user->id,$code);
			$Send = $SendEmail->sendAdminEmail($request->email,$message,$request['full_name']);
			$message = 'Enquery has been sent successfully.';
			$responseArr = [
				'code' => 200,
				'msg'=>  $message
			];
			return $responseArr;
			//return redirect('login')->with('success', 'Reset password email has been sent successfully.');
		}else{
				$message = 'Email does not exist.';
				$responseArr = [
					'code' => 0,
					'msg'=>  $message
				];
				return $responseArr;
				//return redirect('login')->with('error', 'Email does not exist !!');
		}
			
	}
	

}
