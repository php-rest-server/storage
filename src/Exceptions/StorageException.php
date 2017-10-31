<?php
/**
 */

namespace RestCore\Storage\Exceptions;

use Throwable;

class StorageException extends \Exception
{
    public function __construct($message = 'Storage error', $code = 500, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}