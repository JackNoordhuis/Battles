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

use jacknoordhuis\battles\battle\utils\exception\battle\DuplicateBattleIdentifierException;
use jacknoordhuis\battles\BattlesLoader;

class BattleManager {

	/** @var BattlesLoader */
	private $plugin;

	/** @var BattleHeartbeat */
	private $heartbeat;

	/** @var BaseBattle[] */
	private $battlesPool = [];

	public function __construct(BattlesLoader $plugin) {
		$this->plugin = $plugin;
		$this->heartbeat = new BattleHeartbeat($this);
	}

	public function getPlugin() : BattlesLoader {
		return $this->plugin;
	}

	public function getHeartbeat() : BattleHeartbeat {
		return $this->heartbeat;
	}

	/**
	 * Add a battle into the pool
	 *
	 * @param BaseBattle $battle
	 *
	 * @throws DuplicateBattleIdentifierException
	 */
	public function addBattle(BaseBattle $battle) {
		if(!$this->battleExists($id = $battle->getId())) {
			$this->battlesPool[$id] = $battle;
		} else {
			throw new DuplicateBattleIdentifierException($battle);
		}
	}

	/**
	 *
	 * @param string $id
	 *
	 * @return BaseBattle|null
	 */
	public function getBattle(string $id) {
		if($this->battleExists($id)) {
			return $this->battlesPool[$id];
		}
		return null;
	}

	/**
	 * Check if a battle is in the pool
	 *
	 * @param string $id
	 *
	 * @return bool
	 */
	public function battleExists(string $id) : bool {
		return isset($this->battlesPool[$id]) and $this->battlesPool[$id] instanceof BaseBattle;
	}

	/**
	 * Remove a battle from the pool
	 *
	 * @param string $id
	 */
	public function removeBattle(string $id) {
		if($this->battleExists($id)) {
			$this->battlesPool[$id]->end();
			unset($this->battlesPool[$id]);
		}
	}

	/**
	 * @return BaseBattle[]
	 */
	public function getBattles() : array {
		return $this->battlesPool;
	}

}