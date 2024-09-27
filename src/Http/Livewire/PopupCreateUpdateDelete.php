<?php
namespace Jiny\WireTable\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class PopupCreateUpdateDelete extends Component
{
    use WithFileUploads;
    use \Jiny\WireTable\Http\Trait\UploadSlot;

    public $popupForm = false;
    public $popupWindowWidth = "4xl";
    public $message;

    public $popupDelete = false;
    public $confirm = false;

    public $forms=[];
    public $forms_old=[];
    public $last_id;

    public function mount()
    {

    }

    public function popupFormOpen()
    {
        $this->popupForm = true;
        $this->confirm = false;
    }

    public function popupFormClose()
    {
        $this->popupForm = false;
        $this->confirm = false;
    }

    /**
     * 입력 데이터 취소 및 초기화
     */
    public function cancel()
    {
        $this->forms = [];
        $this->forms_old = [];

        $this->popupForm = false;
        $this->popupDelete = false;
        $this->confirm = false;

        $this->message = null;
    }

    public function create()
    {
        $this->message = null;

        // 신규 삽입을 위한 데이터 초기화
        $this->forms = [];

        unset($this->actions['id']);

        // 후킹:: 컨트롤러 메서드 호출
        // if ($controller = $this->isHook("hookCreating")) {
        //     $form = $this->controller->hookCreating($this, $value);
        //     if($form) {
        //         $this->forms = $form;
        //     }
        // }

        // 폼입력 팝업창 활성화
        $this->popupFormOpen();
    }

    // // 오버로드
    // public function store()
    // {
    //     if(isset($this->forms['code'])) {
    //         $code = $this->forms['code'];
    //         $path = base_path('theme/'.$code);
    //         if(!is_dir($path)) mkdir($path,0777,true);
    //     }

    //     parent::store();
    // }


    public function store()
    {
        $this->message = null;

        // 1.유효성 검사
        if (isset($this->actions['validate'])) {
            $validator = Validator::make(
                $this->forms,
                $this->actions['validate'])
                ->validate();
        }

        // 2. 시간정보 생성
        $this->forms['created_at'] = date("Y-m-d H:i:s");
        $this->forms['updated_at'] = $this->forms['created_at'];

        // 3. 파일 업로드 체크 Trait
        $this->fileUpload();

        $form = $this->forms;

        // 4. 컨트롤러 메서드 호출
        // 신규 데이터 DB 삽입전에 호출되는 Hook
        // if ($controller = $this->isHook("hookStoring")) {
        //     $_form = $this->controller->hookStoring($this, $this->forms);
        //     if(is_array($_form)) {
        //         $form = $_form;
        //     } else {
        //         // 훅 처리시 오류가 발생됨.
        //         // $this->message = $_form;
        //         return null;
        //     }
        // } else {
        //     $form = $this->forms;
        // }

        // 5. 데이터 삽입
        if(count($form)>0) { // 삽입된 데이터가 있는 경우에만 처리

            $id = DB::table($this->actions['table'])->insertGetId($form);
            $form['id'] = $id;
            $this->last_id = $id;

            // 6. 컨트롤러 메서드 호출
            // if ($controller = $this->isHook("hookStored")) {
            //     $controller->hookStored($this, $form);
            // }
        }

        // 입력데이터 초기화
        $this->cancel();

        // 팝업창 닫기
        $this->popupFormClose();
    }


    public function edit($id)
    {
        // 수정기능이 비활성화 되어 있는지 확인
        if(!isset($this->actions['edit']['enable'])) {
            return false;
        } else {
            if(!$this->actions['edit']['enable']) {
                return false;
            }
        }

        $this->message = null;

        if($id) {
            $this->actions['id'] = $id;
        }

        // 1. 컨트롤러 메서드 호출
        // if ($controller = $this->isHook("hookEditing")) {
        //     $this->forms = $this->controller->hookEditing($this, $this->forms);
        // }

        if (isset($this->actions['id'])) {
            $row = DB::table($this->actions['table'])
                ->find($this->actions['id']);
            $this->setForm($row);
        }

        // 2. 수정 데이터를 읽어온후, 값을 처리해야 되는 경우
        // if ($controller = $this->isHook("hookEdited")) {
        //     $this->forms = $this->controller->hookEdited($this, $this->forms, $this->forms);
        // }

        $this->popupFormOpen();
    }

