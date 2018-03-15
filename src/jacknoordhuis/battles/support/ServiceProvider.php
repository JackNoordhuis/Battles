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

namespace jacknoordhuis\battles\support;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use jacknoordhuis\battles\contracts\foundation\Application;

abstract class ServiceProvider extends BaseServiceProvider{

	/** @var Application  */
	protected $app;

	/**
	 * Create a new service provider instance
	 *
	 * @param Application|\Illuminate\Contracts\Foundation\Application $app
	 */
	public function __construct($app) {
		parent::__construct($app);
	}

	protected function mergeConfigFrom($path, $key) {
		return;
	}

	protected function loadRoutesFrom($path) {
		return;
	}

	protected function loadViewsFrom($path, $namespace) {
		return;
	}

	protected function loadTranslationsFrom($path, $namespace) {
		return;
	}

	protected function loadJsonTranslationsFrom($path) {
		return;
	}

	protected function loadMigrationsFrom($paths) {
		return;
	}

	protected function publishes(array $paths, $group = null) {
		return;
	}

	protected function ensurePublishArrayInitialized($class) {
		return;
	}

	protected function addPublishGroup($group, $paths) {
		return;
	}

	public static function pathsToPublish($provider = null, $group = null) {
		return [];
	}

	protected static function pathsForProviderOrGroup($provider, $group) {
		return [];
	}

	protected static function pathsForProviderAndGroup($provider, $group) {
		return false;
	}

	public static function publishableProviders() {
		return [];
	}

	public static function publishableGroups() {
		return [];
	}

	public function commands($commands) {
		return;
	}

	public function isDeferred() {
		return false;
	}

}