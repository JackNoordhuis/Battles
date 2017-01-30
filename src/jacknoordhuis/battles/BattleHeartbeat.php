<?php

/**
 * BattleHeartbeat.php class
 *
 * Created on 23/05/2016 at 11:04 PM
 *
 * @author JackNoordhuis
 */

namespace jacknoordhuis\battles;

use jacknoordhuis\Battles;
use pocketmine\scheduler\PluginTask;

class BattleHeartbeat extends PluginTask {

	/** @var Battles */
	private $plugin;

	public function __construct(Battles $plugin) {
		parent::__construct($plugin);
		$this->plugin = $plugin;
	}

	public function getPlugin() : Battles {
		return $this->plugin;
	}

	/**
	 * Ticks all battles
	 * 
	 * @param $tick
	 */
	public function onRun($tick) {
		foreach($this->plugin->getBattles() as $battle) {
			if(!$battle->hasEnded()) {
				$battle->tick($tick);
			}
		}
	}

}