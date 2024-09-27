<?php

namespace Jiny\WireTable\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;

class BaseController extends Controller
{
    // 리소스 저장경로
    const PATH = "actions";
    //protected $actions = [];
    public $actions = [];

    public function __construct()
    {
        // 정적변수에 현재의 객체를 할당하여 공유한다.
        // self::$Instance = $this;
        //wireShare()->controller = $this;

        ## 라우트정보
        $this->detectURI();
        $this->detectRouteName();

        // Actions 기본값 설정
        $this->actions['paging'] =  "10"; // 리스트 한페이지당 출력갯수

        // 라라벨 config 정보 확인
        $conf = config("jiny_table.path");
        $path = resource_path( $conf['path'] ?? self::PATH);
        foreach ($this->readJsonAction($path) as $key => $value)
        {
            // Json Actions 정보를 반영
            $this->actions[$key] = $value;
        }

        // 기본값
        $this->actions['create']['enable'] = true;
        $this->actions['create']['title'] = "추가";

        // 선택삭제 활성화
        $this->actions['delete']['check'] = true;

        // 수정기능 활성화
        $this->actions['edit']['enable'] = true;

    }

    ## Actions.json 파일을 우선으로 처리하고,
    ## 값이 없는 경우 기본값으로 변경
    protected function setReflectActions($actions, &$arr=null)
    {
        if(is_null($arr)) {
            $arr = &$this->actions;
        }

        foreach($actions as $key => $value) {
            if(is_array($value)) {
                if(!isset($arr[$key])) {
                    $arr[$key] = [];
                }
                //dump($arr[$key]);
                $this->setReflectActions($actions[$key], $arr[$key]);
            } else {
                if(!isset($arr[$key])) {
                    $arr[$key] = $value;
                }
            }
        }
    }

    // 생성버튼을 비활성화 합니다.
    protected function createDisable()
    {
        unset($this->actions['create']);
    }

    protected function createEnable($title="추가")
    {
        $this->actions['create']['enable'] = true;
        $this->actions['create']['title'] = $title;

        return $this;
    }

    protected function checkDeleteDisable()
    {
        unset($this->actions['delete']['check']);
    }

    protected function editDisable()
    {
        $this->actions['edit']['enable'] = false;
    }



    private function detectURI()
    {
        // 라우터에서 uri 정보 확인
        $uri = Route::current()->uri;

        // uri에서 {} 매개변수 제거
        $slug = explode('/', $uri);
        $_slug = [];
        foreach($slug as $key => $item) {
            // root같은 경우 값이 없는 경우도 있음,
            // 길이가 0보다 큰 경우에만 검사
            if(strlen($item)>0) {
                if($item[0] == "{") {
                    $param = substr($item, 1, strlen($item)-2);
                    $param = trim($param,'?');
                    //dump($param);
                    $this->actions['nesteds'] []= $param;

                    continue; //unset($slug[$key])
                }
                $_slug []= $item;
            }
        }


        // resource 컨트롤러에서 ~/create 는 삭제.
        $last = count($_slug)-1;
        if(isset($_slug[$last])) {
            if($_slug[$last] == "create" ||  $_slug[$last] == "edit") {
                unset($_slug[$last]);
            }
        }


        $slugPath = implode("/",$_slug); // 다시 url 연결.

        // Actions 정보를 설정함
        $this->actions['route']['uri'] = $slugPath;

        //dd($slugPath);

        return $this;
    }

    private function detectRouteName()
    {
        $routename = Route::currentRouteName();

        // 마지막 method 라우터 이름은 제외
        $this->actions['routename'] = substr($routename,0,strrpos($routename,'.'));
        $this->actions['route']['name'] = $this->actions['routename'];

        return $this;
    }

    ## json 파일을 확인하고, 읽기
    private function readJsonAction($path)
    {
        $filename = $path.DIRECTORY_SEPARATOR;
        //$filename .= str_replace("/","_",$this->actions['route']['uri']).".json";
        $uri = $this->actions['route']['uri'];
        $uri = rtrim($uri, '/');
        $uri = str_replace('/', DIRECTORY_SEPARATOR, $uri);
        $filename .= $uri.".json";

        //dd($filename);

        if (file_exists($filename)) {
            $json = file_get_contents($filename);
            return json_decode($json, true);
        }

        return [];
    }

    public function getActions()
    {
        return $this->actions;
    }


    /**
     * 싱글턴, 라이브와이어와 컨트롤러 연결
     */
    protected static $Instance;


    public $wire;
    /*
    public static function getInstance($wire)
    {
        self::$Instance->wire = $wire;
        return self::$Instance;
    }
    */



    ## 컨트롤러를 통하여 호출시,
    ## 양방향 의존성 설정을 위한 컨트롤러 명 설정
    protected function setVisit($obj)
    {
        if ($obj && is_object($obj)) {
            $this->actions['controller'] = $obj::class;
            self::$Instance = $obj;
        }

        return $this;
    }

    protected function setUploadAfterMoveTo($path)
    {
        $this->actions['upload']['move'] = $path;
        $this->actions['upload']['path'] = "/";

        return $this;
    }

    /**
     * 레이아웃을 설정합니다.
     */
    protected function setLayout($path, $default=null)
    {
        $this->actions['view']['layout'] = $path;
        //Action()->set('view.layout', $path);

        // 기본값
        if($default) {
            $this->actions['view']['_layout'] = $default;
        }

        return $this;
    }

    protected function setLayoutDefault($default)
    {
        $this->actions['view']['_layout'] = $default;

        return $this;
    }

}
