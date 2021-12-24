<?php

namespace Pharaonic\Laravel\Jsonable\Requests;

use Pharaonic\Laravel\Jsonable\Exceptions\Auth\AuthorizationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class JsonableFormRequest extends FormRequest
{
    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $validator->code = $this->errorCode ?? null;
        $validator->message = $this->errorMessage ?? null;
        parent::failedValidation($validator);
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws \App\Jsonable\Exceptions\Auth\AuthorizationException
     */
    protected function failedAuthorization()
    {
        $trans = __('auth.action-unauthorized');
        if ($trans == 'auth.action-unauthorized') $trans = 'This action is unauthorized.';

        throw new AuthorizationException($trans, $this->errorCode ?? null);
    }
}
