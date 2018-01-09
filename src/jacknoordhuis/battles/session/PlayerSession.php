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

use jacknoordhuis\battles\battle\BaseBattle;
use jacknoordhuis\battles\queue\Queue;
use pocketmine\Player;

class PlayerSession {

	/** @var SessionManager */
	private $manager;

	/** @var Player */
	private $owner;

	/** @var int */
	private $status = self::STATUS_LOADING;

	/** @var string|null */
	private $queueId = null;

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

	/**
	 * @return SessionManager
	 */
	public function getManager() : SessionManager {
		return $this->manager;
	}

	/**
	 * @return Player
	 */
	public function getOwner() : Player {
		return $this->owner;
	}

	/**
	 * @return int
	 */
	public function getStatus() : int {
		return $this->status;
	}

	/**
	 * @return null|string
	 */
	public function getQueueId() {
		return $this->queueId;
	}

	/**
	 * @return null|string
	 */
	public function getBattleId() {
		return $this->battleId;
	}

	/**
	 * @return Queue|null
	 */
	public function getQueue() : ?Queue {
		return $this->manager->getPlugin()->getQueueManager()->getQueue($this->queueId);
	}

	/**
	 * @return BaseBattle|null
	 */
	public function getBattle() : ?BaseBattle {
		return $this->manager->getPlugin()->getBattleManager()->getBattle($this->battleId);
	}

	/**
	 * @param int $status
	 *
	 * @return bool
	 */
	protected function checkStatus(int $status) : bool {
		return ($this->status & 0xF0) === $status;
	}

	/**
	 * @return bool
	 */
	public function isLoading() : bool {
		return $this->status === self::STATUS_LOADING;
	}

	/**
	 * @return bool
	 */
	public function inLobby() : bool {
		return $this->checkStatus(self::STATUS_LOBBY);
	}

	/**
	 * @return bool
	 */
	public function isWaiting() : bool {
		return $this->status === self::STATUS_WAITING;
	}

	/**
	 * @return bool
	 */
	public function isPlaying() : bool {
		return $this->checkStatus(self::STATUS_PLAYING);
	}

	/**
	 * @return bool
	 */
	public function inCountdown() : bool {
		return $this->status === self::STATUS_COUNTDOWN;
	}

	/**
	 * @return bool
	 */
	public function isQueued() : bool {
		return  $this->inLobby() and $this->getQueue() instanceof Queue;
	}

	/**
	 * @return bool
	 */
	public function inBattle() : bool {
		return $this->isPlaying() and $this->getBattle() instanceof BaseBattle;
	}

	/**
	 * Add the player to a queue
	 *
	 * @param Queue $queue
	 */
	public function addToQueue(Queue $queue) {
		$this->queueId = $queue->getId();
		$queue->queuePlayer($this);
	}

	/**
	 * Add the player to a battle
	 *
	 * @param BaseBattle $battle
	 */
	public function addToBattle(BaseBattle $battle) {
		$this->battleId = $battle->getId();
	}

	/**
	 * Remove the player from their current queue
	 */
	public function removeFromQueue() {
		if($this->isQueued()) {
			$this->getQueue()->unqueuePlayer($this);
		}
	}

	public function onQuit() {
	}

}