<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrivateAssetController extends Controller
{
    public function show(Request $request, string $path)
    {
        /** @var Storage */
        $storage = Storage::disk('private');

        if (! \file_exists($storage->path($path))) {
            return response('file not found', 404);
        }

        return response($storage->get($path), 200, [
            'Content-Type' => $storage->mimeType($path),
        ]);
    }
}
