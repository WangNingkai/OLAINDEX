<?php

use Illuminate\Database\Seeder;

class ParameterTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('parameters')->delete();
        \Illuminate\Support\Facades\DB::table('parameters')->insert([
            [
                'name' => 'access_token',
                'value' => '',
            ]
            ,
            [
                'name' => 'refresh_token',
                'value' => '',
            ]
            ,
            [
                'name' => 'access_token_expires',
                'value' => '',
            ]
            ,
            [
                'name' => 'name',
                'value' => 'OLAINDEX',
            ]
            ,
            [
                'name' => 'theme',
                'value' => 'materia',
            ]
            ,
            [
                'name' => 'root',
                'value' => '/',
            ]
            ,
            [
                'name' => 'expires',
                'value' => '10',
            ]
            ,
            [
                'name' => 'image_hosting',
                'value' => '0',
            ]
            ,
            [
                'name' => 'image_hosting_path',
                'value' => '',
            ]
            ,
            [
                'name' => 'image',
                'value' => 'bmp jpg jpeg png gif',
            ]
            ,
            [
                'name' => 'video',
                'value' => 'mkv mp4',
            ]
            ,
            [
                'name' => 'audio',
                'value' => 'mp3',
            ]
            ,
            [
                'name' => 'doc',
                'value' => 'csv doc docx odp ods odt pot potm potx pps ppsx ppsxm ppt pptm pptx rtf xls xlsx',
            ]
            ,
            [
                'name' => 'code',
                'value' => 'html htm css go java js json ts sh md',
            ]
            ,
            [
                'name' => 'stream',
                'value' => 'txt log',
            ]
            ,
            [
                'name' => 'password',
                'value' => md5('12345678'),
            ]
        ]);
    }
}
