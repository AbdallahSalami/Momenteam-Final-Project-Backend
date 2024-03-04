<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function checkImageExists($filename)
    {
        $path = storage_path('app/public/images/' . $filename);
        return response()->json(['exists' => file_exists($path)]);
    }
}
