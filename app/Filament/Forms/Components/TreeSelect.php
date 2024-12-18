<?php

namespace Filament\Forms\Components;

use CodeWithDennis\FilamentSelectTree\SelectTree as BaseSelectTree;
use Closure;

class TreeSelect extends BaseSelectTree
{
    protected string|Closure|null $translateLocaleUsing = null;
    protected string|Closure|null $getDisplayValueUsing = null;

    public function translateLocaleUsing(?Closure $callback): static
    {
        $this->translateLocaleUsing = $callback;

        return $this;
    }

    public function getDisplayValueUsing(?Closure $callback): static
    {
        $this->getDisplayValueUsing = $callback;

        return $this;
    }

    protected function buildNode($result, $resultMap, $disabledOptions, $hiddenOptions): array
    {
        $key = $this->getCustomKey($result);

        $displayValue = $this->getDisplayValueUsing
            ? $this->evaluate($this->getDisplayValueUsing, ['record' => $result])
            : $result->{$this->getTitleAttribute()};

        $node = [
            'name' => $displayValue,
            'value' => $key,
            'parent' => $result->{$this->getParentAttribute()},
            'disabled' => in_array($key, $disabledOptions),
            'hidden' => in_array($key, $hiddenOptions),
        ];

        if (isset($resultMap[$key])) {
            $children = collect();
            foreach ($resultMap[$key] as $child) {
                if (in_array($this->getCustomKey($child), $hiddenOptions)) {
                    continue;
                }
                $childNode = $this->buildNode($child, $resultMap, $disabledOptions, $hiddenOptions);
                $children->push($childNode);
            }
            $node['children'] = $children->toArray();
        }

        return $node;
    }
}