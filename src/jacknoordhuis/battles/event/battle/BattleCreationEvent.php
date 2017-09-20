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

	/** @var int */
	private $countdownDuration;

	/** @var int */
	private $playingDuration;

	/** @var int */
	private $endedDuration;

	public function __construct(BattleManager $manager, string $baseBattleClass, string $battleClass, int $countdownDuration, int $playingDuration, int $endedDuration) {
		$this->baseBattleClass = $baseBattleClass;
		$this->battleClass = $battleClass;
		$this->countdownDuration = $countdownDuration;
		$this->playingDuration = $playingDuration;
		$this->endedDuration = $endedDuration;
		parent::__construct($manager);
	}

	public function getBaseBattleClass() : string {
		return $this->baseBattleClass;
	}

	/**
	 * Set the base class that all battle classes and future base battle classes for this event must extend
	 *
	 * @param string $class
	 *
	 * @throws BattlesEventException
	 */
	public function setBaseBattleClass(string $class) {
		if(!is_a($class, $this->baseBattleClass, true)) {
			throw new BattlesEventException($this, "Battle class {$class} must extend {$this->baseBattleClass}.");
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

	/**
	 * Get the countdown duration for the battle
	 *
	 * @return int
	 */
	public function getCountdownDuration() : int {
		return $this->countdownDuration;
	}

	/**
	 * Set the countdown duration for the battle
	 *
	 * @param int $duration
	 */
	public function setCountdownDuration(int $duration) {
		$this->countdownDuration = $duration;
	}

	/**
	 * Get the playing duration for the battle
	 *
	 * @return int
	 */
	public function getPlayingDuration() : int {
		return $this->playingDuration;
	}

	/**
	 * Set the playing duration for the battle
	 *
	 * @param int $duration
	 */
	public function setPlayingDuration(int $duration) {
		$this->playingDuration = $duration;
	}

	/**
	 * Get the ended duration for the battle
	 *
	 * @return int
	 */
	public function getEndedDuration() : int {
		return $this->endedDuration;
	}

	/**
	 * Set the ended duration of the battle
	 *
	 * @param int $duration
	 */
	public function setEndedDuration(int $duration) {
		$this->endedDuration = $duration;
	}

}