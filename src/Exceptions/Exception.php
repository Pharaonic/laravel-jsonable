<?php

namespace Pharaonic\Laravel\Jsonable\Exceptions;

use Exception as GlobalException;

class Exception extends GlobalException
{
    protected ?string $exCode = null;

    public function __construct(string $message = "", ?string $code = null, ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->exCode = $code;
    }

    /**
     * Render the exception as an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        if ($request->wantsJson())
            return $request->header('accept') == 'application/vnd.api+json' ?
                $this->renderJsonApi() :
                $this->renderJson();

        if (!empty($this->exCode))
            $this->message = $this->exCode . ' :: ' . $this->message;

        return;
    }

    /**
     * Render the json response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function renderJson()
    {
        return response()->json([
            'success'   => false,
            'code'      => $this->exCode ?? $this->getCode() ?? null,
            'message'   => $this->getMessage(),
            'data'      => (object) (app()->environment('local', 'staging') ?
                [
                    'line'  => $this->getLine(),
                    'file'  => $this->getFile(),
                ] + (config('Pharaonic.jsonable.tracing', false) ? ['trace' => $this->getTrace()] : [])
                : []
            )
        ]);
    }

    /**
     * Render the JSON:API response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function renderJsonApi()
    {
        return response()->json([
            // 'success'   => false,
            // 'code'      => $this->exCode ?? $this->getCode() ?? null,
            // 'message'   => $this->getMessage(),
            // 'data'      => (object) (app()->environment('local', 'staging') ?
            //     [
            //         'line'  => $this->getLine(),
            //         'file'  => $this->getFile(),
            //     ] + (config('Pharaonic.jsonable.tracing', false) ? ['trace' => $this->getTrace()] : [])
            //     : []
            // )
        ]);
    }
}
