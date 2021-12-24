<?php

namespace Pharaonic\Laravel\Jsonable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Json Responser Facade
 * 
 * @method \Illuminate\Http\JsonResponse success
 * @method \Illuminate\Http\JsonResponse errors
 * @method \Illuminate\Http\JsonResponse exception
 * 
 * @author Moamen Eltouny
 * @package pharoanic/laravel-jsonable
 * @version 0.0.7
 */
class Json extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'json';
    }
}
