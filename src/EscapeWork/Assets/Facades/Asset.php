<?php namespace EscapeWork\Assets\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \EscapeWork\Assets\Asset
 */
class Asset extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'escapework.asset'; }

}
