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
use dktapps\pmforms\element\Input;
use dktapps\pmforms\element\Label;
use pjz9n\coinshopplus\language\Language;
use pjz9n\coinshopplus\shop\ShopItem;
use pjz9n\coinshopplus\shop\ShopType;
use pjz9n\coinshopplus\utils\InventoryUtils;
use pjz9n\coinshopplus\utils\ShopTypeUtils;
use PJZ9n\MoneyConnector\MoneyConnectorUtils;
use pjz9n\pmformsaddon\AbstractCustomForm;
use pocketmine\Player;

class SetCountForm extends AbstractCustomForm
{
    /** @var int */
    private $shopType;

    /** @var ShopItem */
    private $shopItem;

    public function __construct(int $shopType, ShopItem $shopItem, Player $player)
    {
        $this->shopType = $shopType;
        $this->shopItem = $shopItem;
        parent::__construct(
            Language::get()->translateString("shop") . " - " . Language::get()->translateString("shop.setcount.select"),
            [
                new Label("money-now", Language::get()->translateString("element.money.now", [
                    MoneyConnectorUtils::getConnectorByDetect()->myMoney($player),
                    MoneyConnectorUtils::getConnectorByDetect()->getMonetaryUnit(),
                ])),
                new Label("item-description", Language::get()->translateString("shop.item.description", [
                    $shopItem->getDisplayName(),
                    $shopItem->getPrice(),
                    $shopItem->getCount(),
                ])),
                new Label("item-now", Language::get()->translateString("element.item.now", [
                    InventoryUtils::count($player->getInventory(), $shopItem->toItem()),
                ])),
                new Label("allow-max", Language::get()->translateString("shop.setcount.select.allow.max", [
                    $this->getAllowMax($player) !== -1 ? $this->getAllowMax($player) : "∞",
                ])),
                new Label("shop-type", Language::get()->translateString("shop.type.now", [
                    ShopTypeUtils::getShopTypeString($shopType),
                ])),
                new Input(
                    "set-count",
                    Language::get()->translateString("shop.setcount"),
                    "",
                    (string)$this->getAllowMax($player)
                ),
            ]
        );
    }

    public function onSubmit(Player $player, CustomFormResponse $response): void
    {
        $setCount = $response->getString("set-count");
        if (preg_match("/^[0-9]+$/", $setCount) !== 1) {
            $player->sendForm(new ErrorForm(Language::get()->translateString("error.validate.numeric"), $this));
            return;
        }
        $setCount = (int)$setCount;
        if ($setCount <= 0) {
            $player->sendForm(new ErrorForm(Language::get()->translateString("error.validate.toosmall"), $this));
            return;
        }
        //最大セット数超過チェック(UX的な意味合いが強い)
        $allowMax = $this->getAllowMax($player);
        if ($allowMax !== -1 && $allowMax < $setCount) {
            $player->sendForm(new ErrorForm(Language::get()->translateString("error.exceed.allow.max", [$allowMax]), $this));
            return;
        }
        $player->sendForm(new ConfirmForm($this->shopType, $this->shopItem, $setCount, $player));
    }

    private function getAllowMax(Player $player): ?int
    {
        switch ($this->shopType) {
            case ShopType::BUY:
                return $this->shopItem->getPrice() > 0
                    ? (int)floor(MoneyConnectorUtils::getConnectorByDetect()->myMoney($player) / $this->shopItem->getPrice())
                    : -1;
            case ShopType::SELL:
                return $this->shopItem->getCount()
                    ? (int)floor(InventoryUtils::count($player->getInventory(), $this->shopItem->toItem()) / $this->shopItem->getCount())
                    : -1;
        }
        return null;
    }
}
