<?php

namespace Pharaonic\Laravel\Jsonable;

use Illuminate\Http\Resources\Json\JsonResource;
use Pharaonic\Laravel\Jsonable\Exceptions\Exception;

/**
 * Json Responser
 *
 * @method \Illuminate\Http\JsonResponse success
 * @method \Illuminate\Http\JsonResponse errors
 * @method \Illuminate\Http\JsonResponse exception
 *
 * @author Moamen Eltouny
 * @author Hatem Elsaid
 * @package pharoanic/laravel-jsonable
 * @version 0.0.7
 */
class Json
{
    public function success($data, ?string $message = null, ?array $extra = [], int $status = 200, array $headers = [])
    {
        if ($data instanceof JsonResource) {
            $data = $data->toArray(request());
        } elseif (!is_array($data)) {
            throw new \Exception('You have to pass $data as JsonResource or Array.');
        }

        return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data
            ] + ($extra ?? []), $status, $headers);
    }

    public function errors(array $errors, ?string $code, ?string $message = null, ?array $extra = [], int $status = 400, array $headers = [])
    {
        // ERRORS HANDLING
        if (!array_is_list($errors)) {
            $errors = array_values(array_map(function ($key, $errs) {
                if (is_array($errs) && count($errs) == 1)
                    $errs = $errs[0];

                return (object)[
                    'key' => $key,
                    'value' => $errs
                ];
            }, array_keys($errors), array_values($errors)));
        } else {
            $errors = array_map(function ($err) {
                return (object)$err;
            }, $errors);
        }

        // RESPONES
        return response()->json([
                'success' => false,
                'code' => $code,
                'message' => $message,
                'errors' => $errors
            ] + ($extra ?? []), $status, $headers);
    }

    public function exception(\Throwable $exception, ?string $code = null, ?string $message = null, ?array $extra = [], int $status = 400, array $headers = [])
    {
        return response()->json([
                'success' => false,
                'code' => $code ?? $exception->getCode() ?? null,
                'message' => $message ?? $exception->getMessage(),
                'data' => (object)(app()->environment('local', 'staging') ?
                    [
                        'line' => $exception->getLine(),
                        'file' => $exception->getFile(),
                    ] + (config('Pharaonic.jsonable.tracing', false) ? ['trace' => $exception->getTrace()] : [])
                    : []
                )
            ] + ($extra ?? []), $status, $headers);
    }
}
