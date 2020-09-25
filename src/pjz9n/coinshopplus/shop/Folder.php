<?php

/**
 * Copyright (c) 2020 PJZ9n.
 *
 * This file is part of CoinShopPlus.
 *
 * CoinShopPlus is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * CoinShopPlus is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CoinShopPlus. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace pjz9n\coinshopplus\shop;

use pjz9n\coinshopplus\utils\ConfigSerializable;

class Folder implements ConfigSerializable
{
    /** @var string */
    private $name;

    /** @var ShopItem[] */
    private $shopItems = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function addShopItem(ShopItem $shopItem): void
    {
        $this->shopItems[] = $shopItem;
        $this->recalculateShopItemsKey();
    }

    public function getShopItem(int $index): ?ShopItem
    {
        return $this->shopItems[$index] ?? null;
    }

    /**
     * @return ShopItem[]
     */
    public function getAllShopItem(): array
    {
        return $this->shopItems;
    }

    public function removeShopItem(ShopItem $shopItem): void
    {
        unset($this->shopItems[array_search($shopItem, $this->shopItems, true)]);
        $this->recalculateShopItemsKey();
    }

    private function recalculateShopItemsKey(): void
    {
        $this->shopItems = array_values($this->shopItems);
    }

    public function configSerialize(): array
    {
        $serializedShopItems = [];
        foreach ($this->shopItems as $shopItem) {
            $serializedShopItems[] = $shopItem->configSerialize();
        }
        return [
            "name" => $this->name,
            "shopItems" => $serializedShopItems,
        ];
    }

    public static function configDeserialize(array $data): self
    {
        $self = new self($data["name"]);
        foreach ($data["shopItems"] as $serializedShopItem) {
            $self->addShopItem(ShopItem::configDeserialize($serializedShopItem));
        }
        return $self;
    }
}
