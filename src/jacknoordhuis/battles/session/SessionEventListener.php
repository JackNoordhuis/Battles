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

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;

class SessionEventListener implements Listener {

	/** @var SessionManager */
	private $manager;

	public function __construct(SessionManager $manager) {
		$this->manager = $manager;

		$manager->getPlugin()->getServer()->getPluginManager()->registerEvents($this, $manager->getPlugin());
	}

	public function getManager() : SessionManager {
		return $this->manager;
	}

	public function onLogin(PlayerLoginEvent $event) {
		$this->manager->openSession($event->getPlayer());
	}

	public function onQuit(PlayerQuitEvent $event) {
		$this->manager->closeSession($event->getPlayer());
	}

}