<?php namespace Levacic\Mailto;

use Illuminate\Support\ServiceProvider;

class MailtoServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('levacic/mailto');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['mailto'] = $this->app->share(function($app)
		{
			return new Mailto;
		});
	}

}
