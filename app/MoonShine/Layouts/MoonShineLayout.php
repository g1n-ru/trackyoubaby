<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use MoonShine\ColorManager\Palettes\NeutralPalette;
use MoonShine\Laravel\Layouts\AppLayout;

final class MoonShineLayout extends AppLayout
{
    protected ?string $palette = NeutralPalette::class;

    protected function menu(): array
    {
        return $this->autoloadMenu();
    }

    protected function getFooterCopyright(): string
    {
        return '';
    }

    protected function getFooterMenu(): array
    {
        return [];
    }
}
