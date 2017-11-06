<?php
/**
 */

namespace RestCore\Storage\Exceptions;

/**
 * Class SchemaNotFoundException
 * @package RestCore\Storage\Exceptions
 */
class SchemaNotFoundException extends StorageException
{
    public function __construct($message = 'Schema not found', $code = 500, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
