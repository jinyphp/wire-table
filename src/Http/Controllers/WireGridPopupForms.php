<?php
namespace Jiny\WireTable\Http\Controllers;

use Jiny\WireTable\Http\Controllers\LiveController;
class WireGridPopupForms extends LiveController
{
    public function __construct()
    {
        parent::__construct();

        $this->viewFileLayout = "jiny-wire-table"."::grids.admin";
        //$this->viewFileTable = $this->packageName."::grids.table";

    }


}
