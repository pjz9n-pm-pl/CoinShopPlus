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
use pjz9n\coinshopplus\shop\Folder;
use pjz9n\coinshopplus\shop\ShopItem;
use pjz9n\pmformsaddon\AbstractMenuForm;
use pocketmine\Player;

class ShopItemEditSelectForm extends AbstractMenuForm
{
    /** @var Folder */
    private $folder;

    /** @var ShopItem */
    private $shopItem;

    public function __construct(Folder $folder, ShopItem $shopItem)
    {
        $this->folder = $folder;
        $this->shopItem = $shopItem;
        parent::__construct(
            Language::get()->translateString("shop.item.edit.select"),
            Language::get()->translateString("shop.item.edit.select.description"),
            [
                [
                    "text" => Language::get()->translateString("change"),
                ],
                [
                    "text" => Language::get()->translateString("delete"),
                ],
            ]
        );
    }

    public function onSubmit(Player $player, int $selectedOption): void
    {
        switch ($selectedOption) {
            case 0:
                //編集
                $player->sendForm(new ShopItemEditForm($this->folder, $this->shopItem));
                return;
            case 1:
                //削除
                $this->folder->removeShopItem($this->shopItem);
                $player->sendForm(new SuccessForm(Language::get()->translateString("shop.item.edit.success", [
                    Language::get()->translateString("delete"),
                ])));
                return;
        }
    }
}
