<?php

use App\Jobs\FileHandlerJob;

if(!function_exists('uploadFile'))
{
    function uploadFile($file, $path = ''): string
    {
        return app(FileHandlerJob::class)::uploadFile($file, $path);
    }

    function removeFile($file): void
    {
        app(FileHandlerJob::class)::removeFile($file);
    }
}
