<?php
/**
 * Upload를 처리합니다.
 */
namespace Jiny\WireTable\Http\Trait;
use Illuminate\Support\Facades\DB;

trait Upload
{
    public $upload_visible = "private";
    public $upload_path;
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
        foreach($form as $key => $item) {
            if($this->checkTempUpload($item)) {
                // 업로드한 파일
                $upload_path = $this->uploadPath();
                $filename = $item->store($upload_path);
                $this->upload[$key] = $filename;
            }

            if(is_array($item)) {
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


    private function uploadPath()
    {
        // 매개변수로 업로드 경로가 지정된경우
        if($this->upload_path) {
            //return "/upload".$this->upload_path;
            $path = ltrim($this->upload_path,'/');
            return "/".$path;
        }

        // Actions에서 업로드 경로가 지정된 경우
        if(isset($this->actions['upload']['path'])) {
            return "/upload".$this->actions['upload']['path'];
        }

        // 기본 업로드 경로
        return "/upload";
    }


}
