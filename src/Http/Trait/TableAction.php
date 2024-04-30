<?php
namespace Jiny\WireTable\Http\Trait;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

trait TableAction
{
    // Action 정보를 통하여 컨트롤러를 초기화 합니다.
    private function initControllerByActions()
    {
        if(isset($this->actions['theme']) && $this->actions['theme']) {
            $theme = $this->actions['theme'];
            setTheme($theme);
        } else {
            $theme = "admin/sidebar";
            setTheme($theme);
        }

        //setMenu('menus/site.json');
    }

    private function getActionJson($path, $ctrl)
    {
        $filename = $this->getActionJsonPath($path, $ctrl);
        if(file_exists($filename)) {
            $json = file_get_contents($filename);
            return json_decode($json,true);
        }

        return false;
    }

    private function saveActions($path, $ctrl)
    {

    }

    private function getActionJsonPath($path, $ctrl)
    {
        $path = $path;

        $className = get_class($ctrl);

        // 역슬래시로 문자열을 분리하여 배열로 만듭니다.
        $tokens = explode('\\', $className);

        // 배열의 마지막 요소, 즉 마지막 클래스의 이름을 추출합니다.
        $lastToken = end($tokens);

        return $path."/".$lastToken.".json";
    }
}
