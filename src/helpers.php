<?php

if (!function_exists('json')) {
    /**
     * Create a new JSON response instance
     *
     * @return \App\Jsonable\Classes\Json
     */
    function json()
    {
        return app('json');
    }
}
