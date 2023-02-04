<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests;

use App\User;
use App\Helper\myFunction;

use Image;
use Input;
use File;
use Auth;
use Mail;
use Carbon\Carbon;

class Register extends Model
{
    protected $table = 'register';

    public static function approve_data($id)
    {
        try {
            DB::transaction(function () use ($id) {
            	$register = Register::select('register.*','users.name','users.email','users.temp_password','users.phone','users.active')
					                    ->leftJoin('users','register.user_id','=','users.id')
					                    ->where('register.id',$id)
					                    ->first();
            	$expired = Carbon::now()->addMonth(explode(" ",$register['duration'])[0])->format('Y-m-d');
              	Register::where('id',$id)->update(['status'=>'Approved','expired'=>$expired]);
              	User::where('id',$register['user_id'])->update(['active'=>'Y','legitimate'=>'Y']);

            	$disp = array(
                    'email'=>$register['email'],
                    'name'=>$register['name'],
                    'namapengirim'=>'ScanEat Support',
                    'emailpengirim'=>'admin@scaneat.id',
                    'subject'=>"Informasi Pendaftaran : ".$register['invoice']." ( Approved )"
                );

                $content = array(
                    'customer' => ['name'=>$register['name'],'email'=>$register['email'],'phone'=>$register['phone']],
                    'register' => $register,
                    'expired' => $expired,
                    'account_to' => $register['account_to'],
                    'status' => "Approved",
                );

                // Mail::send('pages.email.register', $content, function($message) use ($disp)
                // {
                //    $message->from($disp['emailpengirim'], $disp['namapengirim']);
                //    $message->to($disp['email'], $disp['name'])->subject($disp['subject']);
                // });

                $api_token='0ba0f6d61c459fde8afaedd923c8ae6f'; //silahkan copy dari api token mailketing
                $from_name=$disp['namapengirim']; //nama pengirim
                $from_email=$disp['emailpengirim']; //email pengirim
                $subject=$disp['subject']; //judul email
                $content=view('pages.email.register', $content)->render(); //isi email format text / html
                $recipient=$disp['email']; //penerima email
                $params = [
                  'from_name' => $from_name,
                  'from_email' => $from_email,
                  'recipient' => $recipient,
                  'subject' => $subject,
                  'content' => $content,
                  // 'attach1' => 'direct url file httxxx/xxx/xx.pdf',
                  // 'attach2' => 'direct url file httxxx/xxx/xx.pdf',
                  // 'attach2' => 'direct url file httxxx/xxx/xx.pdf',
                  'api_token' => $api_token
                  ];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,"https://api.mailketing.co.id/api/v1/send");
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $output = curl_exec ($ch); 
                // print_r($output);
                curl_close ($ch);
        
            });
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
    public static function reject_data($request)
    {
        try {
            DB::transaction(function () use ($request) {
            	$data = $request->all();
            	$register = Register::select('register.*','users.name','users.email','users.phone','users.active')
					                    ->leftJoin('users','register.user_id','=','users.id')
					                    ->where('register.id',$data['id'])
					                    ->first();
              	Register::where('id',$data['id'])->update(['status'=>'Rejected','notes'=>$data['notes']]);

            	$disp = array(
                    'email'=>$register['email'],
                    'name'=>$register['name'],
                    'namapengirim'=>'ScanEat Support',
                    'emailpengirim'=>'admin@scaneat.id',
                    'subject'=>"Informasi Pendaftaran : ".$register['invoice']." ( Rejected )"
                );

                $content = array(
                    'customer' => ['name'=>$register['name'],'email'=>$register['email'],'phone'=>$register['phone']],
                    'register' => $register,
                    'account_to' => $register['account_to'],
                    'status' => "Rejected",
                    'notes' => $data['notes'],
                );

                // Mail::send('pages.email.register', $content, function($message) use ($disp)
                // {
                //    $message->from($disp['emailpengirim'], $disp['namapengirim']);
                //    $message->to($disp['email'], $disp['name'])->subject($disp['subject']);
                // });
                
                $api_token='0ba0f6d61c459fde8afaedd923c8ae6f'; //silahkan copy dari api token mailketing
                $from_name=$disp['namapengirim']; //nama pengirim
                $from_email=$disp['emailpengirim']; //email pengirim
                $subject=$disp['subject']; //judul email
                $content=view('pages.email.register', $content)->render(); //isi email format text / html
                $recipient=$disp['email']; //penerima email
                $params = [
                  'from_name' => $from_name,
                  'from_email' => $from_email,
                  'recipient' => $recipient,
                  'subject' => $subject,
                  'content' => $content,
                  // 'attach1' => 'direct url file httxxx/xxx/xx.pdf',
                  // 'attach2' => 'direct url file httxxx/xxx/xx.pdf',
                  // 'attach2' => 'direct url file httxxx/xxx/xx.pdf',
                  'api_token' => $api_token
                  ];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,"https://api.mailketing.co.id/api/v1/send");
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $output = curl_exec ($ch); 
                // print_r($output);
                curl_close ($ch);

            });
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
}
