<?php

namespace Viettuans\FileManager;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Viettuans\FileManager\Skeleton\SkeletonClass
 */
class FileManager extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'FileManager';
    }
}
