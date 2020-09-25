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

namespace pjz9n\coinshopplus\shop;

use pjz9n\coinshopplus\utils\ConfigSerializable;

class Shop implements ConfigSerializable
{
    /** @var Folder[] */
    private $folders = [];

    public function addFolder(Folder $folder)
    {
        $this->folders[] = $folder;
        $this->recalculateFoldersKey();
    }

    /**
     * @return Folder[]
     */
    public function getAllFolders(): array
    {
        return $this->folders;
    }

    public function getFolder(int $index): ?Folder
    {
        return $this->folders[$index] ?? null;
    }

    public function removeFolder(Folder $folder): void
    {
        if (($search = array_search($folder, $this->folders, true)) === false) return;
        unset($this->folders[$search]);
        $this->recalculateFoldersKey();
    }

    private function recalculateFoldersKey(): void
    {
        $this->folders = array_values($this->folders);
    }

    public function configSerialize(): array
    {
        $serializedFolders = [];
        foreach ($this->folders as $folder) {
            $serializedFolders[] = $folder->configSerialize();
        }
        return [
            "folders" => $serializedFolders,
        ];
    }

    public static function configDeserialize(array $data): self
    {
        $self = new self();
        foreach ($data["folders"] as $serializedFolder) {
            $self->addFolder(Folder::configDeserialize($serializedFolder));
        }
        return $self;
    }
}
