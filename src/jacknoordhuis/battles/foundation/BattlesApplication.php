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

namespace jacknoordhuis\battles\foundation;

use Illuminate\Container\Container;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use jacknoordhuis\battles\support\ServiceProvider;
use jacknoordhuis\battles\BattlesLoader;
use jacknoordhuis\battles\contracts\foundation\Application as ApplicationContract;
use jacknoordhuis\battles\providers\BattleApplicationServiceProvider;

/**
 * BattlesApplication class – where all the magic happens.
 *
 *
 *
 * A list of methods to overwrite the return types of to provide
 * IDE auto-completion.
 *
 * @method BattlesLoader|mixed get(string $abstract)
 */
class BattlesApplication extends Container implements ApplicationContract {

	/** @var bool */
	private $booted = false;

	/** @var \Closure[] */
	protected $bootingCallbacks = [];

	/** @var \Closure[] */
	protected $bootedCallbacks = [];

	/** @var \Closure[] */
	protected $terminatingCallbacks = [];

	/** @var ServiceProvider[] */
	protected $serviceProviders = [];

	/** @var ServiceProvider[] */
	protected $loadedProviders = [];

	public function __construct(BattlesLoader $plugin) {
		$this->registerBaseBindings($plugin);
	}

	/**
	 * Get the version string of the application
	 *
	 * @return string
	 */
	public function version() : string {
		return $this->plugin()->getDescription()->getVersion();
	}

	/**
	 * Get the plugin that loaded the application
	 *
	 * @return BattlesLoader
	 */
	public function plugin() : BattlesLoader {
		return $this->get("plugin");
	}

	/**
	 * Get the application instance
	 *
	 * @return BattlesApplication
	 */
	public function app() : BattlesApplication {
		return $this;
	}

	/**
	 * Register the basic bindings into the container
	 *
	 * @param BattlesLoader $plugin
	 *
	 * @return void
	 */
	protected function registerBaseBindings(BattlesLoader $plugin) : void {
		$this->instance("app", $this);
		$this->instance(Container::class, $this);

		$this->instance("plugin", $plugin);
		$this->instance(BattlesLoader::class, $plugin);
	}

	/**
	 * Register all of the base service providers.
	 *
	 * @return void
	 */
	protected function registerBaseServiceBindings() : void {
		$this->register(new BattleApplicationServiceProvider($this));
	}

	/**
	 * Register a service provider with the application.
	 *
	 * @param  ServiceProvider|string  $provider
	 * @param  array  $options
	 * @param  bool   $force
	 *
	 * @return ServiceProvider|BaseServiceProvider
	 */
	public function register($provider, $options = [], $force = false) : BaseServiceProvider {
		if(($registered = $this->getProvider($provider)) && ! $force) {
			return $registered;
		}

		// If the given "provider" is a string, we will resolve it, passing in the
		// application instance automatically for the developer. This is simply
		// a more convenient way of specifying your service provider classes.
		if(is_string($provider)) {
			$provider = $this->resolveProvider($provider);
		}

		if(method_exists($provider, "register")) {
			$provider->register();
		}

		// If there are bindings / singletons set as properties on the provider we
		// will spin through them and register them with the application, which
		// serves as a convenience layer while registering a lot of bindings.
		if(property_exists($provider, "bindings")) {
			foreach($provider->bindings as $key => $value) {
				$this->bind($key, $value);
			}
		}

		if(property_exists($provider, "singletons")) {
			foreach($provider->singletons as $key => $value) {
				$this->singleton($key, $value);
			}
		}

		$this->markAsRegistered($provider);
		// If the application has already booted, we will call this boot method on
		// the provider class so it has an opportunity to do its boot logic and
		// will be ready for any usage by this developer's application logic.

		if($this->booted) {
			$this->bootProvider($provider);
		}

		return $provider;
	}

	/**
	 * Get the registered service provider instance if it exists.
	 *
	 * @param ServiceProvider|string $provider
	 *
	 * @return ServiceProvider|null
	 */
	public function getProvider($provider) {
		return array_values($this->getProviders($provider))[0] ?? null;
	}

