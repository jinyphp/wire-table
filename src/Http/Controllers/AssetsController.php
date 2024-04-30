<?php
namespace Jiny\WireTable\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AssetsController extends Controller
{
    public function __construct()
    {

    }

    /**
     * WireTable에서 업로드한 이미지를 출력합니다.
     */
    public function index(Request $request)
    {
        $uri = request()->path();
        $path = storage_path('app');
        $file = $path.DIRECTORY_SEPARATOR.$uri;
        if (file_exists($file)) {
            return $this->response($file);
        }

        // 파일이 없습니다.
    }

    private function response($file)
    {
        // 파일 이름에서 확장자 추출
        $mime = $this->contentType($file);

        // BinaryFileResponse 인스턴스 생성
        $response = new BinaryFileResponse($file);

        // Content-Type 헤더 설정
        $response->headers->set('Content-Type', $mime);
        return $response;
    }

    private function contentType($file)
    {
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        switch( $extension ) {
            case "css":
                // CSS 파일인 경우
                $mime="text/css";
                break;
            case "js":
                // 예를 들어, JavaScript 파일인 경우
                $mime="application/javascript";
                break;

            case "json":
                $mime="application/json";
                break;

            case "gif":
                $mime="image/gif";
                break;
            case "png":
                $mime="image/png";
                break;
            case "jpeg":
            case "jpg":
                $mime="image/jpeg";
                break;
            case "svg":
                $mime="image/svg+xml";
                break;
            default:
                // 기본적으로 알려진 MIME 유형이 없는 경우
                $mime="application/octet-stream";
        }

        return $mime;
    }
}
