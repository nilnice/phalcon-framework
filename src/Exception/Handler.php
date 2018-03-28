<?php

namespace Nilnice\Phalcon\Exception;

use Exception;
use Nilnice\Phalcon\Http\Response;

class Handler
{
    /**
     * @param \Exception $e
     * @param bool       $isTerminal
     *
     * @return \Nilnice\Phalcon\Http\Response
     */
    public function render(Exception $e, $isTerminal = false): Response
    {
        $content = [
            'code'    => $e->getCode(),
            'message' => $e->getMessage(),
            'data'    => [],
        ];

        if (di()->has('response')) {
            $response = di()->get('response');
        } else {
            $response = new Response();
        }

        if (method_exists($e, 'getStatusCode')) {
            $response->setStatusCode($e->getStatusCode());
        }

        $response->setJsonContent($content);

        return $response->isSent() ? $response : $response->send();
    }
}