	/**
	 * Get the registered service provider instances if any exist.
	 *
	 * @param ServiceProvider|string $provider
	 *
	 * @return ServiceProvider[]
	 */
	public function getProviders($provider) : array {
		$name = is_string($provider) ? $provider : get_class($provider);

		return Arr::where($this->serviceProviders, function($value) use ($name) {
			return $value instanceof $name;
		});
	}

	/**
	 * Resolve a service provider instance from the class name.
	 *
	 * @param string $provider
	 *
	 * @return ServiceProvider
	 */
	public function resolveProvider($provider) : ServiceProvider {
		return new $provider($this);
	}

	/**
	 * Mark the given provider as registered.
	 *
	 * @param ServiceProvider $provider
	 *
	 * @return void
	 */
	protected function markAsRegistered($provider) : void {
		$this->serviceProviders[] = $provider;
		$this->loadedProviders[get_class($provider)] = true;
	}

	/**
	 * Determine if the application has booted.
	 *
	 * @return bool
	 */
	public function isBooted() : bool {
		return $this->booted;
	}

	/**
	 * Boot the application's service providers.
	 *
	 * @return void
	 */
	public function boot() : void {
		if($this->booted) {
			return;
		}

		// Once the application has booted we will also fire some "booted" callbacks
		// for any listeners that need to do work after this initial booting gets
		// finished. This is useful when ordering the boot-up processes we run.
		$this->fireAppCallbacks($this->bootingCallbacks);

		array_walk($this->serviceProviders, function ($p) {
			$this->bootProvider($p);
		});

		$this->booted = true;

		$this->fireAppCallbacks($this->bootedCallbacks);
	}

	/**
	 * Boot the given service provider.
	 *
	 * @param ServiceProvider $provider
	 *
	 * @return mixed
	 */
	protected function bootProvider(ServiceProvider $provider) {
		if(method_exists($provider, "boot")) {
			return $this->call([$provider, "boot"]);
		}

		return null;
	}

	/**
	 * Register a new boot listener.
	 *
	 * @param \Closure $callback
	 *
	 * @return void
	 */
	public function booting(\Closure $callback) : void {
		$this->bootingCallbacks[] = $callback;
	}

	/**
	 * Register a new "booted" listener.
	 *
	 * @param \Closure $callback
	 *
	 * @return void
	 */
	public function booted(\Closure $callback) : void {
		$this->bootedCallbacks[] = $callback;

		if($this->isBooted()) {
			$this->fireAppCallbacks([$callback]);
		}
	}

	/**
	 * Call the booting callbacks for the application.
	 *
	 * @param \Closure[] $callbacks
	 *
	 * @return void
	 */
	protected function fireAppCallbacks(array $callbacks) : void {
		foreach($callbacks as $callback) {
			call_user_func($callback, $this);
		}
	}

	/**
	 * Register a terminating callback with the application.
	 *
	 * @param \Closure $callback
	 *
	 * @return $this
	 */
	public function terminating(\Closure $callback) {
		$this->terminatingCallbacks[] = $callback;

		return $this;
	}

	/**
	 * Terminate the application.
	 *
	 * @return void
	 */
	public function terminate() : void {
		foreach($this->terminatingCallbacks as $terminating) {
			$this->call($terminating);
		}
	}

	/**
	 * Get the service providers that have been loaded.
	 *
	 * @return ServiceProvider[]
	 */
	public function getLoadedProviders() : array {
		return $this->loadedProviders;
	}

	/**
	 * Flush the container of all bindings and resolved instances.
	 *
	 * @return void
	 */
	public function flush() {
		parent::flush();

		$this->buildStack = [];
		$this->loadedProviders = [];
		$this->bootedCallbacks = [];
		$this->bootingCallbacks = [];
		$this->reboundCallbacks = [];
		$this->serviceProviders = [];
		$this->resolvingCallbacks = [];
		$this->afterResolvingCallbacks = [];
		$this->globalResolvingCallbacks = [];
	}

}