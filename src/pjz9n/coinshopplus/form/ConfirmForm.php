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
use pjz9n\coinshopplus\shop\ShopItem;
use pjz9n\coinshopplus\shop\ShopType;
use pjz9n\coinshopplus\utils\ShopTypeUtils;
use PJZ9n\MoneyConnector\MoneyConnectorUtils;
use pjz9n\pmformsaddon\AbstractModalForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ConfirmForm extends AbstractModalForm
{
    /** @var int */
    private $shopType;

    /** @var ShopItem */
    private $shopItem;

    /** @var int */
    private $setCount;

    public function __construct(int $shopType, ShopItem $shopItem, int $setCount, Player $player)
    {
        $this->shopType = $shopType;
        $this->shopItem = $shopItem;
        $this->setCount = $setCount;
        parent::__construct(
            Language::get()->translateString("shop") . " - " . Language::get()->translateString("shop.confirm"),
            Language::get()->translateString("element.money.now", [
                MoneyConnectorUtils::getConnectorByDetect()->myMoney($player),
                MoneyConnectorUtils::getConnectorByDetect()->getMonetaryUnit(),
            ]) . TextFormat::EOL
            . Language::get()->translateString("shop.item.description", [
                $shopItem->getDisplayName(),
                $shopItem->getPrice() * $setCount,
                $shopItem->getCount() * $setCount,
            ]) . TextFormat::EOL
            . TextFormat::EOL . Language::get()->translateString("shop.confirm.description", [ShopTypeUtils::getShopTypeString($shopType)]),
            ShopTypeUtils::getShopTypeString($shopType),
            Language::get()->translateString("gui.back.page")
        );
    }

    public function onSubmit(Player $player, bool $choice): void
    {
        if (!$choice) {
            $player->sendForm(new SetCountForm($this->shopType, $this->shopItem, $player));
            return;
        }
        //前提変数定義
        $inventory = $player->getInventory();
        $nowMoney = MoneyConnectorUtils::getConnectorByDetect()->myMoney($player);
        $unit = MoneyConnectorUtils::getConnectorByDetect()->getMonetaryUnit();
        $price = $this->shopItem->getPrice() * $this->setCount;
        $item = $this->shopItem->toItem();
        $item->setCount($item->getCount() * $this->setCount);
        switch ($this->shopType) {
            case ShopType::BUY:
                //所持金チェック
                if (($diffMoney = $nowMoney - $price) < 0) {
                    $player->sendForm(new ErrorForm(Language::get()->translateString("error.notenough.money", [
                        -$diffMoney,
                        $unit,
                    ]), $this));
                    return;
                }
                //インベントリチェック
                if (!$inventory->canAddItem($item)) {
                    $player->sendForm(new ErrorForm(Language::get()->translateString("error.notenough.inventory"), $this));
                    return;
                }
                //所持金減算、アイテム付与
                MoneyConnectorUtils::getConnectorByDetect()->reduceMoney($player, $price);
                $inventory->addItem($item);
                break;
            case ShopType::SELL:
                //アイテム所持チェック
                if (!$inventory->contains($item)) {
                    $player->sendForm(new ErrorForm(Language::get()->translateString("error.notenough.item"), $this));
                    return;
                }
                //アイテム削除、所持金加算
                $inventory->removeItem($item);
                MoneyConnectorUtils::getConnectorByDetect()->addMoney($player, $price);
                break;
            default:
                return;
        }
        //完了
        $player->sendForm(new SuccessForm(
            Language::get()->translateString("element.money.now", [
                MoneyConnectorUtils::getConnectorByDetect()->getMonetaryUnit(),
                MoneyConnectorUtils::getConnectorByDetect()->myMoney($player),
            ]) . TextFormat::EOL
            . Language::get()->translateString("shop.item.description", [
                $this->shopItem->getDisplayName(),
                $price,
                $item->getCount(),
            ]) . TextFormat::EOL
            . TextFormat::EOL . Language::get()->translateString("shop.success", [ShopTypeUtils::getShopTypeString($this->shopType)]),
            new SetCountForm($this->shopType, $this->shopItem, $player)
        ));
    }
}
