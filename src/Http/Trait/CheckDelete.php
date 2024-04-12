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

    # Livewire Hook

    public function updatedSelectedall($value)
    {
        //dd("aa");
        if($value) {
            $this->selected = [];
            foreach($this->ids as $i => $v) {
                $this->selected[$i] = strval($v);
            }
        } else {
            $this->selected = [];
        }
    }


    public function checkAllSelect()
    {
        if($this->selectedall == false) {
            $this->selectedall = true;
            //dd($this->ids);
            //$this->selected = [];
            foreach($this->ids as $i => $v) {
                $this->selected[$v] = 1; //strval($v);
            }
            //dd($this->selected);
        } else {
            $this->selectedall = false;
            //dd("Unselected");
            //$this->selected = [];
            foreach($this->ids as $i => $v) {
                $this->selected[$v] = 0; //strval($v);
            }
        }
    }

    # Livewire Hook
    /*
    public function updatedSelected($value)
    {
        if(count($this->selected) == count($this->ids)) {
            $this->selectedall = true;
        } else {
            $this->selectedall = false;
        }
    }
    */
    public function checkItem($id)
    {
        //dd($id);
        // 초기화
        if(!isset($this->selected[$id])) $this->selected[$id] = 0;

        if($this->selected[$id] == 1) {
            $this->selected[$id] = 0;
        } else {
            $this->selected[$id] = 1;
        }

        // 선택된 true 갯수 확인
        $this->selected_count = 0;
        foreach($this->selected as $item) {
            if($item == 1) $this->selected_count++;
        }

        // ids : 불러온 데이터 갯수
        if($this->selected_count == count($this->ids)) {
            $this->selectedall = 1;
        } else {
            $this->selectedall = 0;
        }
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
    public $popupDelete = false;

    public function popupDeleteOpen()
    {
        if($this->permit['delete']) {
            $this->popupDelete = true;
        } else {
            $this->popupPermitOpen();
        }
    }

    public function popupDeleteClose()
    {
        // 삭제 확인창을 닫기
        $this->popupDelete = false;
    }

    public function confirmDelete()
    {
        $this->checkeDelete();
    }

    public function checkeDelete()
    {
        if($this->permit['delete']) {

            // 1.컨트롤러 메서드 호출
            if(isset($this->actions['controller'])) {
                $controller = $this->actions['controller']::getInstance($this);
                if(method_exists($controller, "hookCheckDeleting")) {
                    $controller->hookCheckDeleting($this->selected);
                }
            }

            // 2.uploadfile 필드 조회
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

            // 3.복수의 ids를 삭제합니다.
            if($this->dataType == "table") {
                DB::table($this->actions['table'])->whereIn('id', $this->selected)->delete();
            } else if($this->dataType == "uri") {

            } else if($this->dataType == "file") {

            }


            // 컨트롤러 메서드 호출
            if(isset($this->actions['controller'])) {
                $controller = $this->actions['controller']::getInstance($this);
                if(method_exists($controller, "hookCheckDeleted")) {
                    $controller->hookCheckDeleted($this->selected);
                }
            }

            // 4.페이지목록 수 변경시,
            // 기존에 선택된 체크박스는 초기화 함.
            $this->selectedall = false;
            $this->selected = [];

            $this->popupDeleteClose();

        } else {
            $this->popupPermitOpen();
        }
    }
}
