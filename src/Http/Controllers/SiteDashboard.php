<?php
namespace Jiny\WireTable\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use Jiny\WireTable\Http\Controllers\BaseController;
class SiteDashboard extends BaseController
{
    private $packageName = "jiny-wire-table";
    use \Jiny\WireTable\Http\Trait\Permit;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * WWW 리소스의 Slot Layouts을 기반으로
     * Dash보드를 생성합니다.
     */
    public function index(Request $request)
    {
        // IP확인
        $ipAddress = $request->ip();
        $this->actions['request']['ip'] = $ipAddress;

        // request로 전달되는 uri 파라미터값 분석
        $this->checkRequestNesteds($request);

        // request로 전달되는 uri 쿼리스트링 확인
        $this->checkRequestQuery($request);

        // 테마확인
        $this->checkTheme();

        // Menu 확인
        $this->checkMenu();

        // 권한
        $this->permitCheck();

        if($this->isPermitRead()) {

            // 사용자 레이아웃 우선설정
            if (isset($this->actions['view']['layout'])) {
                $view = $this->actions['view']['layout'];
            } else {
                $view = $this->packageName."::dashboard.www_layout";
            }

            if (view()->exists($view)) {
                $_data = [
                    'actions'=>$this->actions,
                    'request'=>$request
                ];

                return view($view,$_data);
            }

            return view($this->packageName."::errors.message",[
                'message' => "Dashboard layouts을 읽어올수 없습니다."
            ]);
        }

        // 권한 접속 실패
        return view("jiny-wire-table::error.permit",[
            'actions'=>$this->actions,
            'request'=>$request
        ]);
    }

    protected function checkTheme()
    {
        if(isset($this->actions['theme'])) {
            if($this->actions['theme']) {
                if(function_exists("setTheme")) {
                    setTheme($this->actions['theme']);
                }
            }
        }

        return $this;
    }

    protected function checkMenu()
    {
        // 로그인: 사용자 메뉴 설정
        $user = Auth::user();
        if($user) {
            //$this->setUserMenu($user);
        }

        return $this;
    }



    /**
     * index 뷰 리소스 파일을 설정합니다.
     */
    protected function setViewIndex($name)
    {
        $this->actions['view']['main'] = $name;
        return $this;
    }

    // 컨트롤러에 테마를 설정합니다.
    protected function setTheme($name)
    {
        $this->actions['theme'] = $name;
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

        return $this;
    }

    protected function checkRequestNesteds($request)
    {
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




}
