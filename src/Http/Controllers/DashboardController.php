<?php
namespace Jiny\WireTable\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;

use Jiny\WireTable\Http\Controllers\BaseController;
class DashboardController extends BaseController
{
    use \Jiny\WireTable\Http\Trait\Permit;
    //use \Jiny\Table\Http\Controllers\SetMenu;

    protected function checkRequestNesteds($request)
    {
        if (isset($this->actions['nesteds'])) {
            foreach($this->actions['nesteds'] as $i => $nested) {
                if(isset($request->$nested)) {
                    unset($this->actions['nesteds'][$i]);
                    $this->actions['nesteds'][$nested] = $request->$nested;
                    $this->actions['request']['nesteds'][$nested] = $request->$nested;
                }
            }
        }

        return $this;
    }

    // Request에서 전달된 query 스트링값을 저장합니다.
    protected function checkRequestQuery($request)
    {
        if($request->query) {
            foreach($request->query as $key => $q) {
                $this->actions['request']['query'][$key] = $q;
            }
        }
        return $this;
    }

    ## Dashboard Index
    public function index(Request $request)
    {
        $this->checkRequestNesteds($request);
        $this->checkRequestQuery($request);


        // 사용자 메뉴 설정
        $user = Auth::user();
        if($user) {
            $this->setUserMenu($user);
        }


        // 권한
        $this->permitCheck();
        if($this->permit['read']) {

            // 메인뷰 페이지...
            if (isset($this->actions['view']['main'])) {
                if (view()->exists($this->actions['view']['main']))
                {
                    $view = $this->actions['view']['main'];
                } else {
                    $view = "jiny-wire-table::dashboard.main";
                }
            } else {
                $view = "jiny-wire-table::dashboard.main";
            }

            return view($view,[
                'actions'=>$this->actions,
                'request'=>$request
            ]);
        }



        // 권한 접속 실패
        return view("jiny-wire-table::error.permit",[
            'actions'=>$this->actions,
            'request'=>$request
        ]);
    }


}
