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

namespace jacknoordhuis\battles\session;

use jacknoordhuis\battles\BattlesLoader;
use jacknoordhuis\battles\event\session\SessionCreationEvent;
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

	public function openSession(Player $player) : void {
		($ev = new SessionCreationEvent($this, $player, PlayerSession::class, PlayerSession::class))->call();
		$class = $ev->getSessionClass();
		$this->sessionPool[spl_object_hash($player)] = new $class($this, $player);
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
	public function getSession($player) : ?PlayerSession {
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
	public function closeSession($player) : void {
		if(!($player instanceof Player)) {
			$player = $this->plugin->getServer()->getPlayerExact($player);
		}
		if(isset($this->sessionPool[$hash = spl_object_hash($player)]) and $this->sessionPool[$hash] instanceof PlayerSession) {
			$this->sessionPool[$hash]->onQuit();
			unset($this->sessionPool[$hash]);
		}
	}

}