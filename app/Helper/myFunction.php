<?php
namespace App\Helper;
use Illuminate\Support\Facades\DB;
use Auth;
use App\User;
use File;
use Carbon\Carbon;

class myFunction{
    public static function id($table, $field)
    {
        $data = DB::table($table)->max($field);
        return $data + 1;
    }
    public static function getAssets(){
        // if(!empty($_SERVER['HTTP_HOST'])){
        //  $hostName = $_SERVER['HTTP_HOST']; 
            
        //  $url=$hostName.dirname($_SERVER['PHP_SELF']); 
        //  return $url;
        // }
        if(!empty($_SERVER['HTTP_HOST'])){
            $hostName = $_SERVER['HTTP_HOST']; 
            $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https://'?'https://':'http://';
            $url=$protocol.$hostName.dirname($_SERVER['PHP_SELF']); 
            return $url;
        }
    }
    public static function baseURL(){
        
        // $getslash = explode("/",dirname($_SERVER['PHP_SELF']));
        // if(!empty($getslash[1])){
        //     $baseurl=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
        // }else{
        //     $baseurl=$_SERVER['HTTP_HOST'];
        // }
        
        // return $baseurl;
        $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https://'?'https://':'http://';
        $getslash = explode("/",dirname($_SERVER['PHP_SELF']));
        if(!empty($getslash[1])){
            $baseurl=$protocol.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
        }else{
            $baseurl=$protocol.$_SERVER['HTTP_HOST'];
        }
        
        return $baseurl;
    }
    public static function validurl($url){
        if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$url)) {
            $valid = 'false';
        }else{
            $valid = 'true';
        }
        return $valid;
    }
    public static function loadPathAsset(){
        if(\Request::getHttpHost()=='localhost'){
            $path='../sources/images/'.Auth::user()->id;
        }else{
            $path='../sources/images/'.Auth::user()->id;
        }
        return $path;
    }
    public static function pathAsset(){
        if(\Request::getHttpHost()=='localhost'){
            $path=realpath(dirname('../../'))."/liatmenu/public/images";
        }else{
            $path=realpath(dirname(''))."/scaneat.id/public/images";
        }
        return $path;
    }
    public static function getProtocol(){
        $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
        if(\Request::getHttpHost()=='localhost'){
            return $protocol.'://'.\Request::getHost()."/gampangjualan/liatmenu/images";
        }else{
            return $protocol.'://'.'scaneat.id/images';
        }
    }
    public static function getMain(){
        $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
        if(\Request::getHttpHost()=='localhost'){
            return $protocol.'://'.\Request::getHost()."/gampangjualan/liatmenu";
        }else{
            return 'https://'.getData::getCatalogSession('catalog_username').'.scaneat.id';
        }
    }
    public static function getPhotoProfile(){
        $mainpath = myFunction::pathAsset();
        $subpath = 'users/'.Auth::user()->id.'/images/profil_pict';
        $path = $mainpath.'/'.$subpath;
        $query = User::where('id',Auth::user()->id)->first();
        $filephoto = basename(parse_url($query['profil_pict'])['path']);
        if(File::exists($path.'/'.basename(parse_url($filephoto)['path']))){
            $photo = str_replace('gampangjualan.my.id','gampangjualan.id',myFunction::getProtocol().'/'.$subpath.'/'.$filephoto);
        }else{
            $photo = $query['profil_pict'];
        }
        return $photo.'?'.time();
    }
    public static function generateCodeDate($date,$id){
        if($date==null){
            return "-";
        }else{
            $fulldate=explode(" ",$date);
            $mydate=substr($date,8,2);
            $month=substr($date,5,2);
            $year=substr($date,0,4);
            return str_pad($id, 8, "0",  STR_PAD_LEFT);
            //return $mydate.''.$month.''.$year.'-'.str_pad($id, 8, "0",  STR_PAD_LEFT);
        }
    }
    public static function thumb_youtube($v)
    {
        $thumb = 'https://img.youtube.com/vi/' . $v . '/0.jpg';
        return $thumb;
    }
    public static function check_diff_login()
    {
        $datework = Carbon::parse(\Auth::user()->created_at);
        $now = Carbon::now();
        return $datework->diffInDays($now);
    }
    public static function colorStatus($status)
    {
        switch($status){
            case 'Checkout':
                return "secondary";
                break;
            case 'Approve':
                return "primary";
                break;
            case 'Process':
                return "info";
                break;
            case 'Delivered':
                return "warning";
                break;
            case 'Completed':
                return "success";
                break;
            case 'Cancel':
                return "danger";
                break;
        }
    }
    public static function payment_type($type)
    {
        $metode_pembayaran = DB::table('metode_pembayaran')->where('id', $type)->first();

        $name  = '-';
        if($metode_pembayaran){
            $name  = $metode_pembayaran->name;
        }

        return $name;
        
        // switch($type){
        //     case 1:
        //         return "Cash";
        //         break;
        //     case 2:
        //         return "Bank Transfer";
        //         break;
        //     case 3:
        //         return "Payment Gateway";
        //         break;
        //     case 4:
        //         return "QRIS Payment";
        //         break;
        // }
    }
    public static function formatNumber($amount,$decimal = 0)
    {
        return number_format($amount, $decimal, '.', ',');
        //return $prefix . number_format($amount, 0, ',', '.');
    }
}