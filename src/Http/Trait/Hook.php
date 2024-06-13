<?php
namespace Jiny\WireTable\Http\Trait;

trait Hook
{
    private $controller;

    // 후크 메소드가 존재하는지 검사합니다.
    private function isHook($name)
    {
        // 컨트롤러 정보가 있는 경우
        if(isset($this->actions['controller'])) {
            if(!$this->controller) {
                $controllerName = $this->actions['controller'];
                $this->controller = new $controllerName;
            }

            if(method_exists($this->controller, $name)) {
                return $this->controller;
            }
        }

        return null;
    }
}
