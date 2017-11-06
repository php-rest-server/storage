<?php
/**
 */

namespace RestCore\Storage\Exceptions;

/**
 * Class ColumnNotFoundException
 * @package RestCore\Storage\Exceptions
 */
class ColumnNotFoundException extends StorageException
{
    public function __construct($message = 'Column not found', $code = 500, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
