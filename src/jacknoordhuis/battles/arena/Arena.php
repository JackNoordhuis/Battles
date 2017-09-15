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

namespace jacknoordhuis\battles\arena;

use pocketmine\math\Vector3;

/**
 * Basic arena for battles
 */
class Arena {

	/** @var string */
	private $name;

	/** @var string */
	private $display;

	/** @var string */
	private $author;

	/** @var Vector3[] */
	private $spawnPositions;

	public function __construct(string $name, string $display, string $author, array $spawns) {
		$this->name = $name;
		$this->display = $display;
		$this->author = $author;
		$this->spawnPositions = $spawns;
	}

	public function getName() : string {
		return $this->name;
	}

	public function getDisplay() : string {
		return $this->display;
	}

	public function getAuthor() : string {
		return $this->author;
	}

	/**
	 * @return Vector3
	 */
	public function getRandomSpawn() {
		return $this->spawnPositions[array_rand($this->spawnPositions)];
	}

}