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

namespace jacknoordhuis\battles\queue\utils\exception\queue;

use jacknoordhuis\battles\queue\Queue;
use jacknoordhuis\battles\queue\utils\exception\QueueException;

class DuplicateQueueIdentifierException extends QueueException {

	public function __construct(Queue $queue) {
		parent::__construct($queue, "Could not add queue to pool due to duplicate ID! ID: {$queue->getId()}");
	}

}