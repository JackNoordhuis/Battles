<?php

/*
 * Battles plugin for PocketMine-MP
 *
 * Copyright (C) 2017-2018 JackNoordhuis
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

namespace jacknoordhuis\battles\battle;

use jacknoordhuis\battles\utils\exception\BattlesException;
use pocketmine\scheduler\PluginTask;

class BattleHeartbeat extends PluginTask {

	/** @var BattleManager */
	private $manager;

	public function __construct(BattleManager $manager) {
		$this->manager = $manager;
		parent::__construct($plugin = $manager->getPlugin());

		$plugin->getServer()->getScheduler()->scheduleRepeatingTask($this, 20);
	}

	public function getManager() : BattleManager {
		return $this->manager;
	}

	/**
	 * Ticks all battles
	 *
	 * @param int $tick
	 */
	public function onRun(int $tick) {
		foreach($this->manager->getBattles() as $battle) {
			try {
				$battle->tick($tick);
			} catch(BattlesException $e) {
				($logger = $this->manager->getPlugin()->getLogger())->debug("Encountered exception '{$e->getShortName()}' when attempting to tick battle! Details:");
				$logger->logException($e);
			}
		}
	}

}