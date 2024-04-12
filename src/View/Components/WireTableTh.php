<?php

namespace Jiny\WireTable\View\Components;

use Illuminate\View\Component;

class WireTableTh extends Component
{
    public $orderBy;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($orderBy=null)
    {
        $this->orderBy = $orderBy;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view("jiny-wire-table::".'components.table.wire-table-th',[

        ]);
    }
}
