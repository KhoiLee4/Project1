<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
            'name' => 'nullable|string|max:100',
        ]);

        $file = $request->file('image');
        $filename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('images', $filename, 'public');

        $image = Image::create([
            'name' => $request->name ?? $file->getClientOriginalName(),
            'image_url' => Storage::url($path),
        ]);

        return response()->json([
            'message' => 'Image uploaded successfully',
            'image' => [
                'id' => $image->id,
                'name' => $image->name,
                'url' => $image->image_url,
                'full_url' => asset($image->image_url),
            ],
        ], 201);
    }

    public function show(string $id)
    {
        $image = Image::findOrFail($id);
        
        return response()->json([
            'id' => $image->id,
            'name' => $image->name,
            'url' => $image->image_url,
            'full_url' => asset($image->image_url),
        ]);
    }

    public function delete(string $id)
    {
        $image = Image::findOrFail($id);
        
        // Extract path from URL
        $path = str_replace('/storage/', '', $image->image_url);
        
        // Delete file from storage
        if (Storage::disk('public')->exists('images/' . basename($path))) {
            Storage::disk('public')->delete('images/' . basename($path));
        }
        
        $image->delete();
        
        return response()->json([
            'message' => 'Image deleted successfully',
        ]);
    }
}
