<?php
namespace Jiny\WireTable\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

use Jiny\WireTable\Http\Controllers\BaseController;
class LiveController extends BaseController
{
    // 화면처리
    public $viewFileLayout, $viewLayout;
    protected $viewFileTable, $viewTable;
    protected $viewFileList, $viewList;
    protected $viewFileItem, $viewItem;
    protected $viewFileTitle, $viewTitle;
    protected $viewFileForms, $viewForms;

    protected $slot;
    protected $theme;

    // view에게 추가로 넘겨주는 데이터
    protected $params = [];


    protected $packageName = "jiny-wire-table";
    use \Jiny\WireTable\Http\Trait\Permit;
    //use \Jiny\Table\Http\Controllers\SetMenu;

    // 2단계 nested table data
    //protected $nested_id;
    protected $nested = [];

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * index 뷰 리소스 파일을 설정합니다.
     */
    protected function setViewIndex($name)
    {
        $this->actions['view']['main'] = $name;
        return $this;
    }

    /**
     * CRUD Resource Process
     */
    public function index(Request $request)
    {
        // // 회원정보 및 slot
        // $auth = Auth::user();
        // if($auth) {
        //     $user = user()->set($auth); // 싱글턴 객체
        //     //Slot()->reload();
        // }



        // IP확인
        $ipAddress = $request->ip();
        $this->actions['request']['ip'] = $ipAddress;

        // request로 전달되는 uri 파라미터값을 분석합니다.
        $this->checkRequestNesteds($request);

        // request로 전달되는 uri 쿼리스트링을 확인합니다.
        $this->checkRequestQuery($request);

        // 라이브와이어로 전달될, Table Blade를 설정합니다.
        $this->setViewFileTable();


        // 테마확인
        if(isset($this->actions['theme'])) {
            if($this->actions['theme']) {
                if(function_exists("setTheme")) {
                    $this->actions['theme'] = str_replace('.',"/",$this->actions['theme']);
                    setTheme($this->actions['theme']);

                    // 레이아웃 적용을 테마로 설정합니다.
                    $this->viewFileLayout = $this->packageName."::theme.layout";
                    $this->viewLayout = $this->actions['theme'];
                }
            }
        }

        // 로그인: 사용자 메뉴 설정
        $user = Auth::user();
        if($user) {
            //$this->setUserMenu($user);
        }

        // 권한확인
        $this->permitCheck();
        if($this->permit['read']) {

            ## 테이블 레이아웃을 읽어 옵니다.
            $view = $this->getViewFileLayout();
            if($view) {
                if (view()->exists($view)) {

                    $_data = $this->mergeParams(['request'=>$request]);
                    //dd($_data);
                    return view($view, $_data);
                }

                ## 테이블 레이아웃 없는 경우
                return view($this->packageName."::errors.no_layout",[
                    'message' => "지정한 ".$view."를 읽어올수 없습니다.",
                    'actions'=>$this->actions
                ]);
            }

            ## 레이아웃 없는 경우
            return false;

            // return view($this->packageName."::errors.no_layout",[
            //     'message' => "기본 Layout view가 지정되어 있지 않습니다.",
            //     'actions'=>$this->actions
            // ]);
        }



        ## 권한 접속 실패
        ## 권환에 대한 오류 화면을 출력합니다.
        return view("jiny-wire-table::error.permit",[
            'actions'=>$this->actions,
            'request'=>$request
        ]);
    }


    private function mergeParams($data=[])
    {
        // 인자값 병합
        foreach($data as $key => $value) {
            $this->params[$key] = $value;
        }

        $this->params['actions'] = $this->actions;
        $this->params['nested'] = $this->nested;

        return $this->params;
    }


    protected function setViewFileTable()
    {
        if($this->viewTable) {
            return $this->viewTable;
        }

        if(isset($this->actions['view']['table'])) {
            return $this->actions['view']['table'];
        }

        // 사용자 테이블이 미설정 되어 있는 경우
        // if($this->viewFileTable) {
        //     $this->actions['view']['table'] = $this->viewFileTable;
        // }

        return "jiny-wire-table::table_popup_forms.table";
    }


    /**
     * 인덱스 메소드의 우선순위 view를 확인합니다.
     * 1. actions -> 절대경로 -> slot경로 -> www:: -> theme -> resources/views
     * 2. viewFileLayout 프로퍼티 ->
     */
    protected function getViewFileLayout($default=null)
    {
        if($this->viewLayout) {
            //return $this->viewLayout;
            if($res = siteViewName($this->viewLayout)){
                return $res;
            }
        }

        // 우선순위1 : actions 설정값
        if (isset($this->actions['view']['layout'])) {
            $aViewLayout = $this->actions['view']['layout'];
            if ($aViewLayout) {
                if($res = siteViewName($aViewLayout)){
                    return $res;
                }
            }
        }


        // 우선순위2 :
        // 컨트롤러 코드에서 설정한 값이 있는 경우
        if($this->viewFileLayout) {
            // 2-1:
            $viewFile = $this->viewFileLayout;
            if($res = siteViewName($viewFile)){
                return $res;
            }
        }


        // 우선순위3 :
        if($default) {
            $this->actions['view']['_layout'] = $default;
        }
        if(isset($this->actions['view']['_layout'])) {
            return $this->actions['view']['_layout'];
        }


        return false;
    }



