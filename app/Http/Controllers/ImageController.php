<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Image;
use File;

class ImageController extends Controller
{
    public function index()
    {
        $this->authorize('access', 'media_library.index');
        return to_json([
            'collection' => Image::filter()
        ]);
    }

    public function update($id, Request $request)
    {
        $this->authorize('access', 'media_library.update');
        $request->validate([
            'title' => 'required|string'
        ]);

        $image = Image::findOrFail($id);
        $image->title = $request->title;
        $image->save();

        return to_json([
            'saved' => true,
            'id' => $image->id
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('access', 'media_library.upload');
        $request->validate([
            'images' => 'array|min:1',
            'images.*' => 'required|image|max:'.config('flow.max_image_size')
        ]);

        // sleep(3);
        if($request->hasfile('images')) {
            $f = [];
            foreach($request->file('images') as $file) {
                $extension = $file->getClientOriginalExtension();
                $name = str_random(32).'.'.$extension;
                $label = $file->getClientOriginalName();
                $size = $file->getClientSize();

                $image_info = getimagesize($file);
                $image_width = $image_info[0];
                $image_height = $image_info[1];

                $dimension = "$image_width x $image_height";
                $path = $file->storeAs(
                    'images', $name
                );

                $f[] = Image::create([
                    'title' => $label,
                    'filename' => '/'.$path,
                    'size' => $size,
                    'extension' => $extension,
                    'dimension' => $dimension
                ]);
            }

            return response()
                ->json([
                    'saved' => true,
                    'images' => $f,
                    'str_rand' => str_random(3)
                ]);
        }

        return abort(404);
    }

    public function show($filename)
    {
        // validate file
    	$path = storage_path('app/images/' . $filename);

    	if (!File::exists($path)) {
    	    abort(404);
    	}

    	$file = File::get($path);
    	$type = File::mimeType($path);

    	$response = response()->make($file, 200);
    	$response->header("Content-Type", $type);
        // $response->header("Cache-Control", " private, max-age=86400");

    	return $response;
    }

    public function download($filename)
    {
        $path = storage_path('app/images/' . $filename);

        if (!File::exists($path)) {
            abort(404);
        }

        return response()->download($path);
    }

    public function destroy($id)
    {
        $this->authorize('access', 'media_library.delete');
        $model = Image::findOrFail($id);

        $path = storage_path('app' . $model->filename);
        File::delete($path);

        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
