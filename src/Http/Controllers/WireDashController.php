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
        $this->setLayoutDefault($this->packageName."::dash.admin");
    }

    public function index(Request $request)
    {
        return parent::index($request);
    }
}
