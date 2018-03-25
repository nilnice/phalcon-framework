<?php

namespace Nilnice\Phalcon\Http;

class Response extends \Phalcon\Http\Response
{
    /**
     * Set exception or error content.
     *
     * @param \Exception $e
     * @param bool       $devMode
     */
    public function setExceptionContent(\Exception $e, $devMode = false): void
    {
        /** @var \Nilnice\Phalcon\Http\Request $request */
        $request = $this->getDI()->get('request');

        /** @var \Nilnice\Phalcon\Support\Message $message */
        $message = $this->getDI()->has(Service::MESSAGE)
            ? $this->getDI()->get(Service::MESSAGE) : null;

        $code = $e->getCode();
        $msg = $e->getMessage();

        if ($message && $message->has($code)) {
            $default = $message->get($code);
            $code = $default['code'];

            if (! $msg) {
                $msg = $default['message'];
            }
        } else {
            $code = 400;
        }

        $data = $userInfo = $devInfo = [];
        $msg = $msg ?? 'An unknown exception or error';
        if ($e instanceof Exception && $e->getUserInfo() !== null) {
            $userInfo = $e->getUserInfo();
        }

        if ($devMode === true) {
            $method = $request->getMethod();
            $uri = $request->getURI();

            if ($e instanceof Exception && $e->getDevInfo() !== null) {
                $devInfo = $e->getDevInfo();
            }
            $devInfo = array_merge($devInfo, [
                'message'       => $e->getMessage(),
                'file'          => $e->getFile(),
                'line'          => $e->getLine(),
                'request'       => $method . ' ' . $uri,
                'previous'      => $e->getPrevious(),
                'trace'         => $e->getTrace(),
                'traceAsString' => $e->getTraceAsString(),
            ]);
            $data = ['devInfo' => $devInfo, 'userInfo' => $userInfo];
        }

        $content = [
            'code'    => $e->getCode(),
            'message' => $msg,
            'data'    => $data,
        ];
        $this->setJsonContent($content);
        $this->setStatusCode($code);
    }

    /**
     * Set JSON content.
     *
     * @param mixed $content
     * @param int   $jsonOptions
     * @param int   $depth
     *
     * @return \Phalcon\Http\Response|void
     */
    public function setJsonContent($content, $jsonOptions = 0, $depth = 512)
    {
        parent::setJsonContent($content, $jsonOptions, $depth);

        $value = md5($this->getContent());
        $this->setContentType('application/json', 'UTF-8');
        $this->setHeader('E-Tag', $value);
    }
}
