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

namespace jacknoordhuis\battles\event\battle;

use jacknoordhuis\battles\battle\BattleManager;
use jacknoordhuis\battles\utils\exception\BattlesEventException;

class BattleCreationEvent extends BattleManagerEvent {

	public static $handlerList = null;

	/** @var string */
	private $baseBattleClass;

	/** @var string */
	private $battleClass;

	public function __construct(BattleManager $manager, string $baseBattleClass, string $battleClass) {
		$this->baseBattleClass = $baseBattleClass;
		$this->battleClass = $battleClass;
		parent::__construct($manager);
	}

	public function getBaseBattleClass() : string {
		return $this->baseBattleClass;
	}

	/**
	 * Set the base class that all battles and future base classes for this event must extend
	 *
	 * @param string $class
	 *
	 * @throws BattlesEventException
	 */
	public function setBaseBattleClass(string $class) {
		if(!is_a($class, $this->baseBattleClass, true)) {
			throw new BattlesEventException($this, "Base class {$class} must extend {$this->baseBattleClass}.");
		}

		$this->baseBattleClass = $class;
	}

	public function getBattleClass() : string {
		return $this->battleClass;
	}

	/**
	 * Set the battle class to be constructed
	 *
	 * @param string $class
	 *
	 * @throws BattlesEventException
	 */
	public function setBattleClass(string $class) {
		if(!is_a($class, $this->baseBattleClass, true)) {
			throw new BattlesEventException($this, "Base class {$class} must extend {$this->baseBattleClass}.");
		}

		$this->battleClass = $class;
	}

}