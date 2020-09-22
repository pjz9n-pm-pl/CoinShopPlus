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
use pjz9n\coinshopplus\language\Language;
use PJZ9n\MoneyConnector\MoneyConnectorUtils;
use pocketmine\plugin\PluginBase;
use RuntimeException;

class Main extends PluginBase
{
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
    }
}
