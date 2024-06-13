<?php
namespace Jiny\WireTable\Http\Controllers;

use Jiny\WireTable\Http\Controllers\LiveController;
class WireTablePopupForms extends LiveController
{
    public function __construct()
    {
        parent::__construct();

        $this->viewFileLayout = $this->packageName."::table_popup_forms.layout";
        $this->viewFileTable = $this->packageName."::table_popup_forms.table";

    }
}