<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use App\Traits\Restable;

class BaseRequest extends FormRequest
{
    use Restable;

    /**
     * Custom message for forbidden requests.
     */
    protected $forbidden_message = 'User not authorized to access resource';

    /**
     * Internal code to be displayed.
     */
    protected $forbidden_code = 40110;

    /**
     * Get the response for a forbidden operation.
     *
     * @return Response
     */
    public function forbiddenResponse()
    {
        return $this->respondUnauthorized(
            $this->forbidden_message,
            $this->forbidden_code
        );
    }
}
