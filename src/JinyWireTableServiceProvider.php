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


        // 화면 UI
        Blade::component($this->package.'::components.table.'.'wire-table', 'wire-table');
        Blade::component($this->package.'::components.table.'.'wire-thead', 'wire-thead');
        //Blade::component($this->package.'::components.'.'wire-tbody', 'wire-tbody');
        Blade::component(\Jiny\WireTable\View\Components\WireTbody::class, "wire-tbody");
        Blade::component(\Jiny\WireTable\View\Components\WireTbodyItem::class, "wire-tbody-item");
        Blade::component(\Jiny\WireTable\View\Components\WireTableTh::class, "wire-th");

        Blade::component(\Jiny\WireTable\View\Components\WireGridItem::class, "wire-grid-item");

        // breadcrumb-item
        Blade::component($this->package.'::components.breadcrumb.items', 'breadcrumb-item');

        // buttons
        Blade::component($this->package.'::components.buttons.btn_video', 'btn-video');
        Blade::component($this->package.'::components.buttons.btn_manual', 'btn-manual');


        // 팝업 Dialog
        Blade::component($this->package.'::components.'.'dialog-modal', 'wire-dialog-modal');
        Blade::component($this->package.'::components.'.'dialog-modal', 'dialog-modal');
        Blade::component($this->package.'::components.'.'modal', 'wire-modal');
        Blade::component($this->package.'::components.'.'modal', 'modal');

        // 팝업 Dialog
        Blade::component($this->package.'::components.'.'dialog-modal', 'table-dialog-modal');
        Blade::component($this->package.'::components.'.'modal', 'table-modal');



        // javascript emit 버튼
        Blade::component($this->package.'::components.'.'wire.create', 'btn-wireCreate');
        Blade::component($this->package.'::components.'.'wire.manual', 'btn-wireManual');

        Blade::component($this->package.'::components.'.'popupFormCreate', 'popupFormCreate');

        // 라이브와이어 동작을 표시하기 위한 인디케이터
        Blade::component($this->package.'::components.'.'loading-indicator', 'loading-indicator');
        Blade::component($this->package.'::components.'.'upload-indicator', 'upload-indicator');

    }

    public function register()
    {
        /* 라이브와이어 컴포넌트 등록 */
        $this->app->afterResolving(BladeCompiler::class, function () {

            Livewire::component('WireTable', \Jiny\WireTable\Http\Livewire\WireTable::class);
            // 팝업 form
            Livewire::component('WirePopupForm', \Jiny\WireTable\Http\Livewire\WirePopupForm::class);
            //Livewire::component('PopupForm', \Jiny\Table\Http\Livewire\PopupForm::class); // 팝업형


            Livewire::component('ButtonPopupCreate',
                \Jiny\WireTable\Http\Livewire\ButtonPopupCreate::class);

            // WireTable + popupForm을 결합한 통합본
            Livewire::component('WireTable-PopupForm',
                \Jiny\WireTable\Http\Livewire\WireTablePopupForm::class);


        });
    }

}
