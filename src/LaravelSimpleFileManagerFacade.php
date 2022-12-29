<?php

namespace Viettuans\LaravelSimpleFileManager;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Viettuans\LaravelSimpleFileManager\Skeleton\SkeletonClass
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
        return 'laravel-file-manager';
    }
}
