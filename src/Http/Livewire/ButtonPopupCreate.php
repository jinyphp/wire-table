<?php
namespace Jiny\WireTable\Http\Livewire;

use Livewire\Component;

class ButtonPopupCreate extends Component
{
    public $title;

    public function render()
    {
        $viewFile = "jiny-wire-table::livewire.button_popup_create";
        return view($viewFile);
    }

    public function popupFormCreate()
    {
        //dd("aaa");
        $this->dispatch('create');
    }
}
