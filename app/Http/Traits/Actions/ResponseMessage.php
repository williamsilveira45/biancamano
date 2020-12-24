<?php

namespace App\Http\Traits\Actions;

/**
 * Trait ResponseMessage - used in action classes
 * @package App\Http\Traits\Actions
 */
trait ResponseMessage
{
    /**
     * @param string $message
     * @param array $extra
     * @return array
     */
    public function responseSuccess(string $message, array $extra = [])
    {
        return $this->responseMessage('success', true, $message, $extra);
    }

    /**
     * @param string $type
     * @param string $message
     * @param array $extra
     * @return array
     */
    public function responseFailure(string $type, string $message, array $extra = [])
    {
        return $this->responseMessage($type,false, $message, $extra);
    }

    /**
     * @param string $type
     * @param boolean $success
     * @param string $message
     * @param array $extra
     * @return array
     */
    private function responseMessage(string $type, bool $success, string $message, array $extra = [])
    {
        return $extra + [
                'type' => $type,
                'success' => $success,
                'message' => $message
            ];
    }
}
