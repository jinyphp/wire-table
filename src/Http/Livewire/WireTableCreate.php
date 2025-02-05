<?php
namespace Jiny\WireTable\Http\Livewire;

use Illuminate\Contracts\Container\Container;
use Illuminate\Routing\Route;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

use Livewire\WithFileUploads;

/**
 * table crud
 * 단독 create 동작 처리
 */
class WireTableCreate extends Component
{
    use WithFileUploads;
    use WithPagination;
    use \Jiny\WireTable\Http\Trait\Hook;
    use \Jiny\WireTable\Http\Trait\Permit;
    use \Jiny\WireTable\Http\Trait\CheckDelete;
    use \Jiny\WireTable\Http\Trait\DataFetch;
    use \Jiny\WireTable\Http\Trait\Upload;

    public $actions;
    public $paging = 10;
    public $admin_prefix;
    public $message;

    // 추출된 데이터 목록 (array)
    public $data=[];
    //public $_temp = [];
    public $table_columns=[];

    public $_id;


    public function mount()
    {
        // admin 접속경로 prefix
        if(function_exists('admin_prefix')) {
            $this->admin_prefix = admin_prefix();
        } else {
            $this->admin_prefix = "admin";
        }

        $this->permitCheck();

        // 페이징 초기화
        if (isset($this->actions['paging'])) {
            $this->paging = $this->actions['paging'];
        }



    }


    /** ----- ----- ----- ----- -----
     *  Table
     */
    public function render()
    {
        // 1. 데이터 테이블 체크
        if(isset($this->actions['table']['name'])) {
            if($this->actions['table']['name']) {
                $this->setTable($this->actions['table']['name']);
            }
        } else {
            // 테이블명이 없는 경우
            return view("jiny-wire-table::errors.message",[
                'message' => "WireTable 테이블명이 지정되어 있지 않습니다."
            ]);
        }

        $this->message = null;

        // 신규 삽입을 위한 데이터 초기화
        $this->formInitField();

        // 삽입 권한이 있는지 확인
        if($this->permit['create']) {
            unset($this->actions['id']);

            // 후킹:: 컨트롤러 메서드 호출
            if ($controller = $this->isHook("hookCreating")) {
                $form = $this->controller->hookCreating($this, $value);
                if($form) {
                    $this->forms = $form;
                }
            }



        } else {
            // 권한 없음 팝업을 활성화 합니다.
            //dd("create 권환이 없습니다.");
            //$this->popupPermitOpen();
        }

        // 6. 출력 레이아아웃
        $view_layout = $this->getViewMainLayout();
        //$view_layout = $this->actions['view']['form'];
        return view($view_layout,[

        ]);
    }


    public $last_id;
    public function store()
    {
        $this->message = null;

        //dd($this->forms);

        // Redirect back to the previous page
        // return redirect()->back();

        // Dispatch a browser event to handle the redirect in the frontend
        $this->dispatch('redirect-back');

        if($this->permit['create']) {



            // 1.유효성 검사
            if (isset($this->actions['validate'])) {
                $validator = Validator::make($this->forms, $this->actions['validate'])->validate();
            }

            // 2. 시간정보 생성
            $this->forms['created_at'] = date("Y-m-d H:i:s");
            $this->forms['updated_at'] = date("Y-m-d H:i:s");

            // 3. 파일 업로드 체크 Trait
            $this->fileUpload();


            // 4. 컨트롤러 메서드 호출
            // 신규 데이터 DB 삽입전에 호출되는 Hook
            if ($controller = $this->isHook("hookStoring")) {
                $_form = $this->controller->hookStoring($this, $this->forms);
                if(is_array($_form)) {
                    $form = $_form;
                } else {
                    // 훅 처리시 오류가 발생됨.
                    // $this->message = $_form;
                    return null;
                }
            } else {
                $form = $this->forms;
            }

            // 5. 데이터 삽입
            if($form) {
                //dd($form);
                $id = DB::table($this->actions['table']['name'])->insertGetId($form);
                $form['id'] = $id;
                $this->last_id = $id;

                // 6. 컨트롤러 메서드 호출
                if ($controller = $this->isHook("hookStored")) {
                    $controller->hookStored($this, $form);
                }
            }

            // 입력데이터 초기화
            $this->cancel();

            // 팝업창 닫기
            // $this->popupFormClose();

            // Livewire Table을 갱신을 호출합니다.
            // $this->emit('refeshTable');

        } else {
            $this->popupPermitOpen();
        }


    }






