<?php

/**
 * Battles.php class
 *
 * Created on 30/01/2017 at 8:46 PM
 *
 * @author JackNoordhuis
 */

namespace jacknoordhuis;

use jacknoordhuis\battles\Arena;
use jacknoordhuis\battles\Battle;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

/**
 * Simple battles plugin
 */
class Battles extends PluginBase {

	/** @var array */
	private $arenaData = [];

	/** @var Arena[] */
	private $arenas = [];

	/** @var Battle[] */
	private $battles = [];

	public function onEnable() {
		$this->loadConfigs();
	}

	public function loadConfigs() {
		$this->saveResource("arenas.yml");
		$this->arenaData = (new Config($this->getDataFolder() . "arenas.yml", Config::YAML))->getAll()["arenas"];
		$this->loadArenas();
	}

	/**
	 * Loads the arena data into arena classes
	 */
	public function loadArenas() {
		foreach($this->arenaData as $arena) {
			$this->arenas[] = new Arena($this, $arena["name"], self::parseVectors($arena["spawns"]));
		}
	}

	/**
	 * @return Battle[]
	 */
	public function getBattles() {
		return $this->battles;
	}

	/**
	 * Get a vector from a string \o/
	 *
	 * @param $string
	 * @return Vector3
	 */
	public static function parseVector($string) {
		$temp = explode(",", str_replace(" ", "", $string));
		return new Vector3($temp[0], $temp[1], $temp[2]);
	}

	/**
	 * Parse an array of vectors
	 *
	 * @param array $strings
	 *
	 * @return array
	 */
	public static function parseVectors(array $strings) {
		$vectors = [];
		foreach($strings as $string) {
			$vectors[] = self::parseVector($string);
		}
		return $vectors;
	}
}