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

namespace pjz9n\coinshopplus\form;

use pjz9n\coinshopplus\language\Language;
use pjz9n\coinshopplus\shop\ShopHolder;
use pjz9n\pmformsaddon\AbstractMenuForm;
use pocketmine\Player;

class ShopItemEditListFolderForm extends AbstractMenuForm
{
    public function __construct()
    {
        $options = [];
        foreach (ShopHolder::getShop()->getAllFolders() as $folder) {
            $options[] = [
                "text" => "{$folder}",
            ];
        }
        parent::__construct(
            Language::get()->translateString("shop.folder.select"),
            Language::get()->translateString("shop.folder.select.description"),
            $options
        );
    }

    public function onSubmit(Player $player, int $selectedOption): void
    {
        $player->sendForm(new ShopItemEditListItemForm(ShopHolder::getShop()->getFolder($selectedOption)));
    }
}
