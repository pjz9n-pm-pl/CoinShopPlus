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

namespace pjz9n\coinshopplus\utils;

use pocketmine\inventory\Inventory;
use pocketmine\item\Item;

class InventoryUtils
{
    public static function count(Inventory $inventory, Item $target): int
    {
        $count = 0;
        foreach ($inventory->all($target) as $item) {
            $count += $item->getCount();
        }
        return $count;
    }

    private function __construct()
    {
        //NOOP
    }
}
