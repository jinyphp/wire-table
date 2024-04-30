<?php
namespace Jiny\WireTable\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ImageView extends Controller
{
    public function __construct()
    {

    }

    public function index($path, $filename)
    {

        $uri = Route::current()->uri;
        $uris = explode("/",$uri);

        $appPath = storage_path('app/'.$uris[1]);
        $filePath = $appPath.DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$filename;
        $filePath = str_replace(['/','\\'],DIRECTORY_SEPARATOR,$filePath);

        if (file_exists($filePath)) {
            $file = basename($filePath);
            $extension = strtolower(substr(strrchr($file,"."),1));
            switch( $extension ) {
                case "gif": $content_Type="image/gif"; break;
                case "png": $content_Type="image/png"; break;
                case "jpeg":
                case "jpg": $content_Type="image/jpeg"; break;
                case "svg": $content_Type="image/svg+xml"; break;
                default:
            }

            $body = file_get_contents($filePath);

            return response($body)
                ->header('Content-type',$content_Type);
        }
        //return "images :".$path."-".$filename;
    }
}
