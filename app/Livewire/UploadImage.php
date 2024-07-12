<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;


class UploadImage extends Component
{
    use WithFileUploads;
    public $file = "";

    public function render()
    {
        return view('livewire.upload-image');
    }

    public function uploadImage()
    {
        dd($this->file);
    }
}
