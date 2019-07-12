<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Image;
use App\Services\ImageService;

class ClearImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:image';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '删除多余的图片';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $cover_ids = DB::table('one_drives')->where('cover_id', '!=', 0)->select('cover_id')->get()->pluck('cover_id')->all();
        $images = Image::whereNotIn('id', $cover_ids)->get()->pluck('id')->all();

        foreach ($images as $image) {
            (new ImageService($image))->delete();
        }
    }
}
