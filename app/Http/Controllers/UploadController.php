<?php
// app/Http/Controllers/Admin/SimpleUploadController.php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        // Временный упрощенный контроллер
        \Log::info('Upload request received', [
            'hasFile' => $request->hasFile('file'),
            'files' => $request->allFiles(),
            'all' => $request->all()
        ]);

        $file = $request->file('file');
        
        if (!$file) {
            return response()->json(['error' => 'No file'], 422);
        }
        

        
        // Сохраняем файл
        $path = $file->store('uploads', 'public');
        $url = asset('storage/' . $path);
        
        return response()->json([
            'location' => $url,
            'path' => $path
        ]);
    }
}