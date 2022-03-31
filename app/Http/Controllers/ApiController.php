<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Service\ApiService;
use App\Http\Controllers\Service\SpecialitiesService;
use Illuminate\Support\Facades\Auth; 
use App\Http\Controllers\Msg;
use App\Http\Controllers\Repository\UserRepository;
use App\Http\Controllers\Repository\CrudRepository;
use App\User;
use App\Models\Partners;
use App\Models\ChipData;
use App\Models\Categories;
use App\Models\Page;
use App\Models\SubCategories;
use Twilio\Rest\Client;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\ChatGrant;
use App\Http\Controllers\Utility\SendEmails;
use DateTime;
use DB;

use Validator;
use Route;


//use Illuminate\Routing\Controller as BaseController;

class ApiController extends Controller
{
    
    public function register(Request $request){
            
        $data = $request->all();
           
        if($request->method() == 'POST'){

            
            if(isset($data['unique_id'])){
                $data['unique_id'] = strtolower($data['unique_id']);
                // Register With Phone Number 
                $rules = array(  
                    'unique_id'=>'required',
                    'username' => 'required'

                );
            
                $validate = Validator::make($data,$rules);

                if($validate->fails() ){
                    
                    $validate_error = $validate->errors()->all();

                    $response = ['code' => 403, 'msg'=> $validate_error[0] ]; 

                }else{

                    $ApiService = new ApiService();
                    $Check = $ApiService->checkemail_phone($data);  
                    $error_msg = new Msg();
                    $msg =  $error_msg->responseMsg($Check->error_code);
                

                    if($Check->error_code == 203 ){
                        //print_r($Check); exit;
                        $datanew['unique_id'] =  strtolower($Check->unique_id); 
                        $datanew['device_id'] =  $Check->device_id?$Check->device_id:1; 
                        $datanew['device_type'] =  $Check->device_type?$Check->device_type:0; 
                        $datanew['fcm_token'] =  $Check->fcm_token?$Check->fcm_token:''; 
                        $datanew['password'] =  $Check->password; 
                        $ApiService = new ApiService();
                        $usdata = $ApiService->login($datanew);
                        //print_r($usdata); exit;
                        $response = [
                            'code' => 200,
                            'msg'=>  $msg,
                            'data' => $usdata->data
                        ];
                    }else{
                        $response = [
                            'code' => $Check->error_code,
                            'msg'=>  $msg
                        ];
                    }

                }
            }
           
            
            return $response;
        }   
    }
    
