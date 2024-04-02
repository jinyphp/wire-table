<?php
/**
 * Upload를 처리합니다.
 */
namespace Jiny\WireTable\Http\Trait;
use Illuminate\Support\Facades\DB;

trait Upload
{
    public $upload_visible = "private";

    /** ----- ----- ----- ----- -----
     *  파일 업로드
     */
    public function setUploadPublic()
    {
        // public 폴더에 업로드 파일을 저장합니다.
        $this->actions['upload']['visible'] = "public";
    }

    public function setUploadPath($path)
    {
        $this->actions['upload']['path'] = $path;
    }


    private function checkUploadPath()
    {
        // public or private 저장영역 설정
        if(isset($this->actions['upload']['visible'])) {
            $visible = $this->actions['upload']['visible'];
        } else {
            $visible = "private";
        }
        $this->upload_visible = $visible;


        if(isset($this->actions['upload']['path'])) {
            $uploadPath = $this->actions['upload']['path'];
        } else {
            // 업로드 경로가 없는경우, 테이블명으로 경로를 지정합니다.
            $uploadPath = $this->actions['table'];
        }

        if($visible=="public") {
            $appPath = public_path();
            $path = $appPath.DIRECTORY_SEPARATOR.$uploadPath;
            if(!\is_dir($path)) {
                \mkdir($path, 755, true);
            }
        } else {
            // 저장소 폴더 확인
            $appPath = storage_path('app/'.$visible);
            $path = $appPath.DIRECTORY_SEPARATOR.$uploadPath;
            if(!\is_dir($path)) {
                \mkdir($path, 755, true);
            }
        }

        return $path;
    }

    // 업로드한 이미지 보기
    // <img src="/images/private{{$forms['image']}}">
    public function fileUpload()
    {
        // private, public 타입 확인
        // 업로드 저장 경로 페스 설정
        $path = $this->checkUploadPath();

        //dump("file update2");
        //dd($this->forms);

        foreach($this->forms as $key => $item) {
            if($item instanceof \Livewire\TemporaryUploadedFile) {

                // 업로드한 이미지
                $filename = $item->store("upload");
                $filename = substr($filename, strrpos($filename,'/')+1);
                $filePath = storage_path('app/upload').DIRECTORY_SEPARATOR.$filename;

                // 이동할 경로
                $movePath = $path."/".$filename;

                //dd("upload");

                if($this->forms_old[$key]) {
                    $oldfile = storage_path('app/'.$this->upload_visible).$this->forms_old[$key];
                    if(file_exists($oldfile)) {
                        //dd("이미지를 수정하셨네요");
                        unlink($oldfile);
                    }
                }

                if(isset($this->actions['upload']['path'])) {
                    // 사용자가 지정한 path
                    $this->forms[$key] = "/".$this->actions['upload']['path']."/".$filename;
                } else {
                    // 테이블명과 동일한 경로에 저장
                    $this->forms[$key] = "/".$this->actions['table']."/".$filename;
                }

                // dump($this->forms);

                // 실제 경로로 이동
                if(file_exists($filePath)) {
                    rename($filePath, $movePath);
                }

                // uploadfile 테이블에 기록
                /*
                DB::table('uploadfile')->updateOrInsert([
                    'table' => $this->actions['table'],
                    'field' => $key
                ]);
                */

            }
        }

    }




    private function checkEditUploadFile($origin)
    {
        // File필드만 검출
        foreach($this->forms as $key => $item) {
            if($item instanceof \Livewire\TemporaryUploadedFile) {

            }
        }

        // uploadfile 필드 조회
        /*
        $fields = DB::table('uploadfile')->where('table', $this->actions['table'])->get();
        foreach($fields as $item) {
            $key = $item->field; // 업로드 필드명
            if($origin->$key != $this->forms[$key]) {
                ## 이미지를 수정하는 경우, 기존 이미지는 삭제합니다.
                // Storage::delete($origin->$key);
            }
        }
        */
    }

}
