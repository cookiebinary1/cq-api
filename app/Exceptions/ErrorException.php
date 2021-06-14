<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

/**
 * Class ErrorException
 * @package App\Exceptions
 * @author Cookie
 */
class ErrorException extends Exception
{
    /**
     * @return Response
     */
    public function render(): Response
    {
        return error($this->getMessage());
    }
}
