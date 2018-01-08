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

namespace jacknoordhuis\battles\event\session;

use jacknoordhuis\battles\session\SessionManager;
use jacknoordhuis\battles\utils\exception\BattlesEventException;
use pocketmine\Player;

class SessionCreationEvent extends SessionManagerEvent {

	public static $handlerList = null;

	/** @var Player */
	private $player;

	/** @var string */
	private $baseSessionClass;

	/** @var string */
	private $sessionClass;

	public function __construct(SessionManager $manager, Player $player, string $baseSessionClass, string $sessionClass) {
		$this->player = $player;
		$this->baseSessionClass = $baseSessionClass;
		$this->sessionClass = $sessionClass;
		parent::__construct($manager);
	}

	public function getPlayer() : Player {
		return $this->player;
	}

	public function getBaseSessionClass() : string {
		return $this->baseSessionClass;
	}

	/**
	 * Set the base class that all session classes and future base session classes for this event instance must extend
	 *
	 * @param string $class
	 */
	public function setBaseSessionClass(string $class) {
		if(!is_a($class, $this->baseSessionClass, true)) {
			throw new BattlesEventException($this, "Base class '{$class}' must extend '{$this->baseSessionClass}'.");
		}

		$this->baseSessionClass = $class;
	}

	public function getSessionClass() : string {
		return $this->sessionClass;
	}

	/**
	 * Set the session class to be constructed
	 *
	 * @param string $class
	 */
	public function setSessionClass(string $class) {
		if(!is_a($class, $this->baseSessionClass, true)) {
			throw new BattlesEventException($this, "Session class '{$class}' must extend '{$this->baseSessionClass}'.");
		}

		$this->sessionClass = $class;
	}

}