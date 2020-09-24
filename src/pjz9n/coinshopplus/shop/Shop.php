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

class Shop implements ConfigSerializable
{
    /** @var string[] */
    private $folders = [];

    /** @var ShopItem[][] */
    private $shopItems = [];

    public function addFolder(string $folder)
    {
        $this->folders[] = $folder;
    }

    /**
     * @return string[]
     */
    public function getAllFolders(): array
    {
        return $this->folders;
    }

    public function getFolder(int $index): ?string
    {
        return $this->folders[$index] ?? null;
    }

    public function removeFolder(int $index): void
    {
        unset($this->folders[$index]);
    }

    public function addShopItem(int $folder, ShopItem $shopItem): void
    {
        $this->shopItems[$folder][] = $shopItem;
    }

    public function getShopItem(int $folder, int $index): ?ShopItem
    {
        return $this->shopItems[$folder][$index] ?? null;
    }

    /**
     * @return ShopItem[]
     */
    public function getAllShopItem(): array
    {
        return $this->shopItems;
    }

    public function removeShopItem(int $folder, ShopItem $shopItem): void
    {
        unset($this->shopItems[$folder][array_search($shopItem, $this->shopItems, true)]);
    }

    public function configSerialize(): array
    {
        $serializedShopItems = [];
        foreach ($this->shopItems as $folder => $shopItems) {
            foreach ($shopItems as $index => $shopItem) {
                $serializedShopItems[$folder][$index] = $shopItem->configSerialize();
            }
        }
        return [
            "folders" => $this->folders,
            "shopItems" => $serializedShopItems,
        ];
    }

    public static function configDeserialize(array $data): self
    {
        $self = new self();
        $self->folders = $data["folders"];
        foreach ($data["shopItems"] as $folder => $serializedShopItems) {
            foreach ($serializedShopItems as $index => $serializedShopItem) {
                $self->shopItems[$folder][$index] = ShopItem::configDeserialize($serializedShopItem);
            }
        }
        return $self;
    }
}
