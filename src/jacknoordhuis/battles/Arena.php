<?php

/**
 * Arena.php class
 *
 * Created on 23/05/2016 at 10:45 PM
 *
 * @author JackNoordhuis
 */

namespace jacknoordhuis\battles;

use jacknoordhuis\Battles;
use pocketmine\math\Vector3;

/**
 * Basic arena for battles
 */
class Arena{

	/** @var string */
	private $name = "";

	/** @var Vector3[] */
	private $spawns;

	public function __construct(Battles $plugin, $name, array $spawns) {
		$this->name = $name;
		$this->spawns = $spawns;
	}

	/**
	 * @return Vector3
	 */
	public function getRandomSpawn() {
		return $this->spawns[array_rand($this->spawns)];
	}

}