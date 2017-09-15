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

use pocketmine\Player;

class PlayerSession {

	/** @var SessionManager */
	private $manager;

	/** @var Player */
	private $owner;

	/** @var int */
	private $status = self::STATUS_LOADING;

	/** @var string|null */
	private $battleId = null;

	/* Session statuses */
	const STATUS_LOADING = 0x00; // spawning
	const STATUS_LOBBY = 0x10; // not in a battle
	const STATUS_WAITING = self::STATUS_LOBBY | 0x01; // queuing for a battle
	const STATUS_PLAYING = 0x20; // in a battle
	const STATUS_COUNTDOWN = self::STATUS_PLAYING | 0x01; // in a battle countdown

	public function __construct(SessionManager $manager, Player $owner) {
		$this->manager = $manager;
		$this->owner = $owner;
	}

	public function getManager() : SessionManager {
		return $this->manager;
	}

	public function getOwner() : Player {
		return $this->owner;
	}

	public function getStatus() {
		return $this->status;
	}

	protected function checkStatus(int $status) : bool {
		return ($this->status & 0xF0) === $status;
	}

	public function isLoading() : bool {
		return $this->status === self::STATUS_LOADING;
	}

	public function inLobby() : bool {
		return $this->checkStatus(self::STATUS_LOBBY);
	}

	public function isWaiting() : bool {
		return $this->status === self::STATUS_WAITING;
	}

	public function isPlaying() : bool {
		return $this->checkStatus(self::STATUS_PLAYING);
	}

	public function inCountdown() : bool {
		return $this->status === self::STATUS_COUNTDOWN;
	}

	public function inBattle() : bool {
		return $this->isPlaying() and $this->battleId !== null;
	}

	public function onQuit() {
	}

}