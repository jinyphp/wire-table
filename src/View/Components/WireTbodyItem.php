<?php

namespace Jiny\WireTable\View\Components;

use Illuminate\View\Component;

class WireTbodyItem extends Component
{
    public $item;
    public $selected;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($selected, $item)
    {
        $this->selected = $selected;
        $this->item = $item;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view("jiny-wire-table::".'components.table.wire-tbody-item',[

        ]);
    }
}
