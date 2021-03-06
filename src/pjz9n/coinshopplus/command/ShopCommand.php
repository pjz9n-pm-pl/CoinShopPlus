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

namespace pjz9n\coinshopplus\command;

use CortexPE\Commando\BaseCommand;
use pjz9n\coinshopplus\form\FolderListForm;
use pjz9n\coinshopplus\language\Language;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class ShopCommand extends BaseCommand
{
    public function __construct(Plugin $plugin)
    {
        parent::__construct(
            $plugin,
            "shop",
            Language::get()->translateString("command.shop.description"),
            ["bshop", "sshop"]
        );
        $this->setPermission("coinshopplus.command.shop");
    }

    protected function prepare(): void
    {
        $this->registerSubCommand(new ShopAddCommand());
        $this->registerSubCommand(new ShopEditCommand());
        $this->registerSubCommand(new ShopFolderCommand());
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . Language::get()->translateString("command.onlyplayer"));
            return;
        }
        $sender->sendForm(new FolderListForm($sender));
    }
}
