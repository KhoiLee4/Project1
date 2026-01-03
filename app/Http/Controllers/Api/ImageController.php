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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'name' => 'nullable|string|max:100',
        ]);

        $file = $request->file('image');
        $filename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('images', $filename, 'cloudinary');

        $imageUrl = Storage::disk('cloudinary')->url($path);

        $image = Image::create([
            'name' => $request->name ?? $file->getClientOriginalName(),
            'image_url' => $imageUrl,
        ]);

        return response()->json([
            'message' => 'Image uploaded successfully',
            'image' => [
                'id' => $image->id,
                'name' => $image->name,
                'url' => $image->image_url,
                'full_url' => $image->full_url,
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
            'full_url' => $image->full_url,
        ]);
    }

    public function delete(string $id)
    {
        $image = Image::findOrFail($id);
        
        if ($image->image_url && str_starts_with($image->image_url, 'http')) {
            try {
                $publicId = $this->extractPublicIdFromUrl($image->image_url);
                if ($publicId) {
                    Storage::disk('cloudinary')->delete($publicId);
                }
            } catch (\Exception $e) {
            }
        }
        
        $image->delete();
        
        return response()->json([
            'message' => 'Image deleted successfully',
        ]);
    }

    private function extractPublicIdFromUrl(string $url): ?string
    {
        $parsedUrl = parse_url($url);
        if (isset($parsedUrl['path'])) {
            $path = trim($parsedUrl['path'], '/');
            $parts = explode('/', $path);
            if (count($parts) >= 2) {
                $lastPart = end($parts);
                return str_replace('.' . pathinfo($lastPart, PATHINFO_EXTENSION), '', $lastPart);
            }
        }
        return null;
    }
}
