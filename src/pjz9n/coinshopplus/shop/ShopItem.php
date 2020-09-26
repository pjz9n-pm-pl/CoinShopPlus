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
use pocketmine\item\Item;

class ShopItem implements ConfigSerializable
{
    /** @var string */
    private $displayName;

    /** @var int */
    private $itemId;

    /** @var int */
    private $itemDamage;

    /** @var int */
    private $count;

    /** @var int */
    private $price;

    /** @var int[] */
    private $acceptTypes;

    /**
     * @param int[] $acceptTypes
     */
    public function __construct(string $displayName, int $itemId, int $itemDamage, int $count, int $price, array $acceptTypes)
    {
        $this->displayName = $displayName;
        $this->itemId = $itemId;
        $this->itemDamage = $itemDamage;
        $this->count = $count;
        $this->price = $price;
        $this->acceptTypes = $acceptTypes;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     */
    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
    }

    /**
     * @return int
     */
    public function getItemId(): int
    {
        return $this->itemId;
    }

    /**
     * @param int $itemId
     */
    public function setItemId(int $itemId): void
    {
        $this->itemId = $itemId;
    }

    /**
     * @return int
     */
    public function getItemDamage(): int
    {
        return $this->itemDamage;
    }

    /**
     * @param int $itemDamage
     */
    public function setItemDamage(int $itemDamage): void
    {
        $this->itemDamage = $itemDamage;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @param int $price
     */
    public function setPrice(int $price): void
    {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     */
    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    /**
     * @return int[]
     */
    public function getAcceptTypes(): array
    {
        return $this->acceptTypes;
    }

    /**
     * @param int[] $acceptTypes
     */
    public function setAcceptTypes(array $acceptTypes): void
    {
        $this->acceptTypes = $acceptTypes;
    }

    public function isTypeAccept(int $type): bool
    {
        return array_search($type, $this->acceptTypes, true) !== false;
    }

    public function toItem(): Item
    {
        return Item::get($this->getItemId(), $this->getItemDamage(), $this->getCount());
    }

    public function configSerialize(): array
    {
        return [
            "displayName" => $this->displayName,
            "itemId" => $this->itemId,
            "itemDamage" => $this->itemDamage,
            "count" => $this->count,
            "price" => $this->price,
            "acceptTypes" => $this->acceptTypes,
        ];
    }

    public static function configDeserialize(array $data): self
    {
        return new self(
            $data["displayName"],
            $data["itemId"],
            $data["itemDamage"],
            $data["count"],
            $data["price"],
            $data["acceptTypes"]
        );
    }
}
