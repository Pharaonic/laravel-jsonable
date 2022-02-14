<?php

if (!function_exists('json')) {
    /**
     * Create a new JSON response instance
     *
     * @return \Pharaonic\Laravel\Jsonable\Json
     */
    function json()
    {
        return app('json');
    }
}
