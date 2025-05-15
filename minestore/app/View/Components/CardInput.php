<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CardInput extends Component
{
    public $type;
    public $name;
    public $icon;
    public $value;
    public $checked;
    public $list;

    public function __construct($type, $name, $icon, $value = null, $checked = false, $list = [])
    {
        $this->type = $type;
        $this->name = $name;
        $this->icon = $icon;
        $this->value = $value;
        $this->checked = $checked;
        $this->list = $list;
    }

    /**
     * Get the template/content representing the component.
     *
     * @return \Illuminate\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.card-input');
    }
}
