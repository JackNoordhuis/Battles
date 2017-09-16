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

namespace jacknoordhuis\battles\battle;

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

	/** @var PlayerSession[] */
	private $players = [];

	/** @var int */
	private $lastTick = 0;

	/** @var bool */
	private $ended = false;

	/** @var string */
	private $state = self::STATE_WAITING;

	/* Battle states */
	const STATE_WAITING = "battle.waiting";
	const STATE_COUNTDOWN = "battle.countdown";
	const STATE_PLAYING = "battle.playing";

	public function __construct(BattleManager $manager) {
		$this->manager = $manager;
	}

	public function getManager() : BattleManager {
		return $this->manager;
	}

	public function getId() : string {
		return $this->id;
	}

	public function getState() : string {
		return $this->state;
	}

	public function hasStarted() : bool {
		return $this->state === self::STATE_PLAYING or $this->state === self::STATE_COUNTDOWN;
	}

	final public function countdown() {
		$this->onCountdown();
	}

	/**
	 * Actions to complete on countdown
	 */
	protected function onCountdown() {

	}

	final public function start() {
		$this->onStart();
	}

	/**
	 * Actions to complete on start
	 */
	protected function onStart() {

	}

	final public function end() {
		$this->onEnd();
	}

	/**
	 * Actions to complete on end
	 */
	protected function onEnd() {

	}

	/**
	 * Tick the battle to make things happen!
	 *
	 * @param $tick
	 *
	 * @return mixed
	 */
	public function tick(int $tick) {
		$tickDiff = $tick - $this->lastTick;
		if($tickDiff <= 0 or $this->ended){
			return;
		}
		$this->checkPlayers();
		$this->lastTick = $tick;
		return;
	}

	/**
	 * Remove players that have left
	 */
	private function checkPlayers() {
		foreach($this->players as $key => $player) {
			if(!$player instanceof PlayerSession) {
				unset($this->players[$key]);
				$this->broadcastMessage("> {$key} quit");
			}
		}
	}

	public function broadcastMessage(string $message) {
		RandomUtilities::mapArrayWithCallable($this->players, function(PlayerSession $session) use ($message) {
			$session->getOwner()->sendMessage($message);
		});
	}

	public function broadcastTitle(string $title, string $subtitle = "", int $fadeIn = -1, int $stay = -1, int $fadeOut = -1) {
		RandomUtilities::mapArrayWithCallable($this->players, function(PlayerSession $session) use ($title, $subtitle, $fadeIn, $stay, $fadeOut) {
			$session->getOwner()->addTitle($title, $subtitle, $fadeIn, $stay, $fadeOut);
		});
	}

	public function broadcastPopup(string $message) {
		RandomUtilities::mapArrayWithCallable($this->players, function(PlayerSession $session) use ($message) {
			$session->getOwner()->sendPopup($message);
		});
	}

	public function broadcastTip(string $message) {
		RandomUtilities::mapArrayWithCallable($this->players, function(PlayerSession $session) use ($message) {
			$session->getOwner()->sendTip($message);
		});
	}

	public function hasEnded() {
		return $this->ended;
	}

}