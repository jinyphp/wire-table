<?php
namespace Jiny\WireTable\View\Components;

use Illuminate\View\Component;

class WireTbody extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $rows = wireShare()->wire->_rows;
        $selected = wireShare()->wire->selected;
        return view("jiny-wire-table::".'components.table.wire-tbody',[
            'rows' => $rows,
            'selected'=>$selected
        ]);
    }
}
