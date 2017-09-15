<?php

/*
 * Battles plugin for PocketMine-MP
 *
 * Copyright (C) 2017 JackNoordhuis
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

namespace jacknoordhuis\battles\arena;

use jacknoordhuis\battles\BattlesLoader;
use jacknoordhuis\battles\utils\ConfigParseHelper;
use pocketmine\level\Position;
use pocketmine\scheduler\FileWriteTask;

class ArenaManager {

	/** @var BattlesLoader */
	private $plugin;

	/** @var bool */
	private $cacheArenas = true;

	/** @var Arena[] */
	private $arenasPool;

	const ARENA_FILE_PATH = DIRECTORY_SEPARATOR . "arenas.json";
	const CACHED_ARENAS_FILE_PATH = BattlesLoader::DATA_DIRECTORY . DIRECTORY_SEPARATOR . "arenas.cache.sl";

	public function __construct(BattlesLoader $plugin) {
		$this->plugin = $plugin;
		$this->cacheArenas = ConfigParseHelper::getBoolValue($plugin->getSettings()->getNested("cache-arena-data", true));

		$this->loadFromFile();
		$this->cacheArenaData();
	}

	/**
	 * @return BattlesLoader
	 */
	public function getPlugin() : BattlesLoader {
		return $this->plugin;
	}

	/**
	 * @param bool $async
	 */
	private function cacheArenaData($async = true) {
		if($this->cacheArenas and !is_file($path = $this->plugin->getDataFolder() . self::CACHED_ARENAS_FILE_PATH)) {
			$data = serialize($this->arenasPool);
			if($async) {
				$this->plugin->getServer()->getScheduler()->scheduleAsyncTask(new FileWriteTask($path, $data));
			} else {
				file_put_contents($path, $data);
			}
		}
	}

	/**
	 * Load arenas from the config
	 */
	private function loadFromFile() {
		if($this->cacheArenas and is_file($path = $this->plugin->getDataFolder() . self::CACHED_ARENAS_FILE_PATH)) { // load the cache
			$this->arenasPool = unserialize(file_get_contents($path));
		} else { // load the arena data from the config
			$this->plugin->saveResource(self::ARENA_FILE_PATH);
			foreach(json_decode(file_get_contents($this->plugin->getDataFolder() . self::ARENA_FILE_PATH), true) as $arenaName => $arenaData) {
				try {
					$this->addArena(strtolower($arenaName), $arenaData["display"] ?? $arenaName, $arenaData["author"] ?? "unknown", ConfigParseHelper::parsePositions($arenaData["spawns"]));
				} catch(\Throwable $e) {
					$this->plugin->getLogger()->warning("Could not load arena {$arenaName}!");
					$this->plugin->getLogger()->logException($e);
				}
			}
		}
	}

	/**
	 * @param string $name
	 * @param string $display
	 * @param string $author
	 * @param Position[] $spawns
	 */
	public function addArena(string $name, string $display, string $author, array $spawns) {
		$this->arenasPool[$name] = new Arena($name, $display, $author, $spawns);
	}

}