    /**
     * 컨트롤러에 테마를 설정합니다.
     */
    protected function setTheme($name)
    {
        $this->actions['theme'] = $name;

        // 세션에 테마 저장
        session()->put('theme', $name);

        return $this;
    }

    // Request에서 전달된 query 스트링값을 저장합니다.
    protected function checkRequestQuery($request)
    {
        if($request->query) {
            foreach($request->query as $key => $q) {
                $this->actions['request']['query'][$key] = $q;
                $this->actions['query'][$key] = $q;

                // 필터검색 요건확인
                $len = strlen("filter_");
                if(strlen($key) > $len) {
                    if(substr($key,0,$len) == "filter_") {
                        $_key = substr($key,$len);
                        $this->actions['filter'][$_key] = $q;
                    }
                }
            }
        }
        //dd($this->actions);
        return $this;
    }

    protected function checkRequestNesteds($request)
    {
        //dd($this->actions['nesteds']);
        //dd($request->id);
        //dd($request);

        if (isset($this->actions['nesteds'])) {
            foreach($this->actions['nesteds'] as $i => $nested) {
                if(isset($request->$nested)) {
                    unset($this->actions['nesteds'][$i]);
                    $this->actions['nesteds'][$nested] = $request->$nested;
                    $this->actions['request'][$nested] = $request->$nested;
                }
            }
        }

        return $this;
    }



    public function show(Request $request, $id)
    {
        $this->checkRequestNesteds($request);
        $this->checkRequestQuery($request);

        // 메뉴 설정
        $user = Auth::user();
        $this->setUserMenu($user);

        // 권한
        $this->permitCheck();
        if($this->permit['read']) {

        }

        // 권한 접속 실패
        return view("jiny-wire-table::error.permit",[
            'actions'=>$this->actions,
            'request'=>$request
        ]);
    }

    public function create(Request $request)
    {
        $this->checkRequestNesteds($request);
        $this->checkRequestQuery($request);

        // 메뉴 설정
        $user = Auth::user();
        $this->setUserMenu($user);

        // 권한
        $this->permitCheck();
        if($this->permit['create']) {

            // 메인뷰 페이지...
            if (isset($this->actions['view']['edit'])) {
                $view = $this->actions['view']['edit'];
            } else {
                $view = "jiny-wire-table::edit";
            }

            return view($view,[
                'actions'=>$this->actions
            ]);
        }

        // 권한 접속 실패
        return view("jiny-wire-table::error.permit",[
            'actions'=>$this->actions,
            'request'=>$request
        ]);
    }

    public function store(Request $request)
    {
        $this->checkRequestNesteds($request);
        $this->checkRequestQuery($request);

        // 권한
        $this->permitCheck();
        if($this->permit['create']) {


        }

        // 권한 접속 실패
        return view("jiny-wire-table::error.permit",[
            'actions'=>$this->actions,
            'request'=>$request
        ]);

    }

    // public function edit(Request $request, $id)
    // {
    //     $this->checkRequestNesteds($request);
    //     $this->checkRequestQuery($request);

    //     // 메뉴 설정
    //     $user = Auth::user();
    //     $this->setUserMenu($user);

    //     // 권한
    //     $this->permitCheck();
    //     if($this->permit['update']) {
    //         // 마지막 값이, id로 간주합니다.
    //         $keyId = array_key_last($this->actions['nesteds']);
    //         $this->actions['id'] = $this->actions['nesteds'][$keyId];

    //         return view("jjiny-wire-table::edit",['actions'=>$this->actions]);
    //     }

    //     // 권한 접속 실패
    //     return view("jiny-wire-table::error.permit",[
    //         'actions'=>$this->actions,
    //         'request'=>$request
    //     ]);
    // }



    // public function update(Request $request, $id)
    // {
    //     $this->checkRequestNesteds($request);
    //     $this->checkRequestQuery($request);

    //     // 권한
    //     $this->permitCheck();
    //     if($this->permit['update']) {


    //     }

    //     // 권한 접속 실패
    //     return view("jiny-wire-table::error.permit",[
    //         'actions'=>$this->actions,
    //         'request'=>$request
    //     ]);
    // }

    // public function destroy($id, Request $request)
    // {
    //     $this->checkRequestNesteds($request);
    //     $this->checkRequestQuery($request);

    //     // 권한
    //     $this->permitCheck();
    //     if($this->permit['delete']) {
    //         // 마지막 값이, id로 간주합니다.
    //         $keyId = array_key_last($this->actions['nesteds']);
    //         $this->actions['id'] = $this->actions['nesteds'][$keyId];
    //     }

    //     // 권한 접속 실패
    //     return view("jiny-wire-table::error.permit",[
    //         'actions'=>$this->actions,
    //         'request'=>$request
    //     ]);
    // }

    /**
     * delete 선택한 항목 삭제
     *
     * @param  mixed $request
     * @return void
     */
    public function delete(Request $request)
    {
        $this->checkRequestNesteds($request);
        $this->checkRequestQuery($request);

        // 권한
        $this->permitCheck();
        if($this->permit['delete']) {

            $ids = $request->ids;
            // 선택한 항목 삭제 AJAX
            DB::table($this->tablename)->whereIn('id', $ids)->delete();
            return response()->json(['status'=>"200", 'ids'=>$ids]);

        }

        // 권한 접속 실패
        return response()->json(['status'=>"201",'message'=>"권한 설정없음"]);
    }

}
