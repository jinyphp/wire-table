<?php
namespace Jiny\WireTable\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


use Jiny\WireTable\Http\Controllers\LiveController;
class WireDashController extends LiveController
{
    public function __construct()
    {
        parent::__construct();

        $this->viewFileLayout = $this->packageName."::dash.admin";
        //$this->viewFileTable = $this->packageName."::table_popup_forms.table";

    }

    // 레이아웃을 admin 또는 www로 변경할 수 있습니다.
    protected function setLayout($type="www")
    {
        if($type) {
            $this->viewFileLayout = $this->packageName."::dash.".$type;
        }

        return $this;
    }


    /**
     * Process
     *
     */
    public function index(Request $request)
    {
        // 1.IP확인
        $ipAddress = $request->ip();
        $this->actions['request']['ip'] = $ipAddress;

        // 2. request로 전달되는 uri 파라미터값을 분석합니다.
        $this->checkRequestNesteds($request);

        // 3. request로 전달되는 uri 쿼리스트링을 확인합니다.
        $this->checkRequestQuery($request);

        // 4.테마확인
        if(isset($this->actions['theme'])) {
            if($this->actions['theme']) {
                if(function_exists("setTheme")) {
                    setTheme($this->actions['theme']);

                    // 레이아웃 적용을 테마로 설정합니다.
                    $this->viewFileLayout = $this->packageName."::theme.dash";
                }
            }
        }

        // 5.로그인: 사용자 메뉴 설정
        $user = Auth::user();
        if($user) {
            //$this->setUserMenu($user);
        }

        ## 6.권한
        $this->permitCheck();
        if($this->permit['read']) {

            $view = $this->getViewFileLayout();
            if (view()->exists($view)) {
                $_data = [
                    'actions'=>$this->actions,
                    'nested'=>$this->nested,
                    'request'=>$request
                ];

                return view($view, $_data);
            }

            return view($this->packageName."::errors.message",[
                'message' => $view."를 읽어올수 없습니다."
            ]);
        }

        ## 권한 접속 실패
        return view("jiny-wire-table::error.permit",[
            'actions'=>$this->actions,
            'request'=>$request
        ]);
    }
}
