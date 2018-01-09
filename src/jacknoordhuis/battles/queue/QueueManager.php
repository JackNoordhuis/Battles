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

use jacknoordhuis\battles\BattlesLoader;
use jacknoordhuis\battles\queue\utils\exception\queue\DuplicateQueueIdentifierException;

class QueueManager {

	/** @var BattlesLoader */
	private $plugin;

	/** @var Queue[] */
	private $queuePool = [];

	public function __construct(BattlesLoader $plugin) {
		$this->plugin = $plugin;
	}

	public function getPlugin() : BattlesLoader {
		return $this->plugin;
	}

	/**
	 * Add queue to the pool
	 *
	 * @param Queue $queue
	 *
	 * @throws DuplicateQueueIdentifierException
	 */
	public function addQueue(Queue $queue) : void {
		if(!$this->queueExists($queue->getId())) {
			$this->queuePool[$queue->getId()] = $queue;
		} else {
			throw new DuplicateQueueIdentifierException($queue);
		}
	}

	/**
	 * @return Queue[]
	 */
	public function getQueues() : array {
		return $this->queuePool;
	}

	/**
	 * Get a queue from the pool
	 *
	 * @param string $id
	 *
	 * @return Queue|null
	 */
	public function getQueue(string $id) : ? Queue {
		return $this->queuePool[$id] ?? null;
	}

	/**
	 * Check if a queue is in the pool
	 *
	 * @param string $id
	 *
	 * @return bool
	 */
	public function queueExists(string $id) : bool {
		return isset($this->queuePool[$id]);
	}

	/**
	 * @param string $id
	 */
	public function removeQueue(string $id) : void {
		if($this->queueExists($id)) {
			$this->queuePool[$id]->unqueueAll();
			unset($this->queuePool[$id]);
		}
	}

}