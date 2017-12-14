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

namespace jacknoordhuis\battles\event;

use jacknoordhuis\battles\BattlesLoader;
use pocketmine\event\Event;

abstract class BattlesEvent extends Event {

	/** @var BattlesLoader */
	private $plugin;

	public function __construct(BattlesLoader $plugin) {
		$this->plugin = $plugin;
	}

	public function getBattlesPlugin() : BattlesLoader {
		return $this->plugin;
	}

	/**
	 * Short hand method for calling the event
	 */
	final public function call() {
		$this->plugin->getServer()->getPluginManager()->callEvent($this);
	}

	/**
	 * Get the short name of the event class
	 *
	 * @return string
	 */
	final public function getShortName() : string {
		return (new \ReflectionObject($this))->getShortName();
	}

}