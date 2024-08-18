<?php
namespace Jiny\WireTable\Http\Trait;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Upload를 처리합니다.
 */
trait Upload
{
    public $upload_visible = "private";
    public $upload_path;
    public $upload_move;
    public $upload = [];
    //public $image;

    public function fileUpload($form=null, $path=null)
    {
        $this->upload = []; // 초기화

        if(!$form) {
            $form = $this->forms;
        }

        // 매개변수로 업로드 경로를 설정한 경우
        if($path) {
            $this->upload_path = $path;
        }

        // 이미지 이동 경로
        if(!$this->upload_move) {
            if(isset($this->actions['upload']['move'])) {
                $this->upload_move = $this->actions['upload']['move'];
            }
        }



        $this->formFileCheck($form);

        foreach($this->upload as $key => $value) {
            $this->forms[$key] = $value;
        }

        // 빈 객체 제거
        foreach($this->forms as &$item) {
            if(is_object($item)) {
                $item = null;
            }
        }

    }


    private function formFileCheck($form, $keyname = null)
    {
        // 업로드할 경로 분석
        $upload_path = $this->uploadPath();

        foreach($form as $key => $item) {
            if($this->checkTempUpload($item)) {
                // 임시파일 저장 및 업로드
                $filename = "/".$item->store($upload_path);

                // 이미지 이동 처리
                if($this->upload_move) {

                    if(Storage::exists($filename)) {
                        $sourcePath = storage_path('app'.$filename);

                        // Slot 리소스 이동
                        $destinationPath = resource_path("/www");
                        $destinationPath .= DIRECTORY_SEPARATOR.www_slot();
                        $destinationPath .= DIRECTORY_SEPARATOR.ltrim($this->upload_move,'/');

                        if(!is_dir($destinationPath.$upload_path)) {
                            mkdir($destinationPath.$upload_path,777,true);
                        }

                        //dump($sourcePath);
                        //dd($destinationPath.$filename);

                        if (rename($sourcePath, $destinationPath.$filename)) {
                            $filename = $this->upload_move.$filename;
                        }
                    }

                }

                $this->upload[$key] = $filename;
            }

            if(is_array($item)) {
                // 재귀호출
                $this->formFileCheck($item, $key);
            }
        }
    }

    private function checkTempUpload($item)
    {
        //if($item instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
        if(is_object($item)) {
            return true;
        }

        return false;
    }


    ## 업로드 파일은 /storage/app/upload 안에 위치합니다.
    ## upload 키워드로 라우트로 이미지를 처리합니다.
    private function uploadPath()
    {
        // 매개변수로 업로드 경로가 지정된경우
        if($this->upload_path) {
            //return "/upload".$this->upload_path;
            $path = ltrim($this->upload_path,'/');
            return "/".$path;
            //return "/upload".$path;
        }

        // Actions에서 업로드 경로가 지정된 경우
        if(isset($this->actions['upload']['path'])) {
            return "/upload".$this->actions['upload']['path'];
        }

        // 기본 업로드 경로
        return "/upload";
    }


}
