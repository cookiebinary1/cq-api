<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class EntityException
 * @package App\Exceptions
 * @author Cookie
 */
class EntityException extends Exception
{
    /**
     * @return Response
     */
    public function render(): Response
    {
        return error(8001, message: $this->getMessage());
    }
}
