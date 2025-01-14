<?php

declare(strict_types=1);

namespace App\Exceptions\Clients;

use Exception;

class RequestBodyEncodeException extends Exception
{
    public function __construct()
    {
        parent::__construct('Failed to encode request body to JSON');
    }
}
