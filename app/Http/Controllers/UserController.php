<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\users;

class UserController extends Controller
{
    public function register(Request $request){

        if($request->name == '' || $request->name == null) {
        response()->json(['http_code' => 400 ,'payload' => 'نام کاربر را ارسال نمایید!'], 404)->send();
        }elseif($request->email == '' || $request->email == null){
        response()->json(['http_code' => 400 ,'payload' => 'ایمیل کاربر را ارسال نمایید!'], 404)->send();
        }else{

        $code = rand(10000 , 10000000);

        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['code'] = $code;

        $user_id = users::insert($data);

        $details = [
            'title' => 'Mail from Alireza Mostafavi',
            'body' => 'to verify your account please send ' . $code . ' to localhost:8000/verify . You have 5 minutes to use this code.'
        ];
       
        \Mail::to($request->email)->send(new \App\Mail\MyTestMail($details));
        
        response()->json(['http_code' => 200 ,'payload' => 'کاربر با موفقیت ثبت شد . کد اعتبار سنجی به ایمیل شما ارسال شد.'], 200)->send();
    }
}


    public function verify(Request $request){
        
        $user = users::findByEmail($request->email);

        if($user->code == $request->code){
            $now = date("Y-m-d H:i:s");

            $minutes = abs(strtotime($now) - strtotime($user->created_at));
        $minutes   = round($minutes / 60);

        if ($minutes > 5){
            response()->json(['http_code' => 400 ,'payload' => 'زمان استفاده از این کد به پایان رسیده است'], 400)->send();
        }else{
            users::verifyUser($user->id);
            response()->json(['http_code' => 200 ,'payload' => 'اکانت شما تایید شد'], 200)->send();
        }
        }else{
            response()->json(['http_code' => 400 ,'payload' => 'کد ارسالی اشتباه است'], 400)->send();
        }

    }


    public function getNewCode(Request $request) 
    {
        $user = users::findByEmail($request->email);

        if($user->verified == 1){
        response()->json(['http_code' => 400 ,'payload' => 'کاربر قبلا تایید شده است'], 400)->send();
    }else{
        $now = date("Y-m-d H:i:s");


        if($user->updated_at == null){

            $minutes = abs(strtotime($now) - strtotime($user->created_at));
            $minutes   = round($minutes / 60);
        }else{

            $minutes = abs(strtotime($now) - strtotime($user->updated_at));
            $minutes   = round($minutes / 60);
        }
        if ($minutes > 5){
            $code = rand(10000 , 10000000);
            $details = [
                'title' => 'Mail from Alireza Mostafavi',
                'body' => 'to verify your account please send ' . $code . ' to localhost:8000/verify . You have 5 minutes to use this code.'
            ];
           
            \Mail::to($user->email)->send(new \App\Mail\MyTestMail($details));
            
            response()->json(['http_code' => 200 ,'payload' => 'کد اعتبارسنجی جدید به ایمیل شما ارسال شد'], 200)->send();
        }else{
            $code = $user->code;

            $details = [
                'title' => 'Mail from Alireza Mostafavi',
                'body' => 'to verify your account please send ' . $code . ' to localhost:8000/verify . You have 5 minutes to use this code.'
            ];
           
            \Mail::to($user->email)->send(new \App\Mail\MyTestMail($details));
            
            response()->json(['http_code' => 200 ,'payload' => 'کد اعتبارسنجی مجددا به ایمیل شما ارسال شد'], 200)->send();
        }
    }
}
    
}