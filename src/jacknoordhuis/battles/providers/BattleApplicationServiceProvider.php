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

namespace jacknoordhuis\battles\providers;

use jacknoordhuis\battles\arena\ArenaManager;
use jacknoordhuis\battles\contracts\foundation\Application;
use jacknoordhuis\battles\support\ServiceProvider;

class BattleApplicationServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap application services.
	 *
	 * @return void
	 */
	public function boot() : void {
		$this->app->singleton(ArenaManager::class, function(Application $app) {
			return new ArenaManager($app->plugin());
		});
	}

	/**
	 * Register the provider
	 *
	 * @return void
	 */
	public function register() : void {
	}

}