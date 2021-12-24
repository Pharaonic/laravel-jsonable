<?php

namespace Pharaonic\Laravel\Jsonable\Exceptions\Auth;

use Pharaonic\Laravel\Jsonable\Exceptions\Exception as Exception;

class AuthorizationException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        if ($response = parent::render($request))
            return $response;

        return abort(403, $this->getMessage());
    }
}
