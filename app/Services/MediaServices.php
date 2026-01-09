<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class MediaServices
{
    /**
     * 
     *
     * @param \Illuminate\Http\UploadedFile $file 
     * @param string $type 
     * @param string $folder 
     * @return string 
     */
    public static function save($file, $type = 'image', $folder = 'uploads')
    {
        
        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();

       
        $path = $file->storeAs($folder, $fileName, 'public');

     
        return  $path;
    }
}
