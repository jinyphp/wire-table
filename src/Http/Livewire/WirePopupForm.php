<?php
namespace Jiny\WireTable\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Livewire\WithFileUploads;

class WirePopupForm extends Component
{
    use WithFileUploads;
    use \Jiny\WireTable\Http\Trait\Hook;
    use \Jiny\WireTable\Http\Trait\Permit;

    use \Jiny\Table\Http\Livewire\Tabbar;

    use \Jiny\WireTable\Http\Trait\Upload;

    /**
     * LivePopupForm with AlpineJS
     */
    public $actions;
    public $forms=[], $forms_old=[];
    public $mode;
    private $controller;

    public $message;
    public $temp=[];

    public function mount()
    {
        $this->permitCheck();
    }

    public function render()
    {
        ## 팝업 레이아웃
        $viewFile = "jiny-wire-table::popup.wire-form";
        return view($viewFile);
    }

    /** ----- ----- ----- ----- -----
     *  팝업창 관리
     */
    protected $listeners = [
        'popupFormOpen','popupFormClose',
        'create','popupFormCreate',
        'edit','popupEdit','popupCreate'
    ];

    public $popupForm = false;
    public function popupFormOpen()
    {
        $this->popupForm = true;
        $this->confirm = false;
    }

    public function popupFormClose()
    {
        $this->popupForm = false;
    }



    /** ----- ----- ----- ----- -----
     *  신규 데이터 삽입
     */

    public function popupFormCreate($value=null)
    {
        // dd("popup-create");
        // create 메소드를 호출합니다.
        return $this->create($value);
    }

    public function popupCreate($value=null)
    {
        return $this->create($value);
    }

    private function formInitField()
    {
        $this->forms = [];
        return $this;
    }

    public function create($value=null)
    {
        $this->message = null;

        // 신규 삽입을 위한 데이터 초기화
        $this->formInitField();

        // 삽입 권한이 있는지 확인
        if($this->permit['create']) {
            unset($this->actions['id']);

            // 후킹:: 컨트롤러 메서드 호출
            if ($controller = $this->isHook("hookCreating")) {
                $form = $controller->hookCreating($this, $value);
                if($form) {
                    $this->forms = $form;
                }
            }

            // 폼입력 팝업창 활성화
            $this->popupFormOpen();

        } else {
            // 권한 없음 팝업을 활성화 합니다.
            //dd("create 권환이 없습니다.");
            $this->popupPermitOpen();
        }
    }

    public $last_id;
    public function store()
    {
        $this->message = null;

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
                $_form = $controller->hookStoring($this, $this->forms);
                if(is_array($_form)) {
                    $form = $_form;
                } else {
                    // 훅 처리시 오류가 발생됨.
                    return null;
                }
            } else {
                $form = $this->forms;
            }

            // 5. 데이터 삽입
            if($form) {
                //dd($form);
                $id = DB::table($this->actions['table'])->insertGetId($form);
                $form['id'] = $id;
                $this->last_id = $id;

                // 6. 컨트롤러 메서드 호출
                if ($controller = $this->isHook("hookStored")) {
                    $controller->hookStored($this, $form);
                }
            }

            //dd($form);

            // 입력데이터 초기화
            $this->cancel();

            // 팝업창 닫기
            $this->popupFormClose();

