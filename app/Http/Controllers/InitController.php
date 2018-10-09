<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InitController extends Controller
{
    public function _1stInstall(Request $request)
    {
        $redirect_uri = $request->get('redirect_uri');
        $ru = "https://developer.microsoft.com/en-us/graph/quick-start?appID=_appId_&appName=_appName_&redirectUrl={$redirect_uri}&platform=option-php";

        $deepLink = "/quickstart/graphIO?publicClientSupport=false&appName=oneindex&redirectUrl={$redirect_uri}&allowImplicitFlow=false&ru=".urlencode($ru);

        $app_url = "https://apps.dev.microsoft.com/?deepLink=".urlencode($deepLink);

        return redirect()->away($app_url);
    }

}
