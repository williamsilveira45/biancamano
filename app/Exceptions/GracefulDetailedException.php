<?php

namespace App\Exceptions;

use Throwable;

/**
 * Class GracefulException
 * Can be used to avoid use of Log::error
 *
 * @package App\Exceptions
 */
class GracefulDetailedException extends \Exception
{
    /** @var array */
    public $details;

    /**
     * GracefulDetailedException constructor.
     *
     * @param string $message
     * @param array $details
     */
    public function __construct($message, $details = [])
    {
        $this->details = $details;
        parent::__construct($message);
    }

    /**
     * @return array
     */
    public function getDetails()
    {
        return $this->details;
    }
}
