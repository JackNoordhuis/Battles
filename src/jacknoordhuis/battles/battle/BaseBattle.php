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

namespace jacknoordhuis\battles\battle;

use jacknoordhuis\battles\arena\Arena;
use jacknoordhuis\battles\battle\utils\exception\battle\UnhandledBattleStageException;
use jacknoordhuis\battles\event\battle\BattleCountdownStartEvent;
use jacknoordhuis\battles\event\battle\BattleStartEvent;
use jacknoordhuis\battles\event\battle\BattleEndEvent;
use jacknoordhuis\battles\session\PlayerSession;
use jacknoordhuis\battles\utils\RandomUtilities;

/**
 * BaseBattle class \o/
 */
abstract class BaseBattle {

	/** @var BattleManager */
	private $manager;

	/** @var string */
	private $id = null;

	/** @var string */
	private $arenaName;

	/** @var PlayerSession[] */
	private $sessions = [];

	/** @var int */
	private $lastTick = 0;

	/** @var int */
	private $countdownDuration; // time in seconds the battle's countdown stage should last for

	/** @var int */
	private $countdownTime = 0; // time the countdown stage has lasted for in seconds

	/** @var int */
	private $playingDuration; // time in seconds the battle's playing stage should last for

	/** @var int */
	private $playingTime = 0; // time the playing stage has lasted for in seconds

	/** @var int */
	private $endedDuration; // time in seconds the battle's ended stage should last for

	/** @var int */
	private $endedTime = 0; // time the ended stage has lasted for in seconds

	/** @var string */
	private $stage = self::STAGE_WAITING;

	/* Battle stages */
	const STAGE_WAITING = "battle.waiting";
	const STAGE_COUNTDOWN = "battle.countdown";
	const STAGE_PLAYING = "battle.playing";
	const STAGE_ENDED = "battle.ended";
	const STAGE_CLOSED = "battle.closed";

	public function __construct(BattleManager $manager, Arena $arena, int $countdownDuration, int $playingDuration, int $endedDuration) {
		$this->manager = $manager;
		$this->id = substr(md5((string) ($this->arenaName . time())), 0, 12); // some random bunch of characters that are extremely unlikely to be repeated, limited to 12 characters long
		$this->arenaName = $arena->getName();
		$this->countdownDuration = $countdownDuration;
		$this->playingDuration = $playingDuration;
		$this->endedDuration = $endedDuration;
	}

	public function getManager() : BattleManager {
		return $this->manager;
	}

	public function getId() : string {
		return $this->id;
	}

	public function getArena() : Arena{
		return $this->manager->getPlugin()->getArenaManager()->getArena($this->arenaName);
	}

	/**
	 * Get the number of seconds the countdown stage lasts for
	 *
	 * @return int
	 */
	public function getCountdownDuration() : int {
		return $this->countdownDuration;
	}

	/**
	 * Set the number of seconds the countdown stage lasts for
	 *
	 * @param int $duration
	 */
	public function setCountdownDuration(int $duration) : void {
		$this->countdownDuration = $duration;
	}

	/**
	 * Get the number of seconds the playing stage lasts for
	 *
	 * @return int
	 */
	public function getPlayingDuration() : int {
		return $this->playingDuration;
	}

	/**
	 * Set the number of seconds the playing stage lasts for
	 *
	 * @param int $duration
	 */
	public function setPlayingDuration(int $duration) : void {
		$this->playingDuration = $duration;
	}

	/**
	 * Get the number of seconds the ended stage lasts for
	 *
	 * @return int
	 */
	public function getEndedDuration() : int {
		return $this->endedDuration;
	}

	/**
	 * Set the number of seconds the ended stage lasts for
	 *
	 * @param int $duration
	 */
	public function setEndedDuration(int $duration) : void {
		$this->endedDuration = $duration;
	}

	/**
	 * Get the current stage of the battle
	 *
	 * @return string
	 */
	public function getStage() : string {
		return $this->stage;
	}

	/**
	 * Actions to complete when the battle is ticked during the waiting stage
	 */
	protected function doWaitingTick() : void {
		$this->broadcastTip("Waiting for players...");
	}

	/**
	 * Check if the battle has started
	 *
	 * @return bool
	 */
	public function hasStarted() : bool {
		return $this->stage === self::STAGE_PLAYING;
	}

	/**
	 * Start the countdown for the battle
	 *
	 * @param int $cause
	 */
	final public function startCountdown(int $cause = BattleCountdownStartEvent::CAUSE_UNKNOWN) : void {
		($ev = new BattleCountdownStartEvent($this, $cause))->call(); // call the battle countdown start event
		if($ev->isCancelled() and $ev->getCause() !== BattleCountdownStartEvent::CAUSE_TICKING) {
			return;
		}
		$this->onCountdownStart(); // do all the countdown start things
	}

	/**
	 * Actions to complete when the countdown starts
	 *
	 * NOTE: If overwritten by child classes this parent function should still
	 * be called to prevent any unexpected behaviour.
	 */
	protected function onCountdownStart() : void {
		$this->stage = self::STAGE_COUNTDOWN;
	}

