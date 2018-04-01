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
        if (config('app.debug')) {
            $data = [
                'code'          => $e->getCode(),
                'message'       => $e->getMessage(),
                'file'          => $e->getFile(),
                'line'          => $e->getLine(),
                'trace'         => $e->getTrace(),
                'traceAsString' => $e->getTraceAsString(),
            ];
        } else {
            $data = ['message' => $e->getMessage()];
        }
        $content = [
            'code'    => $e->getCode(),
            'message' => $e->getMessage(),
            'data'    => $data,
        ];

        if (di()->has('response')) {
            $response = di()->get('response');
        } else {
            $response = new Response();
        }

        $statusCode = 500;
        if (method_exists($e, 'getStatusCode')) {
            $statusCode = $e->getStatusCode();
        }
        $response->setStatusCode($statusCode);
        $response->setJsonContent($content);

        return $response->isSent() ? $response : $response->send();
    }
}
