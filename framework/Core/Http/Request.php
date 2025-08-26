<?php

namespace Core\Http;

class Request
{
    public $cookie;

    public function __construct()
    {
        $this->cookie = $_COOKIE;
    }


    public function get($key = '', $default = null)
    {
        if ($key == '') {
            return $this->clean($_GET);
        }

        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }


    public function post($key = '', $default = null)
    {
        if ($key == '') {
            return $this->clean($_POST);
        }

        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }


    public function input(String $key = '')
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata, true);

        if ($key != '') {
            return isset($request[$key]) ? $this->clean($request[$key]) : null;
        }

        return ($request);
    }


    public function server(string $key = '')
    {
        $data = $key ? ($_SERVER[strtoupper($key)] ?? null) : $_SERVER;
        return $this->clean($data);
    }


    public function getMethod()
    {
        return strtoupper($this->server('REQUEST_METHOD'));
    }


    public function getUrl()
    {
        return $this->server('REQUEST_URI');
    }


    public function clean(array|string $data): array|string
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->clean($value);
            }
        } else {
            $data = htmlspecialchars($data, ENT_QUOTES, "UTF-8");
        }

        return $data;
    }
}
