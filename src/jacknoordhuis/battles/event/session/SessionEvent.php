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

namespace jacknoordhuis\battles\event\session;

use jacknoordhuis\battles\session\PlayerSession;

abstract class SessionEvent extends SessionManagerEvent {

	/** @var string */
	private $sessionId;

	public function __construct(PlayerSession $session) {
		$this->sessionId = spl_object_hash($session->getOwner());
		parent::__construct($session->getManager());
	}

	public function getSession() : PlayerSession {
		return $this->getSessionManager()->getSession($this->sessionId);
	}

}