<?php

namespace YousefAman\ModalRepeater;

use Closure;
use Illuminate\Support\Arr;

class Column
{
    protected string|Closure|null $label = null;

    protected ?Closure $formatUsing = null;

    protected ?string $width = null;

    protected bool $isBoolean = false;

    protected bool $isBadge = false;

    protected ?string $badgeColor = null;

    protected array|Closure $extraAttributes = [];

    protected array|Closure $headerAttributes = [];

    protected array|Closure $cellAttributes = [];

    public function __construct(
        protected string $name,
    ) {}

    public static function make(string $name): static
    {
        return new static($name);
    }

    public function label(string|Closure $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function formatUsing(Closure $callback): static
    {
        $this->formatUsing = $callback;

        return $this;
    }

    public function width(string $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function boolean(): static
    {
        $this->isBoolean = true;

        return $this;
    }

    public function badge(?string $color = null): static
    {
        $this->isBadge = true;
        $this->badgeColor = $color;

        return $this;
    }

    public function money(string $currency): static
    {
        $this->formatUsing = fn ($value) => $value !== null
            ? number_format((float) $value, 2).' '.$currency
            : '-';

        return $this;
    }

    public function extraAttributes(array|Closure $attributes): static
    {
        $this->extraAttributes = $attributes;

        return $this;
    }

    public function headerAttributes(array|Closure $attributes): static
    {
        $this->headerAttributes = $attributes;

        return $this;
    }

    public function cellAttributes(array|Closure $attributes): static
    {
        $this->cellAttributes = $attributes;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        $label = $this->label;

        if ($label instanceof Closure) {
            return ($label)();
        }

        return $label ?? $this->name;
    }

    public function getWidth(): ?string
    {
        return $this->width;
    }

    public function isBoolean(): bool
    {
        return $this->isBoolean;
    }

    public function isBadge(): bool
    {
        return $this->isBadge;
    }

    public function getBadgeColor(): ?string
    {
        return $this->badgeColor;
    }

    public function getHeaderAttributes(): array
    {
        return array_replace_recursive(
            $this->getExtraAttributes(),
            $this->evaluateAttributes($this->headerAttributes),
        );
    }

    public function getExtraAttributes(): array
    {
        return $this->evaluateAttributes($this->extraAttributes);
    }

    private function evaluateAttributes(array|Closure $attributes): array
    {
        if ($attributes instanceof Closure) {
            $attributes = ($attributes)();
        }

        return Arr::wrap($attributes);
    }

    public function getCellAttributes(): array
    {
        return array_replace_recursive(
            $this->getExtraAttributes(),
            $this->evaluateAttributes($this->cellAttributes),
        );
    }

    public function resolveValue(mixed $value): mixed
    {
        if ($this->formatUsing) {
            return ($this->formatUsing)($value);
        }

        return $value;
    }
}
