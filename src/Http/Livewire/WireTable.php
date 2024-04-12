<?php
namespace Jiny\WireTable\Http\Livewire;

use Illuminate\Contracts\Container\Container;
use Illuminate\Routing\Route;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

class WireTable extends Component
{
    use WithPagination;
    use \Jiny\WireTable\Http\Trait\Hook;
    use \Jiny\WireTable\Http\Trait\Permit;
    use \Jiny\WireTable\Http\Trait\CheckDelete;
    use \Jiny\WireTable\Http\Trait\DataFetch;

    public $actions;
    public $paging = 10;
    public $admin_prefix;
    public $message;

    // 추출된 데이터 목록 (array)
    public $data=[];
    //public $_temp = [];
    public $table_columns=[];


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

        // 테이블 컬럼 정보읽기
        if(isset($this->actions['table']) && $this->actions['table']) {
            $columns = DB::select("SHOW COLUMNS FROM ".$this->actions['table']);
            foreach ($columns as $column) {
                $this->table_columns []= $column;
                //echo "Column: $column->Field, Type: $column->Type\n";
            }
        }


    }


    /** ----- ----- ----- ----- -----
     *  Table
     */
    public function render()
    {
        // livewire 컴포넌트를 다른 Blade와 공유를 위한 인스턴스 저장
        wireShare()->wire = $this;

        // 1. 데이터 테이블 체크
        if(isset($this->actions['table'])) {
            if($this->actions['table']) {
                $this->setTable($this->actions['table']);
            }
        } else {
            // 테이블명이 없는 경우
            return view("jiny-wire-table::errors.message",[
                'message' => "WireTable 테이블명이 지정되어 있지 않습니다."
            ]);
        }


        // 2. 후킹 :: 컨트롤러 메서드 호출
        if ($controller = $this->isHook("HookIndexing")) {
            $result = $controller->HookIndexing($this);
            if($result) {
                // 반환값이 있는 경우, 출력하고 이후동작을 중단함.
                return $result;
            }
        }

        // 3. 데이터를 읽어 옵니다.
        $rows = $this->dataFetch($this->actions);

        $totalPages = $rows->lastPage();
        $currentPage = $rows->currentPage();


        // 4. 후크 :: 읽어온 데이터를 후작업 합니다.
        if($rows) {
            if ($controller = $this->isHook("HookIndexed")) {
                $rows = $controller->HookIndexed($this, $rows);

                if(is_null($rows)) {
                    return view("jiny-wire-table::error.message",[
                        'message'=>"HookIndexed() 호출 반환값이 없습니다."
                    ]);
                }
            }
        }


        // 5. 내부함수 생성
        // 팝업창 폼을 활성화 합니다.
        $funcEditPopup = function ($item, $title)
        {
            // emit -> $this->dispatch('popupFormCreate');
            $link = xLink($title)->setHref("javascript: void(0);");
            $link->setAttribute("wire:click", "$"."emit('popupEdit','".$item->id."')");

            if (isset($item->enable)) {
                if($item->enable) {
                    return $link;
                } else {
                    return xSpan($link)->style("text-decoration:line-through;");
                }
            }

            return $link;
        };

        // 내부함수 생성
        // form 페이지로 url을 이동합니다.
        $rules = $this->actions;
        $funcEditLink = function ($item, $title) use ($rules)
        {
            $link = ($title)->setHref(route($rules['routename'].".edit", $item->id));
            if($item->enable) {
                return $link;
            } else {
                return xSpan($link)->style("text-decoration:line-through;");
            }
            return $link;
        };

        // 6. 출력 레이아아웃
        $view_layout = $this->getViewMainLayout();

        $this->toData($rows);


        return view($view_layout,[
            'rows'=>$rows,
            'popupEdit'=>$funcEditPopup,
            'editLink'=>$funcEditLink,
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

    private function getViewMainLayout()
    {
        $view = "jiny-wire-table::livewire.wiretable"; // 기본값

        if(isset($this->actions['view']['main_layout'])) {
            if($this->actions['view']['main_layout']) {
                $view = $this->actions['view']['main_layout'];
            }
        }

        if(isset($this->actions['view']['table'])) {
            if($this->actions['view']['table']) {
                $view = $this->actions['view']['table'];
            }
        }

        // 기본값
        return $view ;
    }


    /* ----- ----- ----- ----- ----- */

    protected $listeners = ['refeshTable'];
    public function refeshTable()
    {
        // 페이지를 재갱신 합니다.
    }

    public function edit($id)
    {
        //dd($id);
        //$this->emit('popupEdit',$id);
        $this->dispatch('popupFormEdit',$id);
    }

    public function create()
    {
        $this->dispatch('popupFormCreate');
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
        }

        return $this->actions['request'];
    }

}
