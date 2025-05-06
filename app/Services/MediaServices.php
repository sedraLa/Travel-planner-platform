<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class MediaServices
{
    /**
     * حفظ ملف وسائط (مثل صورة) في مجلد معين ضمن disk `public`.
     *
     * @param \Illuminate\Http\UploadedFile $file الملف المرفوع
     * @param string $type نوع الملف (مثلاً: image)
     * @param string $folder اسم المجلد داخل public
     * @return string المسار النسبي للملف (للاستخدام في العرض أو قاعدة البيانات)
     */
    public static function save($file, $type = 'image', $folder = 'uploads')
    {
        // توليد اسم فريد للملف
        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();

        // حفظ الملف في مجلد داخل disk 'public'
        $path = $file->storeAs($folder, $fileName, 'public');

        // إرجاع المسار النسبي (مثلاً: storage/Destinations/xyz.jpg)
        return 'storage/' . $path;
    }
}
