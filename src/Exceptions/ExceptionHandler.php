<?php

namespace Pharaonic\Laravel\Jsonable\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Routing\Router;
use Illuminate\Validation\ValidationException;
use Throwable;

class ExceptionHandler extends Handler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        if (method_exists($e, 'render') && $response = $e->render($request)) {
            return $response ? Router::toResponse($request, $response) : $this->pushExpcetion($request, $e);
        } elseif ($e instanceof Responsable) {
            return $e->toResponse($request);
        }

        $e = $this->prepareException($this->mapException($e));

        foreach ($this->renderCallbacks as $renderCallback) {
            foreach ($this->firstClosureParameterTypes($renderCallback) as $type) {
                if (is_a($e, $type)) {
                    $response = $renderCallback($e, $request);

                    if (!is_null($response)) {
                        return $response;
                    }
                }
            }
        }

        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        } elseif ($e instanceof AuthenticationException) {
            $trans = __('auth.unauthenticated');
            if ($trans == 'auth.unauthenticated') $trans = null;

            return $this->shouldReturnJson($request, $e)
                ? json()->exception($e, null, $trans, 401)
                : redirect()->guest($e->redirectTo() ?? route('login'));
        } elseif ($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        }

        return $this->pushExpcetion($request, $e);
    }

    /**
     * Push the exception to the client-end
     *
     * @param \Illuminate\Http\Request  $request
     * @param \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function pushExpcetion($request, $e)
    {
        return $this->shouldReturnJson($request, $e)
            ? $this->prepareJsonResponse($request, $e)
            : $this->prepareResponse($request, $e);
    }

    /**
     * Create a response object from the given validation exception.
     *
     * @param  \Illuminate\Validation\ValidationException  $e
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        if ($e->response) {
            return $e->response;
        }

        return $this->shouldReturnJson($request, $e) ?
            json()->errors(
                $e->validator->errors()->toArray(),
                $e->validator->code ?? null,
                __($e->validator->message ?? null),
                null,
                422
            ) : $this->invalid($request, $e);
    }

    /**
     * Prepare a JSON response for the given exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Illuminate\Http\JsonResponse
     */
    protected function prepareJsonResponse($request, Throwable $e)
    {
        return json()->exception(
            $e,
            null,
            null,
            [],
            $this->isHttpException($e) ? $e->getStatusCode() : 500
        );

        // new JsonResponse(
        //     $this->convertExceptionToArray($e),
        //     $this->isHttpException($e) ? $e->getStatusCode() : 500,
        //     $this->isHttpException($e) ? $e->getHeaders() : [],
        //     JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        // );
    }
}
