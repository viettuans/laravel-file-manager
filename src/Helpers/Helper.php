<?php

namespace Viettuans\FileManager\Helpers;

use Exception;
use Log;

class Helper
{
    /**
     * handleLogError
     *
     * @param  mixed $ex
     * @return void
     */
    public static function handleLogError(Exception $ex)
    {
        $messenger = $ex->getMessage() . ' - ' . $ex->getFile() . ' - line:' . $ex->getLine();
        Log::error($messenger);
    }
}
