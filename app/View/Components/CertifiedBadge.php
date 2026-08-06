<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CertifiedBadge extends Component
{
    public string $label;

    public function __construct(string $label = 'Certifié')
    {
        $this->label = $label;
    }

    public function render()
    {
        return view('components.certified-badge');
    }
}
