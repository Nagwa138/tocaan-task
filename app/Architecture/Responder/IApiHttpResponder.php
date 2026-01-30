<?php

namespace App\Architecture\Responder;

interface IApiHttpResponder
{
    /**
     * @param array $data
     * @param int $code
     * @return mixed
     */
    public function sendSuccess(array $data = [], int $code = 200): mixed;

    /**
     * @param string|null $message
     * @param int $code
     * @return mixed
     */
    public function sendError(string $message = null, int $code = 400): mixed;
}
