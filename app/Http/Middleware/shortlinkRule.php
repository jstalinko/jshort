<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\Jd;
use App\Models\Shortlink;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class shortlinkRule
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $short = $request->short;
        $shortz = Shortlink::where('short', $short)->where('active', true)->first();
        $ip = $request->ip() == '::1' ?? '8.8.8.8';
        $detect = Jd::get_country($ip);
        if(!$shortz) abort(404);
        //------------------------------ BLOCK UNKNOWN ------------------------------//
        $unknowns =[
            Jd::detect_browser(),
            Jd::detect_os(),
            Jd::detect_platform(),
            Jd::detect_device()
        ];
        if(preg_match("/".implode("|",$unknowns)."/i" , "unknown" ))
        {
            Jd::blocked($detect,'ACCESS BLOCKED UNKNOWN DEVICE ,OS OR PLATFORM',$shortz);
        }

        
        //------------------------------ DETECT LOCK COUNTRY ------------------------------//
        if ($shortz->lock_country[0] != 'all'  && !Jd::parse_comma($shortz->lock_country, $detect['country_code'])) {
            Jd::blocked($detect,'ACCESS BLOCKED BY LOCK COUNTRY RULES. ',  $shortz);
        }
        //------------------------------ DETECT LOCK BROWSER ------------------------------//
        if($shortz->lock_browser != 'all')
        {
            if($shortz->lock_browser == 'fb_browser' && Jd::detect_browser() != 'FB_Browser')
            {
                Jd::blocked($detect,'ACCESS BLOCKED BY LOCK BROWSER FB BROWSER ONLY',  $shortz);

            }elseif($shortz->lock_browser == 'chrome_browser' && Jd::detect_browser() != 'Chrome')
            {
                Jd::blocked($detect,'ACCESS BLOCKED BY LOCK BROWSER CHROME ONLY',  $shortz);

            }elseif($shortz->lock_browser == 'opera_browser' && Jd::detect_browser() != 'Opera')
            {
                Jd::blocked($detect,'ACCESS BLOCKED BY LOCK BROWSER OPERA ONLY',  $shortz);

            }elseif($shortz->lock_browser == 'fb_chrome' && !in_array(Jd::detect_browser() , ['FB_Browser','Chrome']))
            {
                Jd::blocked($detect,'ACCESS BLOCKED BY LOCK BROWSER CHROME & FB BROWSER',  $shortz);

            }
        }

        //------------------------------ DETECT LOCK DEVICE ------------------------------//
        if($shortz->lock_device[0] != 'all' && !Jd::parse_comma($shortz->lock_device ,Jd::detect_device()))
        {
            Jd::blocked($detect,'ACCESS BLOCKED BY LOCK DEVICE RULES.',  $shortz);

        }

        //------------------------------ DETECT LOCK OS ------------------------------//
        if($shortz->lock_os[0] != 'all' && !Jd::parse_comma($shortz->lock_os , Jd::detect_os()))
        {
            Jd::blocked($detect,'ACCESS BLOCKED BY LOCK OS RULES.',  $shortz);

        }

        //------------------------------ DETECT LOCK REFERER ------------------------------//
        if($shortz->lock_referer[0] != 'all' && !Jd::parse_comma($shortz->lock_referer , Jd::detect_referer()))
        {
            Jd::blocked($detect,'ACCESS BLOCKED BY LOCK REFERER ('.$shortz->lock_referer.') RULES.',  $shortz);

        }


        //------------------------------ BLOCKING CUSTOM ISP ------------------------------//
        $blacklist_isps = explode("\n",str_replace("\r","" , $shortz->block_isp));
        if(count($blacklist_isps) > 1){
        foreach($blacklist_isps as $isp)
        {
            if(substr_count($detect['isp'] , $isp) > 0)
            {
                Jd::blocked($detect,'ACCESS BLOCKED BY CUSTOM BLOCK ISP RULES (I)',  $shortz);

            }
            if(preg_match("/".$isp."/i" , $detect['isp']))
            {
                Jd::blocked($detect,'ACCESS BLOCKED BY CUSTOM BLOCK ISP RULES (II)',  $shortz);

            }
        }
        }
        //------------------------------ BLOCKING CUSTOM IP ------------------------------//
        $blacklist_ips = explode("\n",str_replace("\r","" , $shortz->block_ip));
        if(count($blacklist_ips) > 1){
        foreach($blacklist_ips as $ip)
        {
            if(preg_match("/".$ip."/i" , $ip))
            {
                Jd::blocked($detect,'ACCESS BLOCKED BY CUSTOM BLOCK IP RULES.',  $shortz);

            }
        }
        }
        //------------------------------ BLOCK VPN ------------------------------//
        if($shortz->block_vpn && Jd::is_vpn($ip))
        {
            Jd::blocked($detect,'ACCESS BLOCKED BY DETECTED VPN',  $shortz);

        }
        //------------------------------ BLOCK CRAWLER ------------------------------//
        if($shortz->block_crawler && in_array($detect['country_code'] , ['IE','SG','ID']))
        {
            Jd::blocked($detect,'ACCESS BLOCKED BY DETECTED IE,SG,ID COUNTRY',  $shortz);

        }
        



        return $next($request);
    }
}
