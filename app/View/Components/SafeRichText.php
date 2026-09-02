<?php

namespace App\View\Components;

use App\Support\AllowedInlineHtml;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class SafeRichText extends Component
{
    public function __construct(
        private AllowedInlineHtml $allowedInlineHtml,
        public ?string $value = null,
    ) {}

    public function render(): View
    {
        return view('components.safe-rich-text', [
            'html' => $this->allowedInlineHtml->render($this->value),
        ]);
    }
}
