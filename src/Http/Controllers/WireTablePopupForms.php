<?php
namespace Jiny\WireTable\Http\Controllers;

use Jiny\WireTable\Http\Controllers\LiveController;
class WireTablePopupForms extends LiveController
{
    public function __construct()
    {
        parent::__construct();

        //$this->viewFileLayout = $this->packageName."::table_popup_forms.admin";
        $this->setLayoutDefault($this->packageName."::table_popup_forms.admin");

        $this->viewFileTable = $this->packageName."::table_popup_forms.table";
    }

    // 레이아웃을 admin 또는 www로 변경할 수 있습니다.
    // protected function setLayout($type="www")
    // {
    //     if($type) {
    //         $this->viewFileLayout = $this->packageName."::table_popup_forms.".$type;
    //     }

    //     return $this;
    // }
}
