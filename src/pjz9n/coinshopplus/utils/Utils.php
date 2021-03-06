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

use pocketmine\item\ItemFactory;
use Throwable;

class Utils
{
    public static function isOnlyNumber(string $str): bool
    {
        return preg_match("/^[0-9]+$/", $str) === 1;
    }

    public static function isValidItem(int $id, int $damage): bool
    {
        try {
            ItemFactory::get($id, $damage);
        } catch (Throwable $throwable) {
            return false;
        }
        return true;
    }

    private function __construct()
    {
        //NOOP
    }
}
