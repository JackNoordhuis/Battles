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

namespace jacknoordhuis\battles\utils;

use pocketmine\level\WeakPosition;
use pocketmine\math\Vector3;
use pocketmine\Server;

/**
 * Bunch of random functions to help parse config values
 */
class ConfigParseHelper {

	/**
	 * Read a bool from a config
	 *
	 * @param string $value
	 *
	 * @return bool
	 */
	public static function getBoolValue(string $value) : bool {
		if(is_bool($value)){
			return $value;
		}
		switch(strtolower($value)){
			case "on":
			case "true":
			case "1":
			case "yes":
				return true;
		}
		return false;
	}

	/**
	 * Get a vector from a string \o/
	 *
	 * @param string $string
	 * @return Vector3
	 */
	public static function parseVector(string $string) {
		$temp = explode(",", str_replace(" ", "", $string));
		return new Vector3((float) $temp[0], (float) $temp[1], (float) $temp[2]);
	}

	/**
	 * Get a position instance from a string \o/
	 *
	 * @param string $string
	 *
	 * @return WeakPosition
	 */
	public static function parsePosition(string $string) {
		$temp = explode(",", str_replace(" ", "", $string));
		$server = Server::getInstance();
		return new WeakPosition((float) $temp[0], (float) $temp[1], (float) $temp[2], $server->getLevel($temp[3]) ?? $server->getDefaultLevel());
	}

	/**
	 * Parse an array of vectors
	 *
	 * @param array $strings
	 *
	 * @return array
	 */
	public static function parseVectors(array $strings) {
		return RandomUtilities::mapArrayWithCallableAndReturn($strings, ["ConfigParseHelper::parseVector"]);
	}

	/**
	 * Parse an array of positions
	 *
	 * @param array $strings
	 *
	 * @return array
	 */
	public static function parsePositions(array $strings) {
		return RandomUtilities::mapArrayWithCallableAndReturn($strings, ["ConfigParseHelper::parsePosition"]);
	}

}