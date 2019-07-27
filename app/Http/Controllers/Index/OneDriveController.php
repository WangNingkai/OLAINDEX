<?php

namespace App\Http\Controllers\Index;

use App\Http\Controllers\Controller;
use App\Models\OneDrive;

class OneDriveController extends Controller
{
    public function __construct()
    {
        $this->model = new OneDrive;
    }

    public function index()
    {
        $oneDrives = $this->model->exclude('settings')->with('cover')->where('is_binded', 1)->get();

        return themeView('onedrive-list', compact('oneDrives'));
    }
}
