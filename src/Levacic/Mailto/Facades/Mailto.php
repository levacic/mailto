<?php namespace Levacic\Mailto\Facades;

use Illuminate\Support\Facades\Facade;

class Mailto extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'mailto'; }

}