    // Object를 Array로 변경합니다.
    private function setForm($row)
    {
        foreach ($row as $key => $value) {
            $this->forms[$key] = $value;
            // 데이터 변경여부를 체크하기 위해서 old 값 지정
            $this->forms_old[$key] = $value;
        }
    }

    public function update()
    {
        // step1. 수정전, 원본 데이터 읽기
        $origin = DB::table($this->actions['table'])->find($this->actions['id']);
        foreach ($origin as $key => $value) {
            $this->forms_old[$key] = $value;
        }

        // step2. 유효성 검사
        if (isset($this->actions['validate'])) {
            $validator = Validator::make($this->forms, $this->actions['validate'])->validate();
        }

        // step3. 컨트롤러 메서드 호출
        // if ($controller = $this->isHook("hookUpdating")) {
        //     $_form = $this->controller->hookUpdating($this, $this->forms, $this->forms_old);
        //     if(is_array($_form)) {
        //         $this->forms = $_form;
        //     } else {
        //         // Hook에서 오류가 반환 되었습니다.
        //         return null;
        //     }
        // }


        // step4. 파일 업로드 체크 Trait
        $this->fileUpload();


        // uploadfile 필드 조회
        /*
        $fields = DB::table('uploadfile')->where('table', $this->actions['table'])->get();
        foreach($fields as $item) {
            $key = $item->field; // 업로드 필드명
            if($origin->$key != $this->forms[$key]) {
                ## 이미지를 수정하는 경우, 기존 이미지는 삭제합니다.
                Storage::delete($origin->$key);
            }
        }
        */


        // step5. 데이터 수정
        if($this->forms) {
            $this->forms['updated_at'] = date("Y-m-d H:i:s");

            DB::table($this->actions['table'])
                ->where('id', $this->actions['id'])
                ->update($this->forms);
        }

        // step6. 컨트롤러 메서드 호출
        // if ($controller = $this->isHook("hookUpdated")) {
        //     $this->forms = $this->controller->hookUpdated($this, $this->forms, $this->forms_old);
        // }

        // 입력데이터 초기화
        $this->cancel();

        // 팝업창 닫기
        $this->popupFormClose();
    }


    /** ----- ----- ----- ----- -----
     *  데이터 삭제
     *  삭제는 2단계로 동작합니다. 삭제 버튼을 클릭하면, 실제 동작 버튼이 활성화 됩니다.
     */
    public function delete($id=null)
    {
        $this->popupDelete = true;
    }

    public function deleteCancel()
    {
        $this->popupDelete = false;
    }

    public function deleteConfirm()
    {
        $this->popupDelete = false;

        $row = DB::table($this->actions['table'])
            ->find($this->actions['id']);
        $form = [];
        foreach($row as $key => $value) {
            $form[$key] = $value;
        }

        // 컨트롤러 메서드 호출
        // if ($controller = $this->isHook("hookDeleting")) {
        //     $row = $this->controller->hookDeleting($this, $form);
        // }

        // uploadfile 필드 조회
        /*
        $fields = DB::table('uploadfile')->where('table', $this->actions['table'])->get();
        foreach($fields as $item) {
            $key = $item->field; // 업로드 필드명
            if (isset($row->$key)) {
                Storage::delete($row->$key);
            }
        }
        */

        // 데이터 삭제
        DB::table($this->actions['table'])
            ->where('id', $this->actions['id'])
            ->delete();

        // 컨트롤러 메서드 호출
        // if ($controller = $this->isHook("hookDeleted")) {
        //     $row = $this->controller->hookDeleted($this, $form);
        // }

        // 입력데이터 초기화
        $this->cancel();
        unset($this->actions['id']);


        // 팝업창 닫기
        $this->popupFormClose();
        $this->popupDelete = false;

        $this->dispatch('history-back');

    }
}
