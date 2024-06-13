<?php
namespace Jiny\WireTable;

class WireShare
{
    public $wire;
    public $controller;

    private static $Instance;

    /**
     * 싱글턴 인스턴스를 생성합니다.
     */
    public static function instance()
    {
        if (!isset(self::$Instance)) {
            // 자기 자신의 인스턴스를 생성합니다.
            self::$Instance = new self();

            self::$Instance->wire = null;
            self::$Instance->controller = null;

            return self::$Instance;
        } else {
            // 인스턴스가 중복
            return self::$Instance;
        }
    }


}