    private function toData($rows)
    {
        $this->data = [];
        foreach($rows as $i => $item) {
            $id = $item->id;
            foreach($item as $key => $value) {
                $this->data[$id][$key] = $value;
            }
        }

        return $this;
    }

    public function getRow($id=null)
    {
        if($id) {
            return $this->data[$id];
        }

        return $this->data;
    }

    // 화면에 출력할 테이블 레이아웃을 지정합니다.
    private function getViewMainLayout()
    {
        //$view = null;
        $view = "jiny-wire-table"."::table_crud.create"; // 기본값

        // 사용자가 지정한 table 레이아웃이 있는 경우 적용!
        if(isset($this->actions['view']['create'])) {
            if($this->actions['view']['create']) {
                $view = $this->actions['view']['create'];
            }
        }

        // 기본값
        return $view ;
    }


    /**
     * Create Read Update Delete
     */
    /* ----- ----- ----- ----- ----- */

    protected $listeners = ['refeshTable'];
    public function refeshTable()
    {
        // 페이지를 재갱신 합니다.
    }

    /**
     * 팝업창 관리
     */
    public $popupForm = false;
    public $popupDelete = false;
    public $confirm = false;
    public $forms=[];
    //public $old=[];
    public $forms_old=[];


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




    private function formInitField()
    {
        $this->forms = [];
        return $this;
    }

    /**
     * 입력 데이터 취소 및 초기화
     */
    public function cancel()
    {
        $this->forms = [];
        //$this->forms_old = [];
        $this->popupForm = false;
        $this->popupDelete = false;
        $this->confirm = false;
    }


