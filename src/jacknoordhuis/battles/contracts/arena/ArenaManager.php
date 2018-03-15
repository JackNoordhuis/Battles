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

declare(strict_types=1);

namespace jacknoordhuis\battles\contracts\arena;

interface ArenaManager {

	/**
	 * Add an arena object to the manager.
	 *
	 * @param Arena $object
	 */
	public function add(Arena $object) : void;

	/**
	 * Check if an arena object exists in the manager.
	 *
	 * @param $identifier
	 *
	 * @return bool
	 */
	public function exists($identifier) : bool;

	/**
	 * Get an arena object from the manager.
	 *
	 * @param $identifier
	 *
	 * @return Arena|null
	 */
	public function get($identifier) : ?Arena;

}