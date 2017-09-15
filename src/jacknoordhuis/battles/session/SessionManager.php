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

namespace jacknoordhuis\battles\session;

use jacknoordhuis\battles\BattlesLoader;
use pocketmine\Player;

class SessionManager {

	/** @var BattlesLoader */
	private $plugin;

	/** @var SessionEventListener */
	private $listener;

	/** @var PlayerSession[] */
	private $sessionPool;

	public function __construct(BattlesLoader $plugin) {
		$this->plugin = $plugin;
		$this->listener = new SessionEventListener($this);
	}

	/**
	 * @return BattlesLoader
	 */
	public function getPlugin() : BattlesLoader {
		return $this->plugin;
	}

	public function openSession(Player $player) {
		$this->sessionPool[spl_object_hash($player)] = new PlayerSession($this, $player);
	}

	/**
	 * @param $player
	 *
	 * @return bool
	 */
	public function hasSession($player) : bool {
		if(!($player instanceof Player)) {
			$player = $this->plugin->getServer()->getPlayerExact($player);
		}
		return isset($this->sessionPool[$hash = spl_object_hash($player)]) and $this->sessionPool[$hash] instanceof PlayerSession;
	}

	/**
	 * @param $player
	 *
	 * @return PlayerSession|null
	 */
	public function getSession($player) {
		if(!($player instanceof Player)) {
			$player = $this->plugin->getServer()->getPlayerExact($player);
		}

		if(isset($this->sessionPool[$hash = spl_object_hash($player)]) and $this->sessionPool[$hash] instanceof PlayerSession) {
			return $this->sessionPool[$hash];
		}
		return null;
	}

	/**
	 * @param Player|string $player
	 */
	public function closeSession($player) {
		if(!($player instanceof Player)) {
			$player = $this->plugin->getServer()->getPlayerExact($player);
		}
		if(isset($this->sessionPool[$hash = spl_object_hash($player)]) and $this->sessionPool[$hash] instanceof PlayerSession) {
			$this->sessionPool[$hash]->onQuit();
			unset($this->sessionPool[$hash]);
		}
	}

}