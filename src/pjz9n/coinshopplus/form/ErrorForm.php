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
use pjz9n\pmformsaddon\AbstractModalForm;
use pocketmine\form\Form;
use pocketmine\Player;

class ErrorForm extends AbstractModalForm
{
    /** @var Form|null */
    private $back;

    public function __construct(string $message, ?Form $back = null)
    {
        $this->back = $back;
        parent::__construct(
            Language::get()->translateString("error"),
            $message,
            $back === null
                ? Language::get()->translateString("gui.close")
                : Language::get()->translateString("gui.back"),
            Language::get()->translateString("gui.close")
        );
    }

    public function onSubmit(Player $player, bool $choice): void
    {
        if ($this->back === null) {
            return;
        }
        if ($choice) {
            $player->sendForm($this->back);
        }
    }
}
