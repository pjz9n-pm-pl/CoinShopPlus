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
use pjz9n\coinshopplus\utils\ShopTypeUtils;
use pjz9n\pmformsaddon\AbstractMenuForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ShopItemEditListItemForm extends AbstractMenuForm
{
    /** @var Folder */
    private $folder;

    public function __construct(Folder $folder)
    {
        $this->folder = $folder;
        $options = [];
        $options[] = [
            "text" => Language::get()->translateString("gui.back.page"),
        ];
        foreach ($folder->getAllShopItem() as $shopItem) {
            $options[] = [
                "text" =>
                    Language::get()->translateString("shop.item.description", [
                        $shopItem->getDisplayName(),
                        $shopItem->getPrice(),
                        $shopItem->getCount(),
                    ]) . TextFormat::EOL
                    . implode(" / ", ShopTypeUtils::getShopTypeStringMulti($shopItem->getAcceptTypes())),
            ];
        }
        $options[] = [
            "text" => Language::get()->translateString("gui.back.page"),
        ];
        parent::__construct(
            Language::get()->translateString("shop.item.select"),
            Language::get()->translateString("shop.folder.now", [$folder->getName()]) . TextFormat::EOL
            . TextFormat::EOL . Language::get()->translateString("shop.item.select.description"),
            $options
        );
    }

    public function onSubmit(Player $player, int $selectedOption): void
    {
        if ($selectedOption === 0) {
            //戻る
            $player->sendForm(new FolderListForm($player));
            return;
        }
        $shopItem = $this->folder->getShopItem($selectedOption - 1);
        if (!($shopItem instanceof ShopItem)) {
            //戻る
            $player->sendForm(new FolderListForm($player));
            return;
        }
        //アイテムが選択された
        $player->sendForm(new ShopItemEditSelectForm($this->folder, $shopItem));
    }
}
