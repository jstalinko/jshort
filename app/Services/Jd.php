<?php

namespace App\Services;

use App\Models\Ipgeo;
use Illuminate\Support\Facades\Storage;


class Jd
{
    public static function redirect($url, $method = 'header')
    {
        if ($method == 'header') {
            // Use header redirection
            @header('HTTP/1.1 301 Moved Permanently');
            @header("Cache-Control: no-cache, no-store, must-revalidate");
            @header("Pragma: no-cache");
            @header("Expires: 0");
            @header('Location: ' . $url);
            exit;
        } elseif ($method == 'js') {
            // Use JavaScript redirection
            echo '<script type="text/javascript">';
            echo 'window.location.href="' . $url . '";';
            echo '</script>';
            exit;
        } elseif ($method == 'meta') {
            // Use meta tag redirection
            echo '<meta http-equiv="refresh" content="0;url=' . $url . '">';
            exit;
        }
    }

    public static function get_country($ip)
    {
        if (Ipgeo::ipExist($ip)) {
            $ipgeo = Ipgeo::where('ip', $ip)->first();
            $response = [
                'ip' => $ipgeo->ip,
                'country_name' => $ipgeo->country_name,
                'country_code' => $ipgeo->country_code,
                'region_code' => $ipgeo->region_code,
                'region_name' => $ipgeo->region_name,
                'city' => $ipgeo->city,
                'zip' => $ipgeo->zip,
                'isp' => $ipgeo->isp,
                'lon' => $ipgeo->lon,
                'lat' => $ipgeo->lat,
                'is_proxy' => $ipgeo->is_proxy,
                'is_hosting' => $ipgeo->is_hosting,
                'by' => $ipgeo->by,
            ];
        } else {
            $ipapi = new IpApiService();
            $result = $ipapi->getIpInfo($ip);
            if ($result['status'] == 'success') {
                $response = [
                    'ip' => $result['query'],
                    'country_name' => $result['country'],
                    'country_code' => $result['countryCode'],
                    'region_code' => $result['region'],
                    'region_name' => $result['regionName'],
                    'city' => $result['city'],
                    'zip' => $result['zip'],
                    'isp' => $result['isp'],
                    'lon' => $result['lon'],
                    'lat' => $result['lat'],
                    'is_proxy' => $result['proxy'],
                    'is_hosting' => $result['hosting'],
                    'by' => 'ip-api.com',
                ];
                Ipgeo::create($response);
            } else {
                return null;
            }
        }

        return $response;
    }

    public static function detect_browser()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $browser = "Unknown Browser";

        $browser_array = array(
            '/FBAV|FBAN|FB_IAB|FB4A|FBIOS|instagram/i' => 'FB_Browser',
            '/OPR|Opera/i' => 'Opera',
            '/Chrome/i' => 'Chrome',
            '/msie|trident/i' => 'Internet Explorer', // Added 'trident' for IE11
            '/firefox/i' => 'Firefox',
            '/safari/i' => 'Safari',
            '/netscape/i' => 'Netscape',
            '/maxthon/i' => 'Maxthon',
            '/konqueror/i' => 'Konqueror',
            '/mobile/i' => 'Handheld Browser',
        );