    public function create($value=null)
    {

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

        if($this->permit['update']) {
            $this->popupFormOpen();

            if($id) {
                $this->actions['id'] = $id;
            }

            // 1. 컨트롤러 메서드 호출
            if ($controller = $this->isHook("hookEditing")) {
                $this->forms = $this->controller->hookEditing($this, $this->forms);
            }

            if (isset($this->actions['id'])) {
                $row = DB::table($this->actions['table']['name'])->find($this->actions['id']);
                $this->setForm($row);
            }

            // 2. 수정 데이터를 읽어온후, 값을 처리해야 되는 경우
            if ($controller = $this->isHook("hookEdited")) {
                $this->forms = $this->controller->hookEdited($this, $this->forms, $this->forms);
            }

        } else {
            $this->popupPermitOpen();
        }
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

    public function resetForm($name=null)
    {
        if($name) {
            $this->forms[$name] = null;
        }
    }

    public function getOld($key=null)
    {
        if ($key) {
            return $this->forms_old[$key];
        }
        return $this->forms_old;
    }

    public function update()
    {
        if($this->permit['update']) {
            // step1. 수정전, 원본 데이터 읽기
            $origin = DB::table($this->actions['table']['name'])->find($this->actions['id']);
            foreach ($origin as $key => $value) {
                $this->forms_old[$key] = $value;
            }

            // step2. 유효성 검사
            if (isset($this->actions['validate'])) {
                $validator = Validator::make($this->forms, $this->actions['validate'])->validate();
            }

            // step3. 컨트롤러 메서드 호출
            if ($controller = $this->isHook("hookUpdating")) {
                $_form = $this->controller->hookUpdating($this, $this->forms, $this->forms_old);
                if(is_array($_form)) {
                    $this->forms = $_form;
                } else {
                    // Hook에서 오류가 반환 되었습니다.
                    return null;
                }
            }


            // step4. 파일 업로드 체크 Trait
            $this->fileUpload();


            // uploadfile 필드 조회
            /*
            $fields = DB::table('uploadfile')->where('table', $this->actions['table']['name'])->get();
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
                //dd($this->forms);
                $this->forms['updated_at'] = date("Y-m-d H:i:s");

                DB::table($this->actions['table']['name'])
                    ->where('id', $this->actions['id'])
                    ->update($this->forms);
            }

            // step6. 컨트롤러 메서드 호출
            if ($controller = $this->isHook("hookUpdated")) {
                $this->forms = $this->controller->hookUpdated($this, $this->forms, $this->forms_old);
            }

            // 입력데이터 초기화
            $this->cancel();

            // 팝업창 닫기
            $this->popupFormClose();

        } else {

            $this->popupPermitOpen();
        }
    }

    /** ----- ----- ----- ----- -----
     *  데이터 삭제
     *  삭제는 2단계로 동작합니다. 삭제 버튼을 클릭하면, 실제 동작 버튼이 활성화 됩니다.
     */
    public function delete($id=null)
    {
        if($this->permit['delete']) {
            $this->popupDelete = true;
        }
    }

    public function deleteCancel()
    {
        $this->popupDelete = false;
    }

    public function deleteConfirm()
    {
        $this->popupDelete = false;

        if($this->permit['delete']) {
            $row = DB::table($this->actions['table']['name'])->find($this->actions['id']);
            $form = [];
            foreach($row as $key => $value) {
                $form[$key] = $value;
            }

            // 컨트롤러 메서드 호출
            if ($controller = $this->isHook("hookDeleting")) {
                $row = $this->controller->hookDeleting($this, $form);
            }

            // uploadfile 필드 조회
            /*
            $fields = DB::table('uploadfile')->where('table', $this->actions['table']['name'])->get();
            foreach($fields as $item) {
                $key = $item->field; // 업로드 필드명
                if (isset($row->$key)) {
                    Storage::delete($row->$key);
                }
            }
            */

            // 데이터 삭제
            DB::table($this->actions['table']['name'])
                ->where('id', $this->actions['id'])
                ->delete();

            // 컨트롤러 메서드 호출
            if ($controller = $this->isHook("hookDeleted")) {
                $row = $this->controller->hookDeleted($this, $form);
            }

            // 입력데이터 초기화
            $this->cancel();

            // 팝업창 닫기
            $this->popupFormClose();
            $this->popupDelete = false;

        } else {
            $this->popupFormClose();
            $this->popupPermitOpen();
        }

    }






    /**
     * 컨트롤러에서 선안한 메소드를 호출
     */
    public function hook($method, ...$args) { $this->call($method, $args); }
    public function call($method, ...$args)
    {
        //dd($method);
        if($controller = $this->isHook($method)) {
            if(method_exists($controller, $method)) {
                return $controller->$method($this, $args[0]);
            }
        }
    }


    public function columnHidden($col_id)
    {
        $row = DB::table('table_columns')->where('id',$col_id)->first();
        if($row->display) {
            DB::table('table_columns')->where('id',$col_id)->update(['display'=>""]);
        } else {
            DB::table('table_columns')->where('id',$col_id)->update(['display'=>"true"]);
        }
    }


    //
    public function request($key=null)
    {
        if($key) {
            if(isset($this->actions['request'][$key])) {
                return $this->actions['request'][$key];
            }

            return null;
        }


        return $this->actions['request'];
    }


    /**
     * 일반 팝업관리
     */
    public $popup = false;
    public function popupOpen($id)
    {
        $this->_id = $id;
        $this->message = null;

        $this->popup = true;
    }

    public function popupClose()
    {
        $this->_id = null;
        $this->forms=[];
        $this->forms_old=[];
        $this->message = null;

        $this->popup = false;
    }
}
