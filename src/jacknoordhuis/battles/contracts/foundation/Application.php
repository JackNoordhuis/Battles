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

namespace jacknoordhuis\battles\contracts\foundation;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use jacknoordhuis\battles\BattlesLoader;

interface Application extends Container {

	/**
	 * Get the version number of the application.
	 *
	 * @return string
	 */
	public function version() : string;

	/**
	 * Get the applications plugin wrapper
	 *
	 * @return BattlesLoader
	 */
	public function plugin() : BattlesLoader;

	/**
	 * Register a service provider with the application.
	 *
	 * @param \Illuminate\Support\ServiceProvider|string $provider
	 * @param array $options
	 * @param bool $force
	 *
	 * @return \Illuminate\Support\ServiceProvider
	 */
	public function register($provider, $options = [], $force = false) : ServiceProvider;

	/**
	 * Boot the application's service providers.
	 *
	 * @return void
	 */
	public function boot() : void;

	/**
	 * Register a new boot listener.
	 *
	 * @param \Closure $callback
	 *
	 * @return void
	 */
	public function booting(\Closure $callback) : void;

	/**
	 * Register a new "booted" listener.
	 *
	 * @param \Closure $callback
	 *
	 * @return void
	 */
	public function booted(\Closure $callback) : void;

}