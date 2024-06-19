<?php
namespace Jiny\WireTable\Http\Controllers;

use Jiny\WireTable\Http\Controllers\LiveController;
class WireGridPopupForms extends LiveController
{
    public function __construct()
    {
        parent::__construct();

        $this->viewFileLayout = $this->packageName."::grids.admin";
        $this->viewFileTable = $this->packageName."::grids.table";

    }

    // 레이아웃을 admin 또는 www로 변경할 수 있습니다.
    protected function setLayout($type="www")
    {
        if($type) {
            $this->viewFileLayout = $this->packageName."::grids.".$type;
        }

        return $this;
    }
}
