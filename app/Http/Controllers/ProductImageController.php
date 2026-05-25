<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImageController extends Controller
{
    public function show(string $path)
    {
        $path = str_replace('\\', '/', $path);
        $path = ltrim($path, '/');

        if (! Str::startsWith($path, 'products/')) {
            abort(404);
        }

        $disk = Storage::disk(config('filesystems.default'));

        if (! $disk->exists($path)) {
            abort(404);
        }

        return $disk->response($path);
    }
}
