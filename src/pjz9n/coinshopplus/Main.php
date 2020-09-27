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

namespace pjz9n\coinshopplus;

use CortexPE\Commando\exception\HookAlreadyRegistered;
use CortexPE\Commando\PacketHooker;
use pjz9n\coinshopplus\command\ShopCommand;
use pjz9n\coinshopplus\language\Language;
use pjz9n\coinshopplus\shop\Shop;
use pjz9n\coinshopplus\shop\ShopHolder;
use PJZ9n\MoneyConnector\MoneyConnectorUtils;
use pocketmine\permission\Permission;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use RuntimeException;

class Main extends PluginBase
{
    /** @var Config */
    private $shopConfig;

    /**
     * @throws HookAlreadyRegistered
     */
    public function onEnable(): void
    {
        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }
        Language::init(
            (string)$this->getConfig()->get("language", "default"),
            $this->getFile() . "resources/locale/",
            "jpn"
        );
        $language = Language::get();
        $this->getLogger()->info($language->translateString("language.selected", [$language->getName()]));
        if (!MoneyConnectorUtils::isExistsSupportedAPI()) {
            throw new RuntimeException($language->translateString("money.notfound"));
        }
        $this->getLogger()->info(
            $language->translateString("money.selected", [MoneyConnectorUtils::getConnectorByDetect()->getName()])
        );
        $this->shopConfig = new Config($this->getDataFolder() . "shop.json");
        if (($serializedShop = $this->shopConfig->get("shop")) === false) {
            $shop = new Shop();
        } else {
            $shop = Shop::configDeserialize($serializedShop);
        }
        ShopHolder::init($shop);
        $this->getServer()->getPluginManager()->addPermission(new Permission(
            "coinshopplus.command.shop",
            "permission for /shop command",
            Permission::DEFAULT_TRUE,
        ));
        $this->getServer()->getPluginManager()->addPermission(new Permission(
            "coinshopplus.command.shop.add",
            "permission for /shop add command",
            Permission::DEFAULT_OP,
        ));
        $this->getServer()->getPluginManager()->addPermission(new Permission(
            "coinshopplus.command.shop.edit",
            "permission for /shop edit command",
            Permission::DEFAULT_OP,
        ));
        $this->getServer()->getPluginManager()->addPermission(new Permission(
            "coinshopplus.command.shop.folder",
            "permission for /shop folder command",
            Permission::DEFAULT_OP,
        ));
        $this->getServer()->getCommandMap()->register($this->getName(), new ShopCommand($this));
    }

    public function onDisable(): void
    {
        $this->shopConfig->set("shop", ShopHolder::getShop()->configSerialize());
        $this->shopConfig->save();
    }
}
