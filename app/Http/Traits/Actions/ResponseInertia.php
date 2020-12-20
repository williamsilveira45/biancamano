<?php

namespace App\Http\Traits\Actions;

/**
 * Trait ResponseMessage - used in action classes
 * @package App\Http\Traits\Actions
 */
trait ResponseInertia
{
    /**
     * @param string $message
     * @param array $extra
     * @return array
     */
    public function responseSuccess(string $message, array $extra = [])
    {
        return $this->responseMessage(true, $message, $extra);
    }

    /**
     * @param string $message
     * @param array $extra
     * @return array
     */
    public function responseFailure(string $message, array $extra = [])
    {
        return $this->responseMessage(false, $message, $extra);
    }

    /**
     * @param boolean $success
     * @param string $message
     * @param array $extra
     * @return array
     */
    private function responseMessage(bool $success, string $message, array $extra = [])
    {
        return $extra + [
                'success' => $success,
                'message' => $message
            ];
    }
}
