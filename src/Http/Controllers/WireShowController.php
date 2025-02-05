<?php
namespace Jiny\WireTable\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

/**
 * 테이블에서 하나의 Row를 추출하여 상세한 정보를 출력합니다.
 */
use Jiny\WireTable\Http\Controllers\LiveController;
class WireShowController extends LiveController
{
    public function __construct()
    {
        parent::__construct();

        $this->viewFileLayout = $this->packageName."::show.admin";
        //$this->viewFileTable = $this->packageName."::table_popup_forms.table";

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
                }
            }
        }

        // 5.로그인: 사용자 메뉴 설정
        $user = Auth::user();
        if($user) {
            //$this->setUserMenu($user);
        }

        // 6.권한
        $this->permitCheck();
        if($this->permit['read']) {

            $view = $this->getViewFileLayout();
            if (view()->exists($view)) {
                $_data = [
                    'actions'=>$this->actions,
                    'nested'=>$this->nested,
                    'request'=>$request
                ];

                // 상세정보는 테이블의 특정 rows의
                // 선택된 id를 조회하여 데이터를 출력합니다.
                if(isset($this->actions['table']['name'])) {
                    if(isset($request->id)) {
                        $row = DB::table($this->actions['table']['name'])
                            ->where('id', $request->id)
                            ->first();
                        $_data['row'] = $row;
                    }
                }


                return view($view,$_data);
            }

            return view($this->packageName."::errors.message",[
                'message' => "layouts.table을 읽어올수 없습니다."
            ]);
        }

        // 권한 접속 실패
        return view("jiny-wire-table::error.permit",[
            'actions'=>$this->actions,
            'request'=>$request
        ]);
    }
}
