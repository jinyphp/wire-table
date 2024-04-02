<?php
namespace Jiny\WireTable\Http\Trait;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait DataFetch
{
    /** ----- ----- ----- ----- -----
     *  Read Data
     */
    public $dataType = "table";
    protected $dbSelect;
    protected $_db;

    // 테이블을 지정합니다.
    public function setTable($table)
    {
        $this->_db = DB::table($table);
        return $this;
    }

    public function database()
    {
        return $this->_db;
    }

    public function db()
    {
        return $this->_db;
    }



    protected function dataFetch($actions)
    {
        $_db = $this->_db;
        if($_db) {

            // DB 검색조건 적용
            if(isset($actions['where'])) {
                if(is_array($actions['where'])) {
                    foreach($actions['where'] as $key => $value) {
                        if(!is_numeric($key)) {
                            $this->_db->where($key, $value);
                        }
                    }
                }
            }

            // Form에서 사용자 필터 조건을 적용한 경우
            // where 조건 추가
            foreach ($this->filter as $key => $filter) {
                $_db->where($key,'like','%'.$filter.'%');
            }

            // 쿼리스트링으로 filter를 지정한 경우
            if(isset($this->actions['filter'])) {
                foreach($this->actions['filter'] as $key => $value) {
                    if(isset($value[0]) && $value[0] == '>') {
                        if(isset($value[1]) && $value[1] == '=') {
                            $_db->where($key,'>=',substr($value,2));
                        } else {
                            $_db->where($key,'>',substr($value,1));
                        }

                    } else if(isset($value[0]) && $value[0] == '<') {
                        if(isset($value[1]) && $value[1] == '=') {
                            $_db->where($key,'<=',substr($value,2));
                        } else {
                            $_db->where($key,'<',substr($value,1));
                        }

                    } else if(isset($value[0]) && $value[0] == '=') {
                        $_db->where($key,'=',substr($value,1));

                    } else {
                        $_db->where($key,'like','%'.$value.'%');
                    }
                }
            }


            // 3.3 Sort
            if (empty($this->sort)) {
                $_db->orderBy('id',"desc");
            } else {
                foreach($this->sort as $key => $value) {
                    $_db->orderBy($key, $value);
                }
            }


            //  3.4 최종 데이터 읽기
            //  페이징이 없는 경우, 전체 읽기
            if(isset($this->paging) && is_numeric($this->paging) ) {
                $rows = $this->_db->paginate($this->paging);
            } else {
                $rows = $this->_db->get();
            }

            //  3.5
            $this->setData($rows);
            $this->setIds();

            // session()->flash('message',"데이터...");
            return $rows;
        }

        // 데이터 없음
        return [];
    }


    public function setWhere($arr)
    {
        if(isset($this->actions['where'])) {
            if(is_array($this->actions['where'])) {
                // 추가
                $this->actions['where'] []= $arr;
                return $this;
            }
        }

        // 초기화
        $this->actions['where'] = $arr;
        return $this;
    }

    public $data=[];
    protected function setData($rows)
    {
        $this->data = [];
        foreach($rows as $i => $item) {
            foreach($item as $k => $v) {
                $this->data[$i][$k] = $v;
            }
        }

        return $this;
    }

    public $ids = [];
    protected function setIds()
    {
        $this->ids = [];
        foreach($this->data as $i => $item) {
            $this->ids[$i] = $item['id'];
        }
    }

    # 컬럼 필드 정렬 적용
    public $sort=[];
    public function orderBy($key)
    {
        //dd($key);
        if (isset($this->sort[$key])) {
            // 토글
            if($this->sort[$key] == "desc") {
                $this->sort[$key] = "asc";
            } else {
                $this->sort[$key] = "desc";
            }
        } else {
            // 설정
            $this->sort[$key] = "desc";
        }
    }

    public function getOrderBy($key)
    {
        if (isset($this->sort[$key])) {
            return $this->sort[$key];
        }
    }

    private function sortClear()
    {
        $this->sort = [];
    }


    # 검색
    public $filter=[];
    public function filter_search()
    {
        // 선택항목 초기화
        $this->selectedall = false;
        $this->selected = [];

        // session()->flash('message',"데이터 검색");
    }

    public function filter_reset()
    {
        $this->filter = [];
        $this->sortClear();
    }

}
