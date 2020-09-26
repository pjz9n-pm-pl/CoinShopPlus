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

use pjz9n\coinshopplus\language\Language;
use pjz9n\coinshopplus\shop\ShopType;
use pocketmine\lang\TranslationContainer;

class ShopTypeUtils
{
    public static function getShopTypeText(int $type): ?TranslationContainer
    {
        switch ($type) {
            case ShopType::BUY:
                return new TranslationContainer("type.buy");
            case ShopType::SELL:
                return new TranslationContainer("type.sell");
        }
        return null;
    }

    /**
     * @param int[] $types
     *
     * @return TranslationContainer|null[]
     */
    public static function getShopTypeTextMulti(array $types): array
    {
        $shopTypeTexts = [];
        foreach ($types as $type) {
            $shopTypeTexts[] = self::getShopTypeText($type);
        }
        return $shopTypeTexts;
    }

    public static function getShopTypeString(int $type): ?string
    {
        return ($shopTypeText = self::getShopTypeText($type)) instanceof TranslationContainer
            ? Language::get()->translate($shopTypeText)
            : null;
    }

    /**
     * @param int[] $types
     *
     * @return string|null[]
     */
    public static function getShopTypeStringMulti(array $types): array
    {
        $shopTypeStrings = [];
        foreach ($types as $type) {
            $shopTypeStrings[] = self::getShopTypeString($type);
        }
        return $shopTypeStrings;
    }

    private function __construct()
    {
        //NOOP
    }
}
