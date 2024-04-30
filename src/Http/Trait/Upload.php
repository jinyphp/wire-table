<?php
/**
 * Upload를 처리합니다.
 */
namespace Jiny\WireTable\Http\Trait;
use Illuminate\Support\Facades\DB;

trait Upload
{
    public $upload_visible = "private";
    public $upload = [];

    public function fileUpload()
    {
        // private, public 타입 확인
        // 업로드 저장 경로 페스 설정
        //$path = $this->checkUploadPath();

        $this->upload = []; // 초기화
        $form = $this->forms;
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
        if(isset($this->actions['upload']['path'])) {
            return $this->actions['upload']['path'];
        }

        return "upload";
    }


}
