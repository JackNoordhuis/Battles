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

namespace jacknoordhuis\battles\utils\exception;

use jacknoordhuis\battles\event\BattlesEvent;

class BattlesEventException extends BattlesException {

	/** @var BattlesEvent */
	private $event;

	public function __construct(BattlesEvent $event, string $message = "") {
		$this->event = $event;
		$message .= " Event: " . $event->getShortName(); // hacky way of adding event name to exception message
		parent::__construct($event->getBattlesPlugin(), $message);
	}

	public function getEvent() : BattlesEvent {
		return $this->event;
	}

}