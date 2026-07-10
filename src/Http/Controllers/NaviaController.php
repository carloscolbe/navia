<?php

namespace Navia\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Navia\Facades\Navia;

class NaviaController extends Controller
{
    public function index()
    {
        return Navia::view('navia::index');
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('navia.login');
    }

    public function upload(Request $request)
    {
        $fullFilename = null;
        $resizeWidth = 1800;
        $resizeHeight = null;
        $slug = $request->input('type_slug');
        $file = $request->file('image');

        $dataType = Navia::model('DataType')->where('slug', '=', $slug)->firstOrFail();

        if ($this->userCannotUploadImageIn($dataType, 'add') && $this->userCannotUploadImageIn($dataType, 'edit')) {
            abort(403);
        }

        $path = $slug.'/'.date('FY').'/';

        $filename = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
        $filename_counter = 1;

        // Make sure the filename does not exist, if it does make sure to add a number to the end 1, 2, 3, etc...
        while (Storage::disk(config('navia.storage.disk'))->exists($path.$filename.'.'.$file->getClientOriginalExtension())) {
            $filename = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension()).(string) ($filename_counter++);
        }

        $fullPath = $path.$filename.'.'.$file->getClientOriginalExtension();

        $ext = $file->guessClientExtension();

        if (in_array($ext, ['jpeg', 'jpg', 'png', 'gif'])) {
            $image = Image::read($file)
                ->scaleDown($resizeWidth, $resizeHeight)
                ->encodeByExtension($file->getClientOriginalExtension(), quality: 75);

            // move uploaded file from temp to uploads directory
            if (Storage::disk(config('navia.storage.disk'))->put($fullPath, (string) $image, 'public')) {
                $status = __('navia::media.success_uploading');
                $fullFilename = $fullPath;
            } else {
                $status = __('navia::media.error_uploading');
            }
        } else {
            $status = __('navia::media.uploading_wrong_type');
        }

        // Return URL for TinyMCE
        return Navia::image($fullFilename);
    }

    public function assets(Request $request)
    {
        try {
            $normalizer = new \League\Flysystem\WhitespacePathNormalizer();
            $path = dirname(__DIR__, 3).'/publishable/assets/'.$normalizer->normalizePath(urldecode($request->path));
        } catch (\LogicException $e) {
            abort(404);
        }

        if (File::exists($path)) {
            $mime = '';
            if (Str::endsWith($path, '.js')) {
                $mime = 'text/javascript';
            } elseif (Str::endsWith($path, '.css')) {
                $mime = 'text/css';
            } else {
                $mime = File::mimeType($path);
            }
            $response = response(File::get($path), 200, ['Content-Type' => $mime]);
            $response->setSharedMaxAge(31536000);
            $response->setMaxAge(31536000);
            $response->setExpires(new \DateTime('+1 year'));

            return $response;
        }

        return response('', 404);
    }

    protected function userCannotUploadImageIn($dataType, $action)
    {
        return auth()->user()->cannot($action, app($dataType->model_name))
                || $dataType->{$action.'Rows'}->where('type', 'rich_text_box')->count() === 0;
    }
}