        foreach ($browser_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $browser = $value;
                break; // Exit the loop once a match is found
            }
        }

        return $browser;
    }

    public static function detect_device()
    {
        // Get the User-Agent string
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        // Define device categories and their respective regex patterns
        $device_array = array(
            'Mobile' => '/Mobile|Android|iPhone|iPod|Opera Mini|IEMobile|WPDesktop|BlackBerry/i',
            'Tablet' => '/Tablet|iPad|Nexus 7|Nexus 10|KFAPWI|Silk|Kindle/i',
            'Desktop' => '/Windows|Macintosh|Linux/i', // This should be last as it is a broad category
        );

        // Default device type
        $device_type = 'Unknown Device';

        // Loop through each pattern and check if it matches the User-Agent
        foreach ($device_array as $device => $regex) {
            if (preg_match($regex, $user_agent)) {
                $device_type = $device;
                break; // Exit the loop once a match is found
            }
        }

        return $device_type;
    }

    public static function detect_platform()
    {
        $user_agent     =   $_SERVER['HTTP_USER_AGENT'];
        $os_platform    =   "Unknown OS Platform";
        $os_array       =   array(
            '/windows nt 10/i'     =>  'Windows 10',
            '/windows nt 6.3/i'     =>  'Windows 8.1',
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',
            '/iphone/i'             =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/webos/i'              =>  'Mobile'
        );
        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os_platform    =   $value;
            }
        }
        return $os_platform;
    }

    public static function detect_os()
    {
        $platform = self::detect_platform();

        if (preg_match("/windows/i", $platform)) {
            return "windows";
        } elseif (preg_match("/mac/i", $platform)) {
            return "macos";
        } elseif (preg_match("/iphone|ipod|ipad/i", $platform)) {
            return "ios";
        } elseif (preg_match("/android/i", $platform)) {
            return "android";
        } elseif (preg_match("/linux|ubuntu/i", $platform)) {
            return "linux";
        }

        return "unknown";
    }

    public static function detect_referer()
    {

        $referer = $_SERVER['HTTP_REFERER'] ?? '';

        // Check if the referer exists
        if (empty($referer)) {
            return 'No Referer'; // No referer found
        }

        // Define patterns for common referers (Google, Facebook, Instagram, etc.)
        $referer_array = array(
            'google' => '/google\.com/i',
            'facebook' => '/facebook\.com|instagram\.com/i',
            'twitter' => '/twitter\.com/i',
            'youtube' => '/youtube\.com/i',
        );

        // Loop through each referer pattern and check if it matches the referer URL
        foreach ($referer_array as $platform => $regex) {
            if (preg_match($regex, $referer)) {
                return $platform; // Return the name of the platform if matched
            }
        }

        return $referer;
    }
    public static function is_vpn($ip)
    {
        $session = sha1('is_vpn' . $ip);
        if (isset($_SESSION[$session])) {
            return $_SESSION[$session];
        } else {
            $url = "https://blackbox.ipinfo.app/lookup/{$ip}";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36");
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                curl_close($ch);
                return false;
            }
            curl_close($ch);

            if ($response === 'N') {
                $_SESSION[$session] = false;
                return false;
            } else {
                $_SESSION[$session] = true;
                return true;
            }
        }
    }

    public static function is_crawler()
    {

        if (preg_match('/bot|crawl|slurp|spider|mediapartners|WhatsApp|Google-Ads-Creatives-Assistant|Google-Adwords-Instant|adsbot|AdsBot-Google|AdsBot-Google-Mobile|GoogleOther|facebookexternalhit|Facebookbot|Facebot|Googlebot|Googlebot-Image|Googlebot-News|Googlebot-Video|Googlebot-Mobile|Mediapartners-Google|AdsBot-Google-Mobile-Apps|Bingbot|DuckDuckBot|Baiduspider|YandexBot|Sogou|Exabot|facebot|ia_archiver|AhrefsBot|SemrushBot|MJ12bot|DotBot|PetalBot|ZoominfoBot|Pingdom|UptimeRobot|TelegramBot|Twitterbot|LinkedInBot|Pinterestbot|DiscordBot|Snapchat|WeChatbot|BLEXBot|CocCocBot|SEOkicks|Amazonbot|AlexaBot|YandexImages|SiteAuditBot|Google-Read-Aloud|AdsBot-Google-Mobile-Apps|GTMetrix|AppEngine-Google|HubSpot|serpstatbot|SeznamBot|Datanyze|MegaIndex|OpenLinkProfiler|okhttp/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        } else {
            return false;
        }
    }

    public static function parse_comma($data, $user)
    {
        $data = strtoupper($data);
        $user = strtoupper($user);

        if (strpos($data, ",") !== false) {

            $arr = explode(",", $data);
            if (in_array($user, $arr)) {
                return true;
            } else {
                return false;
            }
        } else {
            if ($data == $user) {
                return true;
            } else {
                return false;
            }
        }
    }

    public static function matched($data, $user)
    {
        if (is_array($data)) {
            $expr = implode("|", $data);
        } else {
            $expr = $data;
        }
        if (preg_match("/" . $expr . "/i", $user)) {
            return true;
        } else {
            return false;
        }
    }

    public static function ngelog($short,$detect,$reason,$allow=false)
    {
        $format = [
            date('d-m-Y H:i'),
            $detect['ip'],
            $detect['country_name'],
            $detect['isp'],
            self::detect_browser(),
            self::detect_device(),
            self::detect_os(),
            self::detect_referer(),
            $reason
        ];
        if($short->logs)
        {
            $PATH = storage_path('logs/user_'.$short->user_id.'/short_'.$short->id);
            if(!is_dir($PATH))
            {
                mkdir($PATH,0777 , true);
            }
            Storage::append($PATH.'/blocked.log',implode(" | ",$format));
            if($allow)
            {
                $filename = 'allowed.log';
            }else{
                $filename = 'blocked.log';
            }
            file_put_contents($PATH.'/'.$filename , implode(" | " , $format).PHP_EOL , FILE_APPEND);
        }

    }
    public static function log_path($user_id,$short_id)
    {
        $PATH = storage_path('logs/user_'.$user_id.'/short_'.$short_id);
        return $PATH;

    }
    public static function blocked($detect,$reason , $short )
    {
        $short->update(['total_blocked' => $short->total_blocked+1] , ['id' => $short->id]);
       self::ngelog($short,$detect,$reason);
       self::redirect($short->cloak_url , $short->method);
       exit;
    }

    public static function allowed($detect,$reason,$short)
    {
        $short->update(['total_allowed' => $short->total_allowed+1] , ['id' => $short->id]);
       self::ngelog($short,$detect,$reason,true);
       self::redirect($short->real_url , $short->method);
       exit;

    }
}
