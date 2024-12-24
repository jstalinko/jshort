<?php

namespace App\Http\Controllers;

use App\Services\Jd;
use App\Models\Shortlink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ShortlinkController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $shortz = Shortlink::where('short', $request->short)->where('active', true)->first();
        $ip = $request->ip() == '::1' ?? '8.8.8.8';
        $detect = Jd::get_country($ip);
        if (!$shortz) {
            abort(404);
        }

        $cacheKey = 'throttle_' . $request->short;
        $throttleCount = Cache::get($cacheKey, 0);
        $throttleCount += 1;
        Cache::put($cacheKey, $throttleCount, now()->addMinutes(60)); 
        if($throttleCount >= $shortz->throttle)
        {
            Jd::blocked($detect, 'BLOCKED BY MAX THROTTLE HITS. ('.$throttleCount.' > '.$shortz->throttle.')',$shortz);
        }
        $quote = new \RandomQuotes\RandomQuotes();
        $q = $quote->generate();
        $author = $q['quoteAuthor'];
        $text = $q['quoteText'];
        $que = $text." - ".$author;
        Jd::allowed($detect,'ACCESS ALLOWED: ' .$que , $shortz );

    }
}
