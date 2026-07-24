<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Configurações do Sistema')]
class Configuracoes extends Component
{
    public function render()
    {
        return view('livewire.configuracoes');
    }
}
