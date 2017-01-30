<?php

/**
 * Battle.php class
 *
 * Created on 23/05/2016 at 11:01 PM
 *
 * @author JackNoordhuis
 */

namespace jacknoordhuis\battles;

use jacknoordhuis\Battles;
use pocketmine\Player;

/**
 * Battle class \o/
 */
class Battle {

	/** @var Battles */
	private $plugin;

	/** @var Player[] */
	private $players = [];

	/** @var int */
	private $lastTick = 0;

	/** @var bool */
	private $ended = false;

	/** @var string */
	private $state = self::STATE_WAITING;

	/** Battle states */
	const STATE_WAITING = "battle.waiting";
	const STATE_PLAYING = "battle.playing";

	public function __construct(Battles $plugin) {
		$this->plugin = $plugin;
	}

	public function getPlugin() : Battles {
		return $this->plugin;
	}

	public function start() {
		
	}

	/**
	 * Tick the battle to make things happen!
	 *
	 * @param $tick
	 */
	public function tick($tick) {
		$this->checkPlayers();
		$this->lastTick = $tick;
	}

	/**
	 * Remove players that have left
	 */
	private function checkPlayers() {
		foreach($this->players as $key => $player) {
			if(!$player instanceof Player) {
				unset($this->players[$key]);
				$this->broadcast("> {$key} quit");
			}
		}
	}

	public function hasEnded() {
		return $this->ended;
	}

}