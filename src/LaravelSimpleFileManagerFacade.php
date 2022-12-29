<?php

namespace Vita\LaravelSimpleFileManager;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Vita\LaravelSimpleFileManager\Skeleton\SkeletonClass
 */
class LaravelSimpleFileManagerFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel simple file manager';
    }
}
