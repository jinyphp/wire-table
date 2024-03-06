<?php
namespace Jiny\WireTable;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Compilers\BladeCompiler;

use Livewire\Livewire;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;

class JinyWireTableServiceProvider extends ServiceProvider
{
    private $package = "jiny-wire-table";

    public function boot()
    {
        // 모듈: 라우트 설정
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', $this->package);

        // Blade::component($this->package.'::components.'.'loading-indicator', 'loading-indicator');


    }

    public function register()
    {
        /* 라이브와이어 컴포넌트 등록 */
        $this->app->afterResolving(BladeCompiler::class, function () {

        });
    }

}
