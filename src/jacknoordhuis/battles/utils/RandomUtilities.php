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

class RandomUtilities {

	/**
	 * Alternative to array_map due to a foreach having better performance
	 *
	 * @param array $array
	 * @param callable $func
	 */
	public static function mapArrayWithCallable(array $array, Callable $func) {
		foreach($array as $key => $value) {
			call_user_func($func, $value);
		}
	}

	/**
	 * Alternative to array_map due to a foreach having better performance and returns the modified array
	 *
	 * @param array $array
	 * @param callable $func
	 *
	 * @return array
	 */
	public static function mapArrayWithCallableAndReturn(array $array, Callable $func) {
		$result = [];
		foreach($array as $key => $value) {
			$result[$key] = call_user_func($func, $value);
		}
		return $result;
	}

}