	/**
	 * Actions to complete when the battle is ticked during the countdown stage
	 */
	protected function doCountdownTick() : void {
		if($this->countdownTime < $this->countdownDuration) {
			$this->broadcastTitle("Battle starts in {$this->countdownTime}");
			$this->countdownTime++;
		} else {
			$this->start(BattleStartEvent::CAUSE_TICKING);
		}
	}

	/**
	 * Start the battle!
	 *
	 * @param int $cause
	 */
	final public function start(int $cause = BattleStartEvent::CAUSE_UNKNOWN) : void {
		($ev = new BattleStartEvent($this, $cause))->call(); // call the battle start event
		if($ev->isCancelled() and $ev->getCause() !== BattleStartEvent::CAUSE_TICKING) {
			return;
		}
		$this->onStart(); // do all the start things
	}

	/**
	 * Actions to complete when the match starts
	 *
	 * NOTE: If overwritten by child classes this parent function should still
	 * be called to prevent any unexpected behaviour.
	 */
	protected function onStart() : void {
		$this->stage = self::STAGE_PLAYING;
	}

	/**
	 * Actions to complete when the battle is ticked during the playing stage
	 */
	protected function doPlayingTick() : void {
		if($this->playingTime < $this->playingDuration) {
			$this->broadcastTitle("Battle ended!");
		} else {
			$this->end(BattleEndEvent::CAUSE_TICKING);
		}
	}

	/**
	 * End the battle
	 *
	 * @param int $cause
	 */
	final public function end(int $cause = BattleEndEvent::CAUSE_UNKNOWN) : void {
		($ev = new BattleEndEvent($this, $cause))->call(); // call the battle stop event
		if($ev->isCancelled() and $ev->getCause() !== BattleEndEvent::CAUSE_TICKING) {
			return;
		}
		$this->onEnd();
	}

	/**
	 * Actions to complete when the battle has ended
	 *
	 * NOTE: If overwritten by child classes this parent function should still
	 * be called to prevent any unexpected behaviour.
	 */
	protected function onEnd() : void {
		$this->stage = self::STAGE_ENDED;
	}

	/**
	 * Actions to complete when the battle is ticked during the ended stage
	 */
	protected function doEndedTick() : void {
		if($this->endedTime < $this->endedDuration) {
			$this->endedTime++;
		} else {
			foreach($this->sessions as $session) {
				$p = $session->getOwner();
				$p->getInventory()->clearAll();
				$p->teleport($this->manager->getPlugin()->getServer()->getDefaultLevel()->getSafeSpawn());
			}

			$this->close();
		}
	}

	public function close() : void {
		if(!$this->stage === self::STAGE_CLOSED) {
			$this->stage = self::STAGE_CLOSED;
			$this->manager->getPlugin()->getArenaManager()->addAvailableArena($this->arenaName);
			$this->manager->removeBattle($this->id);
		}
	}

	/**
	 * Tick the battle to make things happen!
	 *
	 * @param $tick
	 */
	public function tick(int $tick) : void {
		$tickDiff = $tick - $this->lastTick;
		if($tickDiff <= 0){
			return;
		}

		switch($this->stage) {
			case self::STAGE_PLAYING:
				$this->doPlayingTick();
				break;
			case self::STAGE_COUNTDOWN:
				$this->doCountdownTick();
				break;
			case self::STAGE_ENDED:
				$this->doEndedTick();
				break;
			case self::STAGE_WAITING:
				$this->doWaitingTick();
				break;
			default:
				throw new UnhandledBattleStageException($this);
		}

		$this->lastTick = $tick;
	}

	/**
	 * Broadcast a message to all players in the battle
	 *
	 * @param string $message
	 */
	public function broadcastMessage(string $message) : void {
		RandomUtilities::mapArrayWithCallable($this->sessions, function(PlayerSession $session) use ($message) {
			$session->getOwner()->sendMessage($message);
		});
	}

	/**
	 * Broadcast a title to all players in the battle
	 *
	 * @param string $title
	 * @param string $subtitle
	 * @param int $fadeIn
	 * @param int $stay
	 * @param int $fadeOut
	 */
	public function broadcastTitle(string $title, string $subtitle = "", int $fadeIn = -1, int $stay = -1, int $fadeOut = -1) : void {
		RandomUtilities::mapArrayWithCallable($this->sessions, function(PlayerSession $session) use ($title, $subtitle, $fadeIn, $stay, $fadeOut) {
			$session->getOwner()->addTitle($title, $subtitle, $fadeIn, $stay, $fadeOut);
		});
	}

	/**
	 * Broadcast a popup to all players in the battle
	 *
	 * @param string $message
	 */
	public function broadcastPopup(string $message) : void {
		RandomUtilities::mapArrayWithCallable($this->sessions, function(PlayerSession $session) use ($message) {
			$session->getOwner()->sendPopup($message);
		});
	}

	/**
	 * Broadcast a tip to all players in the battle
	 *
	 * @param string $message
	 */
	public function broadcastTip(string $message) : void {
		RandomUtilities::mapArrayWithCallable($this->sessions, function(PlayerSession $session) use ($message) {
			$session->getOwner()->sendTip($message);
		});
	}

}