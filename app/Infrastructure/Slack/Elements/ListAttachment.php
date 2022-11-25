<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Slack\Elements;

use Illuminate\Support\Collection;

class ListAttachment extends Attachment
{
    protected Collection $items;
    protected string $emptyText = "";

    public function setItems(Collection $items): static
    {
        $this->items = $items;

        return $this;
    }

    public function addItem(string $item): static
    {
        $this->items->add($item);

        return $this;
    }

    public function setEmptyText(string $emptyText): static
    {
        $this->emptyText = $emptyText;

        return $this;
    }

    public function toArray(): array
    {
        $fields = parent::toArray();

        return array_merge($fields, [
            "text" => $this->items->isNotEmpty() ? $this->items->implode("\n") : $this->emptyText,
        ]);
    }
}
