<?php
namespace Jiny\WireTable\Http\Trait;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

trait CheckDelete
{
    /** ----- ----- ----- ----- -----
     *  checkBox Selecting
     */
    public $selectedall = false;
    public $selected = [];
    public $selected_count = 0;

    // model.live로 selectedall 클릭시 호출됩니다.
    public function updatedSelectedall($value)
    {
        if($value) {
            $this->selected = [];
            foreach($this->ids as $i => $v) {
                $this->selected[$i] = strval($v);
            }
        } else {
            $this->selected = [];
        }
    }

    # Livewire Hook
    public function updatedSelected($value)
    {
        if(count($this->selected) == count($this->ids)) {
            $this->selectedall = true;
        } else {
            $this->selectedall = false;
        }

        // 선택된 true 갯수 확인
        $this->selected_count = count($this->selected);
    }


    # Livewire Hook
    public function updatedPaging($value)
    {
        // 페이지목록 수 변경시,
        // 기존에 선택된 체크박스는 초기화 함.
        $this->selectedall = false;
        $this->selected = [];
    }


    /** ----- ----- ----- ----- -----
     *  delete
     */

    # 선택삭제 팝업창
    public $checkDelete = false;
    public $checkDeleteConfirm = false;

    public function popupCheckDelete()
    {
        if($this->permit['delete']) {
            $this->checkDelete = true;
        } else {
            $this->popupPermitOpen();
        }
    }

    public function popupCheckDeleteClose()
    {
        // 삭제 확인창을 닫기
        $this->checkDelete = false;
        $this->checkDeleteConfirm = false;
    }

    public function checkeDeleteConfirm()
    {
        $this->checkDeleteConfirm = true;
    }

    public function checkeDeleteRun()
    {
        if($this->permit['delete']) {

            // 1.컨트롤러 메서드 호출
            if ($controller = $this->isHook("hookCheckDeleting")) {
                if(method_exists($controller, "hookCheckDeleting")) {
                    $controller->hookCheckDeleting($this->selected);
                }
            }


            // 2.uploadfile 필드 조회
            /*
            $fields = DB::table('uploadfile')->where('table', $this->actions['table'])->get();
            $rows = DB::table($this->actions['table'])->whereIn('id', $this->selected)->get();
            foreach ($rows as $row) {
                foreach($fields as $item) {
                    $key = $item->field; // 업로드 필드명
                    if (isset($row->$key)) {
                        Storage::delete($row->$key);
                    }
                }
            }
            */

            // 3.복수의 ids를 삭제합니다.
            DB::table($this->actions['table'])
                ->whereIn('id', $this->selected)->delete();



            // 4. 컨트롤러 메서드 호출
            if ($controller = $this->isHook("hookCheckDeleted")) {
                if(method_exists($controller, "hookCheckDeleted")) {
                    $controller->hookCheckDeleted($this->selected);
                }
            }


            // 5. 기존에 선택된 체크박스는 초기화 함.
            $this->selectedall = false;
            $this->selected = [];
            $this->popupCheckDeleteClose();

        } else {
            $this->popupPermitOpen();
        }
    }
}