            // Livewire Table을 갱신을 호출합니다.
            $this->emit('refeshTable');

        } else {
            $this->popupPermitOpen();
        }
    }


    /**
     * 입력 데이터 취소 및 초기화
     */
    public function cancel()
    {
        $this->forms = [];
        $this->popupDelete = false;
        $this->popupForm = false;
    }


    /** ----- ----- ----- ----- -----
     *  데이터 수정
     */
    public function popupEdit($id)
    {
        $this->edit($id);
    }

    public function edit($id)
    {
        $this->message = null;

        if($this->permit['update']) {
            $this->popupFormOpen();

            if($id) {
                $this->actions['id'] = $id;
            }

            // 1. 컨트롤러 메서드 호출
            if ($controller = $this->isHook("hookEditing")) {
                $this->forms = $controller->hookEditing($this, $this->forms);
            }

            if (isset($this->actions['id'])) {
                $row = DB::table($this->actions['table'])->find($this->actions['id']);
                $this->setForm($row);
            }

            // 2. 수정 데이터를 읽어온후, 값을 처리해야 되는 경우
            if ($controller = $this->isHook("hookEdited")) {
                $this->forms = $controller->hookEdited($this, $this->forms, $this->forms);

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

    public $old=[];
    public function getOld($key=null)
    {
        if ($key) {
            return $this->old[$key];
        }
        return $this->old;
    }

    public function update()
    {
        if($this->permit['update']) {
            // step1. 수정전, 원본 데이터 읽기
            $origin = DB::table($this->actions['table'])->find($this->actions['id']);
            foreach ($origin as $key => $value) {
                $this->old[$key] = $value;
            }

            // step2. 유효성 검사
            if (isset($this->actions['validate'])) {
                $validator = Validator::make($this->forms, $this->actions['validate'])->validate();
            }

            // step3. 컨트롤러 메서드 호출
            if ($controller = $this->isHook("hookUpdating")) {
                $_form = $controller->hookUpdating($this, $this->forms, $this->old);
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
            $fields = DB::table('uploadfile')->where('table', $this->actions['table'])->get();
            foreach($fields as $item) {
                $key = $item->field; // 업로드 필드명
                if($origin->$key != $this->forms[$key]) {
                    ## 이미지를 수정하는 경우, 기존 이미지는 삭제합니다.
                    Storage::delete($origin->$key);
                }
            }


            // step5. 데이터 수정
            if($this->forms) {
                //dd($this->forms);
                $this->forms['updated_at'] = date("Y-m-d H:i:s");

                DB::table($this->actions['table'])
                    ->where('id', $this->actions['id'])
                    ->update($this->forms);
            }

            // step6. 컨트롤러 메서드 호출
            if ($controller = $this->isHook("hookUpdated")) {
                $this->forms = $controller->hookUpdated($this, $this->forms, $this->old);
            }

            // 입력데이터 초기화
            $this->cancel();

            // 팝업창 닫기
            $this->popupFormClose();

            // Livewire Table을 갱신을 호출합니다.
            $this->emit('refeshTable');
        } else {

            $this->popupPermitOpen();
        }
    }



    /** ----- ----- ----- ----- -----
     *  데이터 삭제
     *  삭제는 2단계로 동작합니다. 삭제 버튼을 클릭하면, 실제 동작 버튼이 활성화 됩니다.
     */
    public $popupDelete = false;
    public $confirm = false;
    public function delete($id=null)
    {
        if($this->permit['delete']) {
            $this->popupDelete = true;

        } else {
            //$this->popupFormClose();
            //$this->popupPermitOpen();
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
            $row = DB::table($this->actions['table'])->find($this->actions['id']);
            //dd($row);
            $form = [];
            foreach($row as $key => $value) {
                $form[$key] = $value;
            }

            // 컨트롤러 메서드 호출
            if ($controller = $this->isHook("hookDeleting")) {
                $row = $controller->hookDeleting($this, $form);
            }

            // uploadfile 필드 조회
            $fields = DB::table('uploadfile')->where('table', $this->actions['table'])->get();
            foreach($fields as $item) {
                $key = $item->field; // 업로드 필드명
                if (isset($row->$key)) {
                    Storage::delete($row->$key);
                }
            }

            // 데이터 삭제
            DB::table($this->actions['table'])
                ->where('id', $this->actions['id'])
                ->delete();

            // 컨트롤러 메서드 호출
            if ($controller = $this->isHook("hookDeleted")) {
                $row = $controller->hookDeleted($this, $form);
            }

            // 입력데이터 초기화
            $this->cancel();

            // 팝업창 닫기
            $this->popupFormClose();
            $this->popupDelete = false;

            // Livewire Table을 갱신을 호출합니다.
            $this->emit('refeshTable');

        } else {
            $this->popupFormClose();
            $this->popupPermitOpen();
        }

    }


    public function request($key=null)
    {
        if($key) {
            if(isset($this->actions['request'][$key])) {
                return $this->actions['request'][$key];
            }
        }

        return $this->actions['request'];
    }


    /**
     * 컨트롤러에서 선안한 메소드를 호출
     */
    public function hook($method, ...$args) { $this->call($method, $args); }
    public function call($method, ...$args)
    {
        if(isset($this->actions['controller'])) {
            $controller = $this->actions['controller']::getInstance($this);
            if(method_exists($controller, $method)) {
                return $controller->$method($this, $args[0]);
            }
        }
    }



}
