<?php

namespace App\View\Components;

use App\Models\Tag;
use Illuminate\View\Component;

class TagsMenu extends Component
{
    public $tags;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->tags = Tag::all();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('website.layouts.components.tags-menu');
    }
}
