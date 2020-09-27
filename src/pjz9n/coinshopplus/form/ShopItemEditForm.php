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

use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\element\Toggle;
use InvalidArgumentException;
use pjz9n\coinshopplus\language\Language;
use pjz9n\coinshopplus\shop\Folder;
use pjz9n\coinshopplus\shop\ShopHolder;
use pjz9n\coinshopplus\shop\ShopItem;
use pjz9n\coinshopplus\shop\ShopType;
use pjz9n\coinshopplus\utils\Utils;
use pjz9n\pmformsaddon\AbstractCustomForm;
use pocketmine\Player;

class ShopItemEditForm extends AbstractCustomForm
{
    /** @var Folder|null */
    private $folder;

    /** @var ShopItem|null */
    private $shopItem;

    public function __construct(?Folder $folder = null, ?ShopItem $shopItem = null)
    {
        if (!($folder === null && $shopItem === null || $folder !== null && $shopItem !== null)) {
            throw new InvalidArgumentException("Only one of \$folder and \$shopItem cannot be null.");
        }
        $this->folder = $folder;
        $this->shopItem = $shopItem;
        if ($folder instanceof Folder && $shopItem instanceof ShopItem) {
            parent::__construct(
                Language::get()->translateString("shop.item.edit"),
                [
                    new Input("display-name", Language::get()->translateString("displayname"), "", $shopItem->getDisplayName()),
                    new Input("item-id", Language::get()->translateString("item.id"), "", (string)$shopItem->getItemId()),
                    new Input("item-damage", Language::get()->translateString("item.damage"), "", (string)$shopItem->getItemDamage()),
                    new Input("item-count", Language::get()->translateString("count"), "", (string)$shopItem->getCount()),
                    new Input("price", Language::get()->translateString("price"), "", (string)$shopItem->getPrice()),
                    new Dropdown("folder", Language::get()->translateString("folder"), array_map(function (Folder $folder) {
                        return $folder->getName();
                    }, ShopHolder::getShop()->getAllFolders()), ShopHolder::getShop()->getFolderIndex($folder)),
                    new Toggle("buy", Language::get()->translateString("type.buy"), $shopItem->isTypeAccept(ShopType::BUY)),
                    new Toggle("sell", Language::get()->translateString("type.sell"), $shopItem->isTypeAccept(ShopType::SELL)),
                ]
            );
        } else {
            parent::__construct(
                Language::get()->translateString("shop.item.edit"),
                [
                    new Input("display-name", Language::get()->translateString("displayname")),
                    new Input("item-id", Language::get()->translateString("item.id")),
                    new Input("item-damage", Language::get()->translateString("item.damage")),
                    new Input("item-count", Language::get()->translateString("count")),
                    new Input("price", Language::get()->translateString("price")),
                    new Dropdown("folder", Language::get()->translateString("folder"), array_map(function (Folder $folder) {
                        return $folder->getName();
                    }, ShopHolder::getShop()->getAllFolders())),
                    new Toggle("buy", Language::get()->translateString("type.buy")),
                    new Toggle("sell", Language::get()->translateString("type.sell")),
                ]
            );
        }
    }

    public function onSubmit(Player $player, CustomFormResponse $response): void
    {
        $displayName = $response->getString("display-name");
        if (!Utils::isOnlyNumber(($itemId = $response->getString("item-id")))) {
            $player->sendForm(new ErrorForm(Language::get()->translateString("error.validate.numeric", [
                Language::get()->translateString("shop.item.add.item.id"),
            ]), $this));
            return;
        }
        $itemId = (int)$itemId;
        if (!Utils::isOnlyNumber(($itemDamage = $response->getString("item-damage")))) {
            $player->sendForm(new ErrorForm(Language::get()->translateString("error.validate.numeric", [
                Language::get()->translateString("shop.item.add.item.damage"),
            ]), $this));
            return;
        }
        $itemDamage = (int)$itemDamage;
        if (!Utils::isValidItem($itemId, $itemDamage)) {
            $player->sendForm(new ErrorForm(Language::get()->translateString("error.invalid.item"), $this));
            return;
        }
        if (!Utils::isOnlyNumber(($itemCount = $response->getString("item-count")))) {
            $player->sendForm(new ErrorForm(Language::get()->translateString("error.validate.numeric", [
                Language::get()->translateString("shop.item.add.item.count"),
            ]), $this));
            return;
        }
        $itemCount = (int)$itemCount;
        if (!Utils::isOnlyNumber(($price = $response->getString("price")))) {
            $player->sendForm(new ErrorForm(Language::get()->translateString("error.validate.numeric", [
                Language::get()->translateString("shop.item.add.price"),
            ]), $this));
            return;
        }
        $price = (int)$price;
        $folder = ShopHolder::getShop()->getFolder($response->getInt("folder"));
        $buy = $response->getBool("buy");
        $sell = $response->getBool("sell");
        $acceptTypes = [];
        if ($buy) $acceptTypes[] = ShopType::BUY;
        if ($sell) $acceptTypes[] = ShopType::SELL;
        if (count($acceptTypes) === 0) {
            $player->sendForm(new ErrorForm(Language::get()->translateString("error.require.one.select", [
                implode(", ", [
                    Language::get()->translateString("type.buy"),
                    Language::get()->translateString("type.sell"),
                ]),
            ]), $this));
            return;
        }
        $folder->addShopItem(new ShopItem(
            $displayName,
            $itemId,
            $itemDamage,
            $itemCount,
            $price,
            $acceptTypes
        ));
        if ($this->folder instanceof Folder && $this->shopItem instanceof ShopItem) {
            //移動のため
            $this->folder->removeShopItem($this->shopItem);
            $player->sendForm(new SuccessForm(Language::get()->translateString("shop.item.edit.success", [
                Language::get()->translateString("change"),
            ]), $this));
        } else {
            $player->sendForm(new SuccessForm(Language::get()->translateString("shop.item.edit.success", [
                Language::get()->translateString("add"),
            ]), $this));
        }
    }
}
