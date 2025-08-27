<?php

namespace Core\Http;

class Response
{
    protected $headers = [];

    protected $statusCode = 200;

    protected $statusCodeText = [
        // 1xx: Informational
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',

        // 2xx: Success
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',

        // 3xx: Redirection
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',

        // 4xx: Client Error
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',

        // 5xx: Server Error
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];

    protected $content;

    public function setStatusCode(int $code)
    {
        $this->statusCode = $code;
    }

    public function getStatusCodeText(int $code)
    {
        return isset($this->statusCodeText[$code]) ? $this->statusCodeText[$code] : 'unknown status code';
    }

    public function setHeader(String $header)
    {
        $this->headers[] = $header;
    }

    public function getHeader()
    {
        return $this->headers;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function redirect($url)
    {
        if (empty($url)) {
            trigger_error('Cannot redirect to an empty URL.');
            exit;
        }

        header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $url), true, 302);
        exit();
    }

    public function isInvalidCode($code)
    {
        return $code < 100 || $code > 599;
    }

    public function sendStatusCode($code)
    {
        if (!$this->isInvalidCode($code)) {
            $this->setHeader('HTTP/1.1 ' . $code . ' ' . $this->getStatusCodeText($code));
        }
    }

    public function send()
    {
        if (!headers_sent()) {
            foreach ($this->headers as $header) {
                header($header, true);
            }
        }

        http_response_code($this->statusCode);

        if (is_array($this->content)) {
            echo json_encode($this->content);
        } elseif (!empty($this->content)) {
            echo $this->content;
        }

        exit;
    }


    public function render()
    {
        if (!headers_sent()) {
            foreach ($this->headers as $header) {
                header($header, true);
            }
        }

        if (is_array($this->content)) {
            echo json_encode($this->content);
        } else {
            echo $this->content;
        }
    }
}
