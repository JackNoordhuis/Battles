<?php

/**
 * Battles plugin for PocketMine-MP
 *
 * Copyright (C) 2017-2018 Jack Noordhuis
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

namespace jacknoordhuis\battles;

use jacknoordhuis\battles\arena\ArenaManager;
use jacknoordhuis\battles\battle\BattleManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

/**
 * Battles plugin for PocketMine-MP
 */
class BattlesLoader extends PluginBase {

	/** @var Config */
	private $settings;

	/** @var ArenaManager */
	private $arenaManager;

	/** @var BattleManager */
	private $battleManager;

	/* Directory where all the important data is stored */
	const DATA_DIRECTORY = "data";

	/* Main battles configuration file */
	const SETTINGS_FILE = "Settings.yml";

	public function onEnable() {
		$this->loadConfigs();
		$this->setArenaManager();
		$this->setBattleManager();

		$this->getLogger()->info(TextFormat::AQUA . $this->getDescription()->getFullName() . TextFormat::GREEN . " by " . TextFormat::YELLOW . implode(TextFormat::GRAY . ", ", $this->getDescription()->getAuthors()) . TextFormat::GREEN . " has been enabled successfully!");
	}

	/**
	 * Save and create required directories and files
	 */
	protected function loadConfigs() {
		if(!is_dir($path = $this->getDataFolder())) {
			@mkdir($path);
		}
		if(!is_dir($path = $this->getDataFolder() . self::DATA_DIRECTORY)) {
			@mkdir($path);
		}

		$this->saveResource(self::SETTINGS_FILE);
		$this->settings = new Config($this->getDataFolder() . self::SETTINGS_FILE);
	}

	public function onDisable() {
		$this->getLogger()->info(TextFormat::AQUA . $this->getDescription()->getFullName() . TextFormat::GOLD . " by " . TextFormat::YELLOW . implode(TextFormat::GRAY . ", ", $this->getDescription()->getAuthors()) . TextFormat::GOLD . " has been disabled.");
	}

	public function getSettings() : Config {
		return $this->settings;
	}

	/**
	 * Set the arena manager
	 */
	protected function setArenaManager() {
		if(!$this->arenaManager instanceof ArenaManager) {
			$this->arenaManager = new ArenaManager($this);
		}
	}

	/**
	 * Set the battle manager
	 */
	protected function setBattleManager() {
		if(!$this->battleManager instanceof BattleManager) {
			$this->battleManager = new BattleManager($this);
		}
	}

	public function getArenaManager() : ArenaManager {
		return $this->arenaManager;
	}

	public function getBattleManager() : BattleManager {
		return $this->battleManager;
	}

}