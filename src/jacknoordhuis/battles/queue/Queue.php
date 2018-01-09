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

namespace jacknoordhuis\battles\queue;

use jacknoordhuis\battles\session\PlayerSession;
use pocketmine\Player;

class Queue {

	/** @var QueueManager */
	private $manager;

	/** @var string */
	private $id;

	/** @var PlayerSession[] */
	private $queuedPlayers = [];

	public function __construct(QueueManager $manager) {
		$this->manager = $manager;
		$this->id = substr(md5((string) (mt_rand(-1023, 1024) + time())), 0, 12); // some random bunch of characters that are extremely unlikely to be repeated, limited to 12 characters long
	}

	public function getManager() : QueueManager {
		return $this->manager;
	}

	public function getId() : string {
		return $this->id;
	}

	/**
	 * @param int $count
	 *
	 * @return PlayerSession[]
	 */
	public function getRandomQueuedPlayers(int $count = 2) : array {
		$indexes = array_rand($this->queuedPlayers, $count);
		/** @var PlayerSession[] $players */
		$players = [];
		foreach($indexes as $index) {
			$session = $this->queuedPlayers[$index];
			if(!$session->inBattle()) {
				$players[] = $session;
			} else {
				$this->unqueuePlayer($session);
				return $this->getRandomQueuedPlayers($count);
			}
		}

		return $players;
	}

	/**
	 * @return PlayerSession[]
	 */
	public function getQueuedPlayers() : array {
		return $this->queuedPlayers;
	}

	/**
	 * @param string $name
	 *
	 * @return PlayerSession|null
	 */
	public function findQueuedPlayer(string $name) : ?PlayerSession {
		return $this->queuedPlayers[$name] ?? null;
	}

	/**
	 * @param PlayerSession $player
	 */
	public function queuePlayer(PlayerSession $player) : void {
		$this->queuedPlayers[$player->getOwner()->getName()] = $player;
	}

	/**
	 * @param Player|PlayerSession|string $player
	 */
	public function unqueuePlayer($player) : void {
		if($player instanceof PlayerSession) {
			$player = $player->getOwner()->getName();
		} elseif($player instanceof Player) {
			$player = $player->getName();
		}

		unset($this->queuedPlayers[$player]);
	}

	/**
	 * Remove all players from the queue
	 */
	public function unqueueAll() : void {
		foreach($this->queuedPlayers as $queued) {
			$queued->removeFromQueue();
		}

		if(($count = count($this->queuedPlayers)) > 0) {
			$this->getManager()->getPlugin()->getLogger()->debug("Failed to unqueue {$count} players from queue with id {$this->getId()}");
		}
	}

}