    /*********************************************************************
    * API                   => verify Phone and email                    *
    * Description           => It is used  verify                        *
    * Required Parameters   => code,password,confirm_password            *
    * Created by            => Sunil                                     *
    *********************************************************************/
    public function verifyUser(Request $request){

        $data = $request->all();

        if($request->method() == 'POST'){

            $ApiService = new ApiService();
            $Check = $ApiService->verifyUser($data);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 205 ){
                $ApiService = new ApiService();
                $Check = $ApiService->login($data);
                //print_r($Check); exit;
                $response = [
                    'code' => 200,
                    'msg'=>  $msg,
                    'data' => $Check->data
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   

    }


    
    /***********************************************************************************
      API                   => set Password                                            *
    * Description           => It is to set the password                               *
    * Required Parameters   =>                                                         *
    * Created by            => Sunil                                                   *
    ************************************************************************************/
    public function resetPassword(Request $request){
       
        $data = $request->all();
        if($request->method() == 'POST'){

            $rules = array(
                    'id'         =>  'required',
                    'password'      =>  'required');

            $validate = Validator::make($data,$rules);

            if($validate->fails()){

                $validate_error = $validate->errors()->all();
                $response = ['code' => 403, 'msg'=>  $validate_error[0]]; 

            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->resetPassword($data);

                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 638 || $Check->error_code == 645){

                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
            
            return $response;
        }   
    }

    /************************************************************************************
    * API                   => Login                                                    *
    * Description           => It is used to login new user                             *
    * Required Parameters   => email,password,device_id,device_type                     *
    * Created by            => Sunil                                                    *
    *************************************************************************************/

    public function login(Request $request){
        $data = $request->all();

        if($request->method() == 'POST'){

            $rules = array(
                    'password'      =>  'required | min:6',
                    'device_id'     =>  'required',
                    'device_type'   =>  'required');

            $validate = Validator::make($data,$rules);

            if($validate->fails()){
                $validate_error = $validate->errors()->all();
                $response = ['code' => 403, 'msg'=>  $validate_error[0]]; 

            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->login($data);
                
                    //print_r($Check); exit; 
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 200){
                    $response = [
                        'code' => 200,
                        'msg'=>  $msg,
                        'data' => $Check->data
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
            return $response;
        }   
    }


    public function registeremail(Request $request){
            
    	$data = $request->all();
    	   
       
    	if($request->method() == 'POST'){

            $rules = array('email' =>'required|email|max:255|unique:users','password'=>'required | min:8');
            

            $validate = Validator::make($data,$rules);

            if($validate->fails() ){
                
                $validate_error = $validate->errors()->all();

                $response = ['code' => 403, 'msg'=> $validate_error[0] ]; 

            }else{
                
                $ApiService = new ApiService();
                $Check = $ApiService->checkemail_phone($data);  
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            

                if($Check->error_code == 203 ){
                    $response = [
                        'code' => 200,
                        'msg'=>  $msg
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }

            }
    		
    		return $response;
    	}	
    }


    /******************************************************************************
    * API                   => ''                                                 *
    * Description           => It is used  verify  the email..                             *
    * Required Parameters   => ''                                                          *
    * Created by            => Sunil                                                       *
    ***************************************************************************************/

    public function activation(Request $request){
            //print_r($request->all());die;
            $id = $request->id;
            $code = $request->code;   

            $UserRepostitory   = new UserRepository();
            $getuser = $UserRepostitory->getuserById($id);
            //echo '<pre>'; print_r($getuser); exit;
            if($getuser['id'] == 1){
                $getCode = $getuser['forgot_password_code'];
            }else{
                $getCode = $getuser['activation_code'];
            }
            $endTime = strtotime("+5 minutes",strtotime($getCode));
            $newTime = date('H:i:s',$endTime);
            if($getCode == $request->code){
                $user = $UserRepostitory->update_activation($id);
                if($getuser['id'] == 1){
                    return view('admin/users/reset');
                }else{
                    return view('activations');

                } 
            }else{
                
                return view('activationsfail');
            }   
        }


    /***************************************************************************************
    * API                   => ''                                                          *
    * Description           => It is used  verify  the email..                             *
    * Required Parameters   => ''                                                          *
    * Created by            => Sunil                                                       *
    ***************************************************************************************/

    public function terms(Request $request){
           $result = DB::table('pages')->where('p_status','=',1)->where('id','=',1)->first();
            $error_msg = new Msg();
                $msg =  $error_msg->responseMsg(200);
                $data['terms'] = $result->p_description;
            $response = [
                'code' => 200,
                'msg'=>  $msg,
                'data' => $data
            ];
            return $response;
          // print_r($result->p_description);
    }   

    public function privacypolicy(Request $request){
           $result = DB::table('pages')->where('p_status','=',1)->where('id','=',2)->first();
           print_r($result->p_description);

    }   

    
    public function aboutus(Request $request){
            echo 'About Us';
    }    
      
  

    /*************************************************************************************
    * API                   => Forgot Password                                           *
    * Description           => It is used send forgot password mail..                    *
    * Required Parameters   => email                                                     *
    * Created by            => Sunil                                                     *
    **************************************************************************************/

    public function forgotPassword(Request $request){
        $data = $request->all();
        if($request->method() == 'POST'){
        
            $ApiService = new ApiService();
            $Check = $ApiService->forgotPassword($data);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 601){
                $response = [
                    'code' => 200,
                    'msg'=>  $msg
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }



    /***************************************************************************************
      API                   => Category list                                                *
    * Description           => It is to get Chip list                                       *
    * Required Parameters   => Access Token                                                 *
    * Created by            => Sunil                                                        *
    ***************************************************************************************/

    public function category_list(Request $request){
       
        if($request->method() == 'GET'){

            $ApiService = new ApiService();
            $Check = $ApiService->category_list();
            $Check_gender = $ApiService->gender_list();
            $Check_race = $ApiService->race_list();
            $Check_religion = $ApiService->religion_list();
            $Check_report = $ApiService->report_list();
            $Check_report = $ApiService->report_list();
            $Check_partner_type = $ApiService->partner_type();
            $Check_region = $ApiService->region();

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            $data = $Check->data;   
            $gender = $Check_gender->data;   
            $race = $Check_race->data;   
            $religion = $Check_religion->data;   
            $report = $Check_report->data;   
            $partner_type = $Check_partner_type->data;   
            $region = $Check_region->data;   
            if($Check->error_code == 641){
                $responseOld = [
                    'data'  => $data->toArray(),
                    'gender' =>  $gender->toArray(),  
                    'race' =>  $race->toArray(),  
                    'religion' =>  $religion->toArray(),  
                    'report' =>  $report->toArray(),  
                    'partner_type' =>  $partner_type->toArray(),  
                    'region' =>  $region->toArray(),  
                ];
                //echo '<pre>'; print_r($responseOld['gender']); exit;
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $responseOld['data']['data'],
                    'gender'  =>  $responseOld['gender']['data'],
                    'race'  =>  $responseOld['race']['data'],
                    'religion'  =>  $responseOld['religion']['data'],
                    'report'  =>  $responseOld['report']['data'],
                    'partner_type'  =>  $responseOld['partner_type']['data'],
                    'region'  =>  $responseOld['region']['data'],
                    'current_page' => $responseOld['data']['current_page'],
                    'first_page_url' => $responseOld['data']['first_page_url'],
                    'from' => $responseOld['data']['from'],
                    'last_page' => $responseOld['data']['last_page'],
                    'last_page_url' => $responseOld['data']['last_page_url'],
                    'per_page' => $responseOld['data']['per_page'],
                    'to' => $responseOld['data']['to'],
                    'total' => $responseOld['data']['total']
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }

    /***************************************************************************************
      API                   => Get and update Profile                                     *
    * Description           => It is user for Profile                                     *
    * Required Parameters   =>                                                            *
    * Created by            => Sunil                                                      *
    ***************************************************************************************/
    public function profile(Request $request){
        
        //$userId= Auth::user()->id;
        $userId = $request['userid'];
        $Is_method  = 0; 
        
        if($request->method() == 'GET'){
           

            //$data = $request->id;
            $data = $userId;
            $Is_method = 1;
            $ApiService = new ApiService();
            $Check = $ApiService->profile($Is_method,$data);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 207){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

        }

        if($request->method() == 'POST'){

            $data = $request->all();
            $Is_method = 0;
            $ApiService = new ApiService();
            $Check = $ApiService->profile($Is_method,$data);
            
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 217){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }
        }      
        return $response;
    }

    /**********************************************************************
    * API                   => Update Device                              *
    * Description           => It is user for email                       *
    * Required Parameters   =>                                            *
    * Created by            => Sunil                                      *
    **********************************************************************/
    public function update_device(Request $request){
        
        $userId= Auth::user()->id;
        $Is_method  = 0; 
      
        if($request->method() == 'POST'){
            $data = $request;
            $Is_method = 1;
            $ApiService = new ApiService();
            $Check = $ApiService->update_device($Is_method,$data,$userId);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 207){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

        }

        return $response;
    }


    /*****************************************************************************
    * API                   => create Post                                       *
    * Description           => It is Use to  create Post                         *
    * Required Parameters   =>                   *
    * Created by            => Sunil                                             *
    *****************************************************************************/    
    public function createPost(Request $request){

        $data = $request->all();

        if($request->method() == 'POST'){

            $rules = array(
                    'description'   =>  'required',
                    'post_type'   =>  'required');

            $validate = Validator::make($data,$rules);

            if($validate->fails()){
                $validate_error = $validate->errors()->all();
                $response = ['code' => 403, 'msg'=>  $validate_error[0]]; 

            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->createPost(2, $data);
                
                    //print_r($Check); exit; 
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 218){
                    $response = [
                        'code' => 200,
                        'msg'=>  $msg,
                        'data' => $Check->data
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
            return $response;
        }   
    }


    public function contact(Request $request){
        if($request->method() == 'POST'){
            $data = $request->all();
            //print_r($data); exit;
            $email = @$data['email'];
            $phone = @$data['phone'];
            if($data['c_id'] == 1){
                $c_id = "Enquiry";
            }elseif($data['c_id'] == 2){
                $c_id = "Complaint";
            }else{
                $c_id = "Feedback";
            }
            $msg = @$data['msg'];
            $name = @$data['first_name'];
            $to = 'hopple@mailinator.com';
            $SendEmail = new SendEmails();
            $SendEmail->sendContact($to,$email,$phone,$c_id,$name,$msg);
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg(648);
            $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                ];
                return $response;
        }
    }
    /**********************************************************************
      API                   => Get and update Profile                     *
    * Description           => It is user for Profile                     *
    * Required Parameters   =>                                            *
    * Created by            => Sunil                                      *
    **********************************************************************/
    public function user_detail(Request $request){
        
        $Is_method  = 0; 
      
        if($request->method() == 'GET'){
            //$data = $request->id;
            $data = $request['userid'];
            $Is_method = 1;
            $ApiService = new ApiService();
            $Check = $ApiService->user_detail($Is_method,$data);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 207){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

        }
        return $response;
    }

    
    




   


    /**********************************************************************
    * API                   => Home Page Post list                        *
    * Description           => It is to get Post list                     *
    * Required Parameters   => Access Token                               *
    * Created by            => Sunil                                      *
    ***********************************************************************/

    public function home_list(Request $request){
       
        if($request->method() == 'GET'){

            $ApiService = new ApiService();
            $Check = $ApiService->home_list($request);
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            if($Check->error_code == 647){
                //print_r($Check); exit;
                $data = $Check->data;   
                $responseOld = [
                    'data'  => $data->toArray()    
                ];
                $partner_array = array();
                $Partner_list['friend'] = array();

                foreach($responseOld['data']['data'] as $list){
                    //print_r($list);
                    $partner_array['userid']            =   @$list['userid'] ? $list['userid'] : 0;
                    $partner_array['status']            =   @$list['status'] ? $list['status'] : 0;
                    $partner_array['chatroom']            =   @$list['chatroom'] ? $list['chatroom'] : '';
                    $partner_array['username']  =   @$list['username'] ? $list['username'] : '';
                    $partner_array['unique_id']  =   @$list['unique_id'] ? $list['unique_id'] : '';
                    $partner_array['r_id']  =   @$list['r_id'] ? $list['r_id'] : 0;
                    $partner_array['sender_id']  =   @$list['sender_id'] ? $list['sender_id'] : 0;
                    $partner_array['receiver_id']  =   @$list['receiver_id'] ? $list['receiver_id'] : 0;
                    
                    array_push($Partner_list['friend'],$partner_array);
                }
                $Partner_list['current_page'] = $responseOld['data']['current_page'];
                $Partner_list['first_page_url'] = $responseOld['data']['first_page_url'];
                $Partner_list['from'] = $responseOld['data']['from']?$responseOld['data']['from']:0;
                $Partner_list['last_page'] = $responseOld['data']['last_page'];
                $Partner_list['last_page_url'] = $responseOld['data']['last_page_url'];
                $Partner_list['per_page'] = $responseOld['data']['per_page'];
                $Partner_list['to'] = $responseOld['data']['to']?$responseOld['data']['to']:0;
                $Partner_list['total'] = $responseOld['data']['total']?$responseOld['data']['total']:0;
                //echo '<pre>'; print_r($responseOld['data']); exit;
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Partner_list,
                    
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }


    /**********************************************************************
    * API                   => Bolck user list                            *
    * Description           => It is to get Bolck user list               *
    * Required Parameters   => Access Token                               *
    * Created by            => Sunil                                      *
    ***********************************************************************/

    public function block_list(Request $request){
       
        if($request->method() == 'GET'){
            $ApiService = new ApiService();
            $Check = $ApiService->block_list($request);
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            if($Check->error_code == 647){
                //print_r($Check); exit;
                $data = $Check->data;   
                $responseOld = [
                    'data'  => $data->toArray()    
                ];
                $partner_array = array();
                $Partner_list['friend'] = array();

                foreach($responseOld['data']['data'] as $list){
                    //print_r($list);
                    $partner_array['userid']            =   @$list['userid'] ? $list['userid'] : '';
                    $partner_array['status']            =   @$list['status'] ? $list['status'] : '';
                    $partner_array['chatroom']            =   @$list['chatroom'] ? $list['chatroom'] : '';
                    $partner_array['username']  =   @$list['username'] ? $list['username'] : '';
                    $partner_array['unique_id']  =   @$list['unique_id'] ? $list['unique_id'] : '';
                    
                    array_push($Partner_list['friend'],$partner_array);
                }
                $Partner_list['current_page'] = $responseOld['data']['current_page'];
                $Partner_list['first_page_url'] = $responseOld['data']['first_page_url'];
                $Partner_list['from'] = $responseOld['data']['from']?$responseOld['data']['from']:0;
                $Partner_list['last_page'] = $responseOld['data']['last_page'];
                $Partner_list['last_page_url'] = $responseOld['data']['last_page_url'];
                $Partner_list['per_page'] = $responseOld['data']['per_page'];
                $Partner_list['to'] = $responseOld['data']['to']?$responseOld['data']['to']:0;
                $Partner_list['total'] = $responseOld['data']['total']?$responseOld['data']['total']:0;
                //echo '<pre>'; print_r($responseOld['data']); exit;
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Partner_list,
                    
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }

      /***************************************************************************************
      API                   => Recommend list                                               *
    * Description           => It is to get Recommend list                                  *
    * Required Parameters   => Access Token                                                *
    * Created by            => Sunil                                                       *
    ***************************************************************************************/

    public function recommend_list(Request $request){
       
        if($request->method() == 'GET'){

            $ApiService = new ApiService();
            $Check = $ApiService->recommend_list($request);
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            if($Check->error_code == 647){
                //print_r($Check); exit;
                $data = $Check->data;   
                $responseOld = [
                    'data'  => $data->toArray()    
                ];
                $partner_array = array();
                $Partner_list = array();

                foreach($responseOld['data']['data'] as $list){
                    $partner_array['id']            =   @$list['id'] ? $list['id'] : '';
                    $partner_array['name']  =   @$list['name'] ? $list['name'] : '';
                    $partner_array['desc']  =   @$list['desc'] ? $list['desc'] : '';
                    $partner_array['photo']         =  @$list['photo'] ? URL('/public/images/'.@$list['photo']) :URL('/public/images/profile.png');
                    $partner_array['status']        =   @$list['status'] ? $list['status'] : '';
                    $partner_array['promo_code']        =   @$list['promo_code'] ? $list['promo_code'] : '';
                    $partner_array['promo_detail']        =   @$list['promo_detail'] ? $list['promo_detail'] : '';
                    $partner_array['is_recommend']        =   @$list['is_recommend'] ? $list['is_recommend'] : 0;
                    $partner_array['is_premium']        =   @$list['is_premium'] ? $list['is_premium'] : 0;
                    
                    array_push($Partner_list,$partner_array);
                }
                //echo '<pre>'; print_r($responseOld['data']); exit;
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Partner_list,
                    'current_page' => $responseOld['data']['current_page'],
                    'first_page_url' => $responseOld['data']['first_page_url'],
                    'from' => $responseOld['data']['from'],
                    'last_page' => $responseOld['data']['last_page'],
                    'last_page_url' => $responseOld['data']['last_page_url'],
                    'per_page' => $responseOld['data']['per_page'],
                    'to' => $responseOld['data']['to'],
                    'total' => $responseOld['data']['total']
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }


    /***************************************************************************************
      API                   => Get  partner detail                                         *
    * Description           => It is user for partner detail                               *
    * Required Parameters   =>                                                             *
    * Created by            => Sunil                                                       *
    ***************************************************************************************/
    public function partner_detail(Request $request){
        
        $Is_method  = 0; 
      
        if($request->method() == 'GET'){
           

            //$data = $request->id;
            $data = $request['id'];
            $Is_method = 1;
            $ApiService = new ApiService();
            $Check = $ApiService->partner_detail($Is_method,$data);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 213    ){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

        }
        return $response;
    }

    /***************************************************************************************
      API                   => Get and update Profile                                     *
    * Description           => It is user for Profile                                     *
    * Required Parameters   =>                                                            *
    * Created by            => Sunil                                                      *
    ***************************************************************************************/
    public function profile1(Request $request){
        
        $userId= Auth::user()->id;
        $Is_method  = 0; 
      
        if($request->method() == 'GET'){
           

            //$data = $request->id;
            $data = $userId;
            $Is_method = 1;
            $ApiService = new ApiService();
            $Check = $ApiService->profile($Is_method,$data);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 207){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

        }

        if($request->method() == 'POST'){

            $data = $request->all();
            $Is_method = 0;
            $ApiService = new ApiService();
            $Check = $ApiService->profile($Is_method,$data);
            
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 217){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

        }      



        
        return $response;
    }






    /***************************************************************************************
      API                   => Upload Gallery                                              *
    * Description           => It is user for for CRED gallery api                                      *
    * Required Parameters   =>                                                             *
    * Created by            => Sunil                                                       *
    ***************************************************************************************/
    
    public function gallery(Request $request){
        $Is_method = 0;
        if($request->method() == 'GET'){
        
            $Is_method = 1;

            $rules = array('p_u_id' => 'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);

            if($validate->fails()){    
                $validate_error = $validate->errors()->all();  
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            
            }else{

                $ApiService = new ApiService();
                $Check = $ApiService->gallery($Is_method,$data);
                
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 218){
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg,
                        'data'  =>  $Check->data 
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }
        }    


        if($request->method() == 'POST'){


            $Is_method = 2;
            $rules = array('p_photo' => 'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);

            if($validate->fails()){      

                $validate_error = $validate->errors()->all();
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{

                $ApiService = new ApiService();
                $Check = $ApiService->gallery($Is_method,$data);
                
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 218){
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg,
                        'data'  =>  $Check->data 
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
        }  


        
        if($request->method() == 'DELETE'){


            $Is_method = 3;
            $rules = array('p_id' => 'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);

            if($validate->fails()){    
                $validate_error  = $validate->errors()->all();  
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{

                $ApiService = new ApiService();
                $Check = $ApiService->gallery($Is_method,$data);
                
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 214){
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
        }  

        return $response;
    
    }


    /***************************************************************************************
    * API                   => make default                                                *
    * Description           => It is used for creating the report                          *        
    * Required Parameters   =>                                                             *
    * Created by            => Sunil                                                       *
    ***************************************************************************************/
    
    public function make_default(Request $request){
         if($request->method() == 'POST'){
            $rules = array('p_id' => 'required','is_default' =>'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);

            if($validate->fails()){      

                $validate_error = $validate->errors()->all();
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->mark_default($data);
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 646){
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg,
                        'data'  =>  $Check->data 
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
        }   

        return $response;        
        
    }



   


    /************************************************************************************
    * API                   => Create Like post                                         *
    * Description           => It is used for liked the post                            * 
    * Required Parameters   =>                                                          *
    * Created by            => Sunil                                                    *
    ************************************************************************************/
    public function create_room(Request $request){
       
        if($request->method() == 'POST'){
            $data = $request;
            $ApiService = new ApiService();
            $Check = $ApiService->create_room($data);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            //print_r($Check); exit;
            if($Check->error_code == 219){
                $userId= Auth::user()->id;
                $result = DB::table('rooms')->select('users.id as userid','users.username as username','users.unique_id as unique_id','rooms.*')
                        ->where('sender_id',$userId)
                        ->where('receiver_id',$data['receiver_id'])
                        ->leftjoin('users','rooms.receiver_id','users.id')
                        ->first();


                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $result 
                ];
            }else if($Check->error_code == 302){
                $userId= Auth::user()->id;
                $result = DB::table('rooms')->select('users.id as userid','users.username as username','users.unique_id as unique_id','rooms.*')
                        ->where('sender_id',$userId)
                        ->where('receiver_id',$data['receiver_id'])
                        ->leftjoin('users','rooms.receiver_id','users.id')
                        ->first();


                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $result 
                ];
            
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }

     public function userNotify(Request $request){
       
        if($request->method() == 'POST'){
            $data = $request;
            $ApiService = new ApiService();
            $Check = $ApiService->userNotify($data);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            //print_r($Check); exit;
            if($Check->error_code == 649){
         
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg 
                ];
            }else if($Check->error_code == 302){
                

                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg
                ];
            
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }


    /************************************************************************************
    * API                   => Create favourite post                                    *
    * Description           => It is used for favourite post                            * 
    * Required Parameters   =>                                                          *
    * Created by            => Sunil                                                    *
    ************************************************************************************/
    public function favourite(Request $request){
       
        if($request->method() == 'POST'){
            $data = $request;
            $ApiService = new ApiService();
            $Check = $ApiService->favourite($data);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            //print_r($msg); exit;
            if($Check->error_code == 219){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    //'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }

 
    /***************************************************************************************
    * API                   => Patient List                                              *
    * Description           => It is used for getting patient list                        *        
    * Required Parameters   =>                                                             *
    * Created by            => Sunil                                                       *
    ***************************************************************************************/
    
    public function patient_list(Request $request){
       
        if($request->method() == 'GET'){

            $ApiService = new ApiService();
            $Check = $ApiService->patient_list();

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 635){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }


    



    /***************************************************************************************
      API                   => Chip Register                                                *
    * Description           => It is to Register Chip                                                   *
    * Required Parameters   => Access Token                                                             *
    * Created by            => Sunil                                                        *
    ***************************************************************************************/

    public function chip(Request $request){

        if($request->method() == 'POST'){

            $data = $request->all();
            $rules = array('chip_name' =>'required|max:255','unique_id'=>'required');

            $validate = Validator::make($data,$rules);

            if($validate->fails() ){
                
                $validate_error = $validate->errors()->all();

                $response = ['code' => 403, 'msg'=> $validate_error[0] ]; 

            }else{

                $ApiService = new ApiService();
                $Check = $ApiService->chip($data);
                //print_r($Check->data->id);exit;

                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 210){
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg,
                        'data'  => $Check->data->id
                        
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
            
            return $response;
        }

    }

    

    /*************************************************************************************
      API                   => check_username                                            *
    * Description           => It is user for username                                   *
    * Required Parameters   =>                                                           *
    * Created by            => Sunil                                                     *
    *************************************************************************************/
    public function check_username(Request $request){
        
        $userId= Auth::user()->id;
        $Is_method  = 0; 
      
        if($request->method() == 'GET'){
            $data = $request;
            $Is_method = 1;
            $ApiService = new ApiService();
            $Check = $ApiService->check_username($Is_method,$data,$userId);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 207){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

        }

        return $response;
    }

     /*************************************************************************************
      API                   => Check unique_id                                            *
    * Description           => It is user for username                                   *
    * Required Parameters   =>                                                           *
    * Created by            => Sunil                                                     *
    *************************************************************************************/
    public function check_unique_id(Request $request){
        
        $Is_method  = 0; 
      
        if($request->method() == 'GET'){
            $data = $request;
            $Is_method = 1;
            $ApiService = new ApiService();
            $Check = $ApiService->check_unique_id($Is_method,$data);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 207){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

        }

        return $response;
    }

    /***************************************************************************************
      API                   => chat_user for test                                          *
    * Description           => It is user for chat_user                                  *
    * Required Parameters   =>                                                            *
    * Created by            => Sunil                                                      *
    ***************************************************************************************/
    public function chat_user(Request $request){
        $userId = Auth::user()->id;
        // Find your Account SID and Auth Token at twilio.com/console
        // and set the environment variables. See http://twil.io/secure
        $sid = getenv("TWILIO_ACCOUNT_SID");
        $token = getenv("TWILIO_AUTH_TOKEN");
        $twilio = new Client($sid, $token);
        //print_r($twilio); exit;
        $user = $twilio->conversations->v1->users
                                          ->create($userId);

        //print_r($user); exit;
        $sid = $user->sid;
        $ApiService = new ApiService();
        $Check = $ApiService->chat_user_sid_update($sid,$userId);
    }


    /***************************************************************************************
      API                   => Chat_token                                                 *
    * Description           => It is user for test_twilio                                 *
    * Required Parameters   =>                                                            *
    * Created by            => Sunil                                                      *
    ***************************************************************************************/
    public function chat_token(Request $request){
        
        // Required for all Twilio access tokens
        // Required for Chat grant
        $data = $request; 
        //print_r($data['device_type']); exit; 
        $twilioAccountSid = getenv("TWILIO_ACCOUNT_SID");
        $twilioApiKey = getenv("TWILIO_APIKEY");
        $twilioApiSecret = getenv("TWILIO_APISECRET");
        $userId = Auth::user()->id;
        // Required for Chat grant
        $serviceSid = getenv("TWILIO_SERVICESID");//Default
        $chat_env = getenv("CHAT_ENV");//Default
        // choose a random username for the connecting user
        $identity = $chat_env.''.$userId ;//$data['sid'];

        // Create access token, which we will serialize and send to the client
        $token = new AccessToken(
            $twilioAccountSid,
            $twilioApiKey,
            $twilioApiSecret,
            3600,
            $identity
        );
        //print_r($token); exit;
        // Create Chat grant
        $chatGrant = new ChatGrant();
        $chatGrant->setServiceSid($serviceSid);
        if($data['device_type'] == 0){// APNS
            $chatGrant->setPushCredentialSid('CR6d5f79c62f75ff86e03453027a6662dd');
        }else{//FCM
            $chatGrant->setPushCredentialSid('CR159af2c172372ea4bf411d8e465104c5');
        }
       
        // Add grant to token
        $token->addGrant($chatGrant);

        // render token to string
        $user_token = $token->toJWT();

        
        $response = [
            'code' => 200,
            'msg'=>  'Token created succesfully',
            'token'=> $user_token
        ];

        return $response;
    }


    public function addchatuser(){
        $sid = getenv("TWILIO_ACCOUNT_SID");
        $token = getenv("TWILIO_AUTH_TOKEN");
        $twilio = new Client($sid, $token);

       /* $message = $twilio->conversations->v1->conversations("CHc1bafe6eab554f01ba755b350fb450e4")
                                     ->messages
                                     ->create([
                                                  "author" => "Dev3",
                                                  "body" => "Ahoy there!"
                                              ]
                                     );*/
          
        //if($data->EventType == 'onConversationAdded'){
            //fwrite($file,"\n ". print_r('sunil2', true));
            // fwrite($file,"\n ". print_r($data->EventType, true));
            // $receiver_id = getenv("CHAT_ENV").''.$data->Attributes; 
           //echo $receiver_id = getenv("CHAT_ENV").'3'; 
        $participant = $twilio->conversations->v1->conversations("CHc779b7e4ed3b44c29bad092083e68d61")
                 ->participants
                 ->create([
                            "identity" => "Dev41"
                          ]
                 );
                $datanew =  json_decode ($participant ,true );                          
        print($datanew);
            //print($participant->sid);
        //}

    }

    public function chat_post_event(Request $request){
        // Find your Account SID and Auth Token at twilio.com/console
        // and set the environment variables. See http://twil.io/secure
        $data = $request->all();  
        //if(isset($data)){

            //$datanew =  json_encode ( $data ,true );
            //$fileName = date('Ymd').'chat_post_event.txt';
            // prd($fileName);
            //$file = fopen($fileName,'a');
            $file = fopen('chat_pre_event.txt','a+');
            
            fwrite($file,"\n ". print_r('sunil1', true));
            //fwrite($file,"\n ". print_r($datanew, true));
            fwrite($file,"\n ". print_r($data, true));
            if(!empty($_FILES))
            {
            
                fwrite($file,"\n ".print_r($_FILES, true));
                fclose($file);
            
            }

            if($data['EventType'] == 'onConversationAdded'){
                $sid = getenv("TWILIO_ACCOUNT_SID");
                $token = getenv("TWILIO_AUTH_TOKEN");
                $twilio = new Client($sid, $token);
                fwrite($file,"\n ". print_r('sunil2', true));
                $ConversationSid = $data['ConversationSid'];
                $Attributes = $data['Attributes'];
                $receiver_id = getenv("CHAT_ENV").''.$data['Attributes']; 
                $participant = $twilio->conversations->v1->conversations($ConversationSid)
                     ->participants
                     ->create([
                                "identity" => $receiver_id
                              ]
                     );

                //print($participant->sid);
            }

           
            fwrite($file,"\n ". print_r('sunil6', true));
                
            /////////
        //}
    }

    public function chat_pre_event(Request $request){
        $data = $request->all();   
        //if(isset($data)){

            $datanew =  json_encode ( $data ,true );

            if($datanew['EventType'] == 'onConversationAdded'){


            }
            $file = fopen('chat_pre_event.txt','a+');
            
            fwrite($file,"\n ". print_r($datanew, true));
            fwrite($file,"\n ". print_r($datanew->EventType, true));
            fwrite($file,"\n ". print_r($datanew->Attributes, true));
            fwrite($file,"\n ". print_r('sunil', true));
            if(!empty($_FILES))
            {
            
                fwrite($file,"\n ".print_r($_FILES, true));
                fclose($file);
            
            }
            if($datanew->EventType == 'onMessageAdded'){
                 fwrite($file,"\n ". print_r($datanew->EventType, true));
                $sid = getenv("TWILIO_ACCOUNT_SID");
                $token = getenv("TWILIO_AUTH_TOKEN");
                $twilio = new Client($sid, $token);
                $receiver_id = getenv("CHAT_ENV").''.$datanew->Attributes; 
                $participant = $twilio->conversations->v1->conversations($datanew->ConversationSid)
                     ->participants
                     ->create([
                                "identity" => $receiver_id
                              ]
                     );

                //print($participant->sid);
            }
            /////////
        //}
    }

    public function chat_update_uername(Request $request){
        $data = $request->all();   
             //if(isset($data)){
       
        // Find your Account SID and Auth Token at twilio.com/console
        // and set the environment variables. See http://twil.io/secure
        $sid = getenv("TWILIO_ACCOUNT_SID");
        $token = getenv("TWILIO_AUTH_TOKEN");
        $twilio = new Client($sid, $token);

        $user = $twilio->conversations->v1->users("US6808d12f805c493b8572e02f81f03153")
          ->update([
                       "friendlyName" => "techno new name",
                   ]
          );

        //print($user->friendlyName);

       
                //print($participant->sid);
           
            /////////
        //}
    }


    public function check_pending(){
        $date = new DateTime;
        //echo $test = $date->format('Y-m-d H:i:s').'<br>';
        $date->modify('-1 minutes');
        $formatted_date = $date->format('Y-m-d H:i:s');

        $result = DB::table('pending_matches')->where('is_pending','=',1)->where('is_notify','=',0)->where('added_date','<',$formatted_date)->get();
        if(!empty($result)){
            foreach ($result as $resultkey => $resultvalue) {
                # code...
                DB::table('pending_matches')->where('id', $resultvalue->id)
                ->update([
                   'is_notify' => 1,
                   ]);
                $message =  "your are not found any match in last fifteen minutes.";
                $data['userid'] = $resultvalue->sender_id;
                $data['message'] = $message;
                $data['n_type'] = 3;
                $notify = array ();
                $notify['receiver_id'] = $resultvalue->sender_id;
                $notify['relData'] = $data;
                $notify['message'] = $message;
                echo print_r($notify);
                $UserRepostitory   = new UserRepository();
                $test =  $UserRepostitory->sendPushNotification($notify); 
                         echo '<pre>'; print_r($resultvalue->sender_id);
            }
        }
    }

    // Cron 30MIn
    public function update_previous(){
        $date = new DateTime;
        //echo $test = $date->format('Y-m-d H:i:s').'<br>';
        $date->modify('-1 minutes');
        $formatted_date = $date->format('Y-m-d H:i:s');

        $result = DB::table('pending_matches')->where('is_new','=',1)->where('is_pending','=',0)->where('added_date','<',$formatted_date)->get();

        if(!empty($result)){
            foreach ($result as $resultkey => $resultvalue) {
                //print_r($resultvalue->id); exit;
                # code...
                  DB::table('pending_matches')->where('id', $resultvalue->id)
                                ->update([
                               'is_pending' => 0,
                               'is_new' => 0,
                               ]);
                /*$message =  "your are not found any match in last fifteen minutes.";
                $data['userid'] = $resultvalue->sender_id;
                $data['message'] = $message;
                $data['n_type'] = 3;
                $notify = array ();
                $notify['receiver_id'] = $resultvalue->sender_id;
                $notify['relData'] = $data;
                $notify['message'] = $message;
                //print_r($notify); exit;
                $UserRepostitory   = new UserRepository();
                $test =  $UserRepostitory->sendPushNotification($notify); 
                         echo '<pre>'; print_r($resultvalue->sender_id);*/
            }
        }
    }
    /***************************************************************************************
      API                   => Chip Data                                                    *
    * Description           => It is to  Chipdata                                           *
    * Required Parameters   => Access Token                                                 *
    * Created by            => Sunil                                                        *
    ***************************************************************************************/

    public function chip_data(Request $request){

        if($request->method() == 'POST'){

            $data = $request->all();
            //echo '<pre>'; print_r($data);  exit;
            
            $ApiService = new ApiService();
            $Check = $ApiService->chipdata($data);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 219){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }
             
            return $response;
        }
    }

    /********************************************************************************
      API                   => serach unquie id                                     *
    * Description           => It is to get  unquie id                              *
    * Required Parameters   => Access                                               *
    * Created by            =>                                                      *
    *********************************************************************************/

    public function user_search(Request $request){
       
        if($request->method() == 'GET'){
            $ApiService = new ApiService();
            $data = $request->all();
            //echo '<pre>'; print_r($data);  exit;
            $Check = $ApiService->user_search($data);
            //print_r($Check); exit;

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            if($Check->error_code == 641){
                $data = $Check->data;   
                /*$responseOld = [
                    'data'  => $data->toArray()    
                ];*/
                //echo '<pre>'; print_r($data); exit;
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $data,
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }


     /***************************************************************************************
      API                   => graph_list                                                   *
    * Description           => It is to get Chip data list                                   *
    * Required Parameters   => Access Token                                                 *
    * Created by            => Sunil                                                        *
    ***************************************************************************************/

    public function graph_list(Request $request){
       
        if($request->method() == 'GET'){
            $ApiService = new ApiService();
            $data = $request->all();
            //$Check = $ApiService->graph_list($data);
            $model      = "App\Models\ChipData";
            $CrudRepository = new CrudRepository();
            //$Check = $ApiService->getdetailuser($model,$data);
            //echo '<pre>'; print_r($Check);  exit;
            $Check      = $CrudRepository->getdetailuser($model,$data['unique_id'],$data);
            //echo 'dasd'; exit;

            $error_msg = new Msg();
            //$msg =  $error_msg->responseMsg($Check->error_code);
            //$data = $Check->data;   
            if(!empty($Check)){
                $charArr = array();
                foreach ($Check as $userskey => $usersvalue) {
                   /* $chatTemp = array();
                    $chatTemp[] = $usersvalue->created_at; 
                    $chatTemp[] = $usersvalue->cycle_count; 

                    $charArr[] = $chatTemp;*/

                     $chatTemp = array();
                    $chatTemp['created_at'] = $usersvalue->created_at; 
                    $chatTemp['cycle_count'] = $usersvalue->cycle_count; 

                    $charArr[] = $chatTemp;
                    # code...
                }
               // echo '<pre>'; print_r($charArr); exit;
                $response = [
                    'code'  =>  200,
                    'msg'   =>  'Graph list',
                    'data'  =>  $charArr,
                ];
            }else{
                $response = [
                    'code' => '500',
                    'msg'=>  "No data found"
                ];
            }

            return $response;
        }   
    }


    public function graph_list_old(Request $request){
       
        if($request->method() == 'GET'){
            $ApiService = new ApiService();
            $data = $request->all();
            //$Check = $ApiService->graph_list($data);
            $model      = "App\Models\ChipData";
            $CrudRepository = new CrudRepository();
            //$Check = $ApiService->getdetailuser($model,$data);
            $Check      = $CrudRepository->getdetailuser($model,$data['unique_id']);
            //echo '<pre>'; print_r($Check);  exit;
            //echo 'dasd'; exit;

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            $data = $Check->data;   
            if($Check->error_code == 641){
                $responseOld = [
                    'data'  => $data->toArray()    
                ];
                //echo '<pre>'; print_r($responseOld['data']); exit;
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $responseOld['data'],
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }

    /***************************************************************************************
      API                   => Get and Question List                                      *
    * Description           => It is Question List                                        *
    * Required Parameters   =>                                                            *
    * Created by            => Sunil                                                      *
    ***************************************************************************************/
    public function question(Request $request){
        
        $Is_method  = 0; 
        if($request->method() == 'GET'){
            //$data = $request->id;
            $Is_method = 1;
            $data = Auth::user()->id; 
            $ApiService = new ApiService();
            $Check = $ApiService->question($Is_method,$data);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 300){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

        }
        return $response;
    }

     /**************************************************************************************
    * API                   => Create Answer                                              *
    * Description           => It is used for creating the Answer                         * 
    * Required Parameters   =>                                                            *
    * Created by            => Sunil                                                      *
    ***************************************************************************************/
    
    public function answer(Request $request){
       
        if($request->method() == 'POST'){
            $data = $request;
            $ApiService = new ApiService();
            $Check = $ApiService->answer($data);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            //print_r($msg); exit;
            if($Check->error_code == 301){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    //'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }

    public function answer_delete(Request $request)
    {
         if($request->method() == 'DELETE'){
            $rules = array('id' => 'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);

            if($validate->fails()){    
                $validate_error  = $validate->errors()->all();  
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{

                $ApiService = new ApiService();
                $Check = $ApiService->answer_delete($data);
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 302){
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
                return $response;
            }    
        }   
    }


    /***************************************************************************************
      API                   => Get and notification_match_detail                          *
    * Description           => It is notification_match_detail                            *
    * Required Parameters   =>                                                            *
    * Created by            => Sunil                                                      *
    ***************************************************************************************/
    public function notification_match_detail(Request $request){
        
        $Is_method  = 0; 
        if($request->method() == 'GET'){
            $req = $request->id;
            $Is_method = 1;
            $data = Auth::user()->id; 
            $ApiService = new ApiService();
            $Check = $ApiService->notification_match_detail($Is_method,$req,$data);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 303){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

        }
        return $response;
    }
    /***************************************************************************************
      API                   => Logout                                                     *
    * Description           => It is user for Logout                                      *
    * Required Parameters   =>                                                            *
    * Created by            => Sunil                                                      *
    ***************************************************************************************/


    public function logout(Request $request){
       
        if($request->method() == 'GET'){

            $ApiService = new ApiService();
            $Check = $ApiService->logout();

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 642){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }

    public function deleteAccount(Request $request)
    {
        if($request->method() == 'DELETE'){
               
                $ApiService = new ApiService();
                $Check = $ApiService->deleteAccount();
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 447){
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
                return $response;
            
        }   
    }

    /************************************************************************************
    * API                   => subscriptionsList                                        *
    * Description           => It is used for subscriptionsList                         * 
    * Required Parameters   =>                                                          *
    * Created by            => Sunil                                                    *
    *************************************************************************************/
    
    public function subscriptionsList(Request $request){
        if($request->method() == 'GET'){
            //$data = $request;
            $ApiService = new ApiService();
            $Check = $ApiService->subscriptionsList();
            //print_r($Check); exit;
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 220){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  'Subscriptions List',
                    'data'  =>  $Check  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }


    /************************************************************************************
    * API                   => subscriptions                                            *
    * Description           => It is used for subscriptions                             * 
    * Required Parameters   =>                                                          *
    * Created by            => Sunil                                                    *
    *************************************************************************************/
    
    public function subscriptions(Request $request){
        if($request->method() == 'GET'){
            //$data = $request;
            $ApiService = new ApiService();
            $Check = $ApiService->subscriptions();
            //print_r($Check); exit;
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            i
            if($Check->error_code == 220){
                
                $response = [
                    'code'  =>  200,
                    'msg'   =>  'Current plan check',
                    'data'  =>  $Check  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }


    /************************************************************************************
      API                   => pendingSubscriptionPlan IOS                              *
    * Required Parameters   => Access Token                                             *
    * Created by            => Sunil                                                    *
    *************************************************************************************/

    public function pendingSubscriptionPlan(Request $request){
         if($request->method() == 'POST'){
            $data = $request->all();
            $userId = Auth::user()->id;
            $datanew =  json_encode ($data ,true );
            // /*echo "<pre/>";
            // print_r($datanew);
            // exit;*/http://18.218.99.33/
            $path = '/var/www/html/public/images';
            $fileName = $path.'/'.date('Ymd').'subscription.txt';
           
            $file = fopen($fileName,'a+');
            fwrite($file,"\n ------------------------\n ");
            fwrite($file, 'time='.date('Y-m-d H:i:s'));
            fwrite($file,"\n ------------------------\n ");
            // //////////////
            //$file = fopen($fileName,'a');
            //$controller =  Route::getCurrentRoute()->getActionName()?Route::getCurrentRoute()->getActionName():'';
            
             //fwrite($file,"\n Called from api :- ".$controller );
             fwrite($file,"\n ". print_r($datanew, true));
            // fwrite($file,"\n -----------Response----------\n");
            // fwrite($file,"\n ". print_r($response, true));
            // fwrite($file,"\n -----------error msg---------\n");
            // fwrite($file,"\n ". print_r($errormsg, true));
            fwrite($file,"\n -----------userid-------------\n");
            fwrite($file,"\n ". print_r($userId, true));
            
            // //fwrite($file,"\n re :- ".  $ResponseData['error']);
            // //fwrite($file,"\n ". print_r(json_encode( $_POST['data']['User'] ), true));
            // if(!empty($_FILES))
            // {
            
                fwrite($file,"\n ".print_r($_FILES, true));
                fclose($file);
            
            // }
            ///////

            $ApiService = new ApiService();
            $Check = $ApiService->pendingSubscriptionPlan($data,$userId);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            //$data = $Check->data;   
            if($Check->error_code == 221){
                /*$responseOld = [
                    'data'  => $data->toArray()    
                ];*/
                //echo '<pre>'; print_r($responseOld['data']); exit;
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }  
    }

    /************************************************************************************
      API                   => cronJobForSubscreption                                   *
    * Required Parameters   => Access Token                                             *
    * Created by            => Sunil                                                    *
    *************************************************************************************/

    public function cronJobForSubscreption(Request $request){
         if($request->method() == 'GET'){
            $data = $request->all();
            //$userId = Auth::user()->id;
            $ApiService = new ApiService();
            $Check = $ApiService->cronJobForSubscreption();

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            $data = $Check->data;   
            if($Check->error_code == 221){
                $responseOld = [
                    'data'  => $data->toArray()    
                ];
                //echo '<pre>'; print_r($responseOld['data']); exit;
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }  
    }

    /*************************************************************************************
      API                   => pendingSubscriptionPlan IOS                               *
    * Required Parameters   => Access Token                                              *
    * Created by            => Sunil                                                     *
    *************************************************************************************/

    public function androidSubscreption(Request $request){
         if($request->method() == 'POST'){
            $data = $request->all();
            $userId = Auth::user()->id;
            $datanew =  json_encode ($data ,true );
            // /*echo "<pre/>";
            // print_r($datanew);
            // exit;*/http://18.218.99.33/
            $path = '/var/www/html/public/images';
            $fileName = $path.'/'.date('Ymd').'androidsubscription.txt';
           
            $file = fopen($fileName,'a+');
            fwrite($file,"\n ------------------------\n ");
            fwrite($file, 'time='.date('Y-m-d H:i:s'));
            fwrite($file,"\n ------------------------\n ");
            // //////////////
            //$file = fopen($fileName,'a');
            //$controller =  Route::getCurrentRoute()->getActionName()?Route::getCurrentRoute()->getActionName():'';
            
             //fwrite($file,"\n Called from api :- ".$controller );
             fwrite($file,"\n ". print_r($datanew, true));
            // fwrite($file,"\n -----------Response----------\n");
            // fwrite($file,"\n ". print_r($response, true));
            // fwrite($file,"\n -----------error msg---------\n");
            // fwrite($file,"\n ". print_r($errormsg, true));
            fwrite($file,"\n -----------userid-------------\n");
            fwrite($file,"\n ". print_r($userId, true));
            
            // //fwrite($file,"\n re :- ".  $ResponseData['error']);
            // //fwrite($file,"\n ". print_r(json_encode( $_POST['data']['User'] ), true));
            // if(!empty($_FILES))
            // {
            
                fwrite($file,"\n ".print_r($_FILES, true));
                fclose($file);
            
            // }
            ///////
            $ApiService = new ApiService();
            $Check = $ApiService->androidSubscreption($data,$userId);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            //$data = $Check->data;   
            if($Check->error_code == 221){
                /*$responseOld = [
                    'data'  => $data->toArray()    
                ];*/
                //echo '<pre>'; print_r($responseOld['data']); exit;
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }  
    }

}
