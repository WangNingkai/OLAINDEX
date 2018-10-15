<?php

namespace App\Http\Middleware;

use App\Helpers\Tool;
use Closure;

class HotlinkProtection
{
    /**
     * 处理防盗链
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $hotlink_protection = Tool::config('hotlink_protection','');
        if (!$hotlink_protection) {
            return $next($request);
        }
        // 简单处理防盗链，建议加入更加其他防盗链措施
        $whiteList = explode(' ', $hotlink_protection);
        if (!$request->server('HTTP_REFERER')) {
            abort(403);
        }
        $ua = $request->server('HTTP_USER_AGENT');
        $badUA= ['Googlebot-Image','FeedDemon ','BOT/0.1 (BOT for JCE)','CrawlDaddy ','Java','Feedly','UniversalFeedParser','ApacheBench','Swiftbot','ZmEu','Indy Library','oBot','jaunty','YandexBot','AhrefsBot','MJ12bot','WinHttp','EasouSpider','HttpClient','Microsoft URL Control','YYSpider','jaunty','Python-urllib','lightDeckReports Bot','PHP','vxiaotou-spider','spider'];
        if(!$ua) {
            abort(403);
        }else{
            foreach ($badUA as $item) {
                if(str_contains($ua, $item)) {
                    abort(403);
                }
            }
        }
        //判断$_SERVER['HTTP_REFERER'] 是不是处于白名单
        foreach ($whiteList as $item) {
            if(strpos($request->server('HTTP_REFERER'),$item) == 0) {
                return $next($request);
            } else {
                abort(403);
            }
        }
    }
}
