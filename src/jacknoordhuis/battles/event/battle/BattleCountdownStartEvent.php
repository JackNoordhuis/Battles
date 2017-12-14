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

namespace jacknoordhuis\battles\event\battle;

use jacknoordhuis\battles\battle\BaseBattle;
use pocketmine\event\Cancellable;

class BattleCountdownStartEvent extends BattleEvent implements Cancellable {

	public static $handlerList = null;

	const CAUSE_UNKNOWN = -1;
	const CAUSE_TICKING = 0;
	const CAUSE_CUSTOM = 1;

	/** @var int */
	private $cause = self::CAUSE_UNKNOWN;

	public function __construct(BaseBattle $battle, int $cause) {
		$this->cause = $cause;
		parent::__construct($battle);
	}

	public function getCause() : int {
		return $this->cause;
	}

}