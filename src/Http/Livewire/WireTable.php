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

class WireTable extends Component
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


        // 2. 후킹_before :: 컨트롤러 메서드 호출
        // DB 데이터를 조회하는 방법들을 변경하려고 할때 유용합니다.
        if ($controller = $this->isHook("HookIndexing")) {
            $result = $this->controller->hookIndexing($this);
            if($result) {
                // 반환값이 있는 경우, 출력하고 이후동작을 중단함.
                return view("jiny-wire-table::errors.message",[
                    'message' => $result
                ]);
            }
        }


        // 3. DB에서 데이터를 읽어 옵니다.
        $rows = $this->dataFetch($this->actions);
        $totalPages = $rows->lastPage();
        $currentPage = $rows->currentPage();


        // 4. 후킹_after :: 읽어온 데이터를 별도로
        // 추가 조작이 필요한 경우 동작 합니다. (단, 데이터 읽기가 성공한 경우)
        if($rows) {
            if ($controller = $this->isHook("HookIndexed")) {
                $rows = $this->controller->hookIndexed($this, $rows);
                if(is_array($rows) || is_object($rows)) {
                    // 반환되는 Hook 값은, 배열 또는 객체값 이어야 합니다.
                    // 만일 오류를 발생하고자 한다면, 다른 문자열 값을 출력합니다.
                } else {
                    return view("jiny-wire-table::error.message",[
                        'message'=>"HookIndexed() 호출 반환값이 없습니다."
                    ]);
                }
            }
        }

        $this->toData($rows); // rows를 data 배열에 복사해 둡니다.

        // 6. 출력 레이아아웃
        $view_layout = $this->getViewMainLayout();
        return view($view_layout,[
            'rows'=>$rows,
            'totalPages'=>$totalPages,
            'currentPage'=>$currentPage
        ]);

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
        $view = "jiny-wire-table"."::wiretable.table"; // 기본값

        // 사용자가 지정한 table 레이아웃이 있는 경우 적용!
        if(isset($this->actions['view']['table'])) {
            if($this->actions['view']['table']) {
                $view = $this->actions['view']['table'];
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

}
