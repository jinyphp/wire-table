<?php
namespace Jiny\WireTable\Http\Trait;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * www의 Slot 안쪽으로 파일을 Upload 합니다.
 */
trait UploadSlot
{
    public $upload_visible = "private";
    public $upload_path;
    public $upload_move;
    public $upload = [];

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

        //dd($this->upload_move);

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

    private function resourcePath()
    {
        // Slot 리소스 이동
        $destinationPath = resource_path("/www");
        $destinationPath .= DIRECTORY_SEPARATOR.www_slot();
        $destinationPath .= DIRECTORY_SEPARATOR.trim($this->upload_move,'/');

        return $destinationPath;
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
                        $destinationPath = $this->resourcePath();

                        //dd($destinationPath);
                        if(!is_dir($destinationPath)) {
                            mkdir($destinationPath,0777,true);
                        }

                        //dump($destinationPath);
                        //dump($this->upload_move);

                        if (rename($sourcePath, $destinationPath.$filename)) {
                            $filename = rtrim($this->upload_move,'/').$filename;
                        }


                        //dd($filename);


                    }

                }

                $filename = trim($filename,'/');
                $this->upload[$key] = "/".$filename;
            }

            if(is_array($item)) {
                // 재귀호출
                $this->formFileCheck($item, $key);
            }
        }
    }

    /**
     * form 입력데이터에서 upload 객체가 있는지 판단합니다.
     */
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
            $path = ltrim($this->actions['upload']['path'],'/');
            return "/".$path;
            //return "/upload".$this->actions['upload']['path'];
        }

        // 기본 업로드 경로
        return "/upload";
    }


}
