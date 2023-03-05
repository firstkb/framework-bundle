<?php

namespace Firstkb\FrameworkBundle\Http;

use Exception;

class Response
{
    /**
     * @var integer the HTTP status code
     */
    protected $statusCode;

    /**
     * @var string the HTTP status text
     */
    protected $statusText;

    /**
     * @var string the HTTP protocol
     */
    protected $protocol;

    /**
     * @var array cookies support
     */
    protected $cookies;

    /**
     * @var array the HTTP headers
     */
    protected $headers;

    /**
     * @var string the HTTP response content
     */
    protected $content;

    /**
     * Response constructor.
     *
     * Initializes the response object with the specified content, status code and headers, and sends the headers.
     *
     * @param string $content The content of the response.
     * @param int $status The HTTP status code of the response.
     * @param array $headers An array of headers to be included in the response.
     * @throws Exception
     */
    public function __construct(string $content = '', int $status = 200, array $headers = [])
    {
        // Initialize the response object
        $this->statusCode = $status;
        $this->content = $content;
        $this->cookies = [];
        $this->setStatus();
        $this->setHeader($headers);
    }

    /**
     * Sends the response headers and content.
     *
     * * @return Response The response object itself for method chaining.
     */
    public function send() : Response
    {
        // Send All header
        $this->sendHeaders();
        // Send the response content
        $this->sendContent();

        return $this;
    }

    /**
     * Sets the status text for the response based on the status code.
     * If the status code is a known code, the corresponding text is set as the status text.
     * If the status code is not known, an exception is thrown with a message indicating that the status code is not found.
     */
    protected function setStatus()
    {
        $this->protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';

        switch ($this->statusCode) {
            case 100:
                $statusText = 'Continue';
                break;
            case 101:
                $statusText = 'Switching Protocols';
                break;
            case 200:
                $statusText = 'OK';
                break;
            case 201:
                $statusText = 'Created';
                break;
            case 202:
                $statusText = 'Accepted';
                break;
            case 203:
                $statusText = 'Non-Authoritative Information';
                break;
            case 204:
                $statusText = 'No Content';
                break;
            case 205:
                $statusText = 'Reset Content';
                break;
            case 206:
                $statusText = 'Partial Content';
                break;
            case 300:
                $statusText = 'Multiple Choices';
                break;
            case 301:
                $statusText = 'Moved Permanently';
                break;
            case 302:
                $statusText = 'Found';
                break;
            case 303:
                $statusText = 'See Other';
                break;
            case 304:
                $statusText = 'Not Modified';
                break;
            case 305:
                $statusText = 'Use Proxy';
                break;
            case 307:
                $statusText = 'Temporary Redirect';
                break;
            case 400:
                $statusText = 'Bad Request';
                break;
            case 401:
                $statusText = 'Unauthorized';
                break;
            case 402:
                $statusText = 'Payment Required';
                break;
            case 403:
                $statusText = 'Forbidden';
                break;
            case 404:
                $statusText = 'Not Found';
                break;
            case 405:
                $statusText = 'Method Not Allowed';
                break;
            case 406:
                $statusText = 'Not Acceptable';
                break;
            case 407:
                $statusText = 'Proxy Authentication Required';
                break;
            case 408:
                $statusText = 'Request Timeout';
                break;
            case 409:
                $statusText = 'Conflict';
                break;
            case 410:
                $statusText = 'Gone';
                break;
            case 411:
                $statusText = 'Length Required';
                break;
            case 412:
                $statusText = 'Precondition Failed';
                break;
            case 413:
                $statusText = 'Request Entity Too Large';
                break;
            case 414:
                $statusText = 'Request-URI Too Long';
                break;
            case 415:
                $statusText = 'Unsupported Media Type';
                break;
            case 416:
                $statusText = 'Requested Range Not Satisfiable';
                break;
            case 417:
                $statusText = 'Expectation Failed';
                break;
            case 500:
                $statusText = 'Internal Server Error';
                break;
            case 501:
                $statusText = 'Not Implemented';
                break;
            case 502:
                $statusText = 'Bad Gateway';
                break;
            case 503:
                $statusText = 'Service Unavailable';
                break;
            case 504:
                $statusText = 'Gateway Timeout';
                break;
            case 505:
                $statusText = 'HTTP Version Not Supported';
                break;
            case 506:
                $statusText = 'Variant Also Negotiates';
                break;
            case 507:
                $statusText = 'Insufficient Storage';
                break;
            case 508:
                $statusText = 'Loop Detected';
                break;
            case 510:
                $statusText = 'Not Extended';
                break;
            case 511:
                $statusText = 'Network Authentication Required';
                break;
            default:
                throw new Exception('Status '.$this->statusCode.' not found.', 500);

        }

        $this->statusText = $statusText;

    }

    /**
     *
     * Set the headers for the response object.
     *
     * @param array $headers Optional headers to set for the response. If a header key already exists, its value will be overwritten.
     *
     * @throws Exception If any of the headers are invalid.
     */
    protected function setHeader(array $headers = [])
    {

        $headersDefault = [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $headers = array_merge($headersDefault, $headers);

        $validHeaders = [];

        foreach ($headers as $name => $value) {
            if (!is_string($name) || !is_string($value)) {
                throw new Exception('Headers must be key-value pairs of strings', 500);
            }

            if (preg_match('/[^\x09\x0a\x0d\x20-\x7e\x80-\xff]/', $name . $value)) {
                throw new Exception('Invalid characters in header name or value', 500);
            }

            $validHeaders[$name] = $value;
        }

        $this->headers = $validHeaders;

    }

    protected function sendHeaders()
    {
        if (headers_sent()) {
            return $this;
        }

        // headers
        foreach ($this->headers as $name => $value) {
            $replace = 0 === strcasecmp($name, 'Content-Type');
            header($name.': '.$value, $replace, $this->statusCode);
        }

        // cookies
        foreach ($this->cookies as $cookie) {
            $cookieString = sprintf(
                '%s=%s; expires=%s; path=%s; domain=%s; secure=%s; httponly=%s',
                urlencode($cookie['name']),
                urlencode($cookie['value']),
                $cookie['expires'] ? gmdate('D, d-M-Y H:i:s T', $cookie['expires']) : 0,
                $cookie['path'],
                $cookie['domain'],
                $cookie['secure'] ? 'TRUE' : 'FALSE',
                $cookie['httpOnly'] ? 'TRUE' : 'FALSE'
            );

            header('Set-Cookie: ' . $cookieString, false, $this->statusCode);
        }

        // status
        header(sprintf('%s %s %s', $this->protocol, $this->statusCode, $this->statusText), true, $this->statusCode);

        // send redirect if status code is 330 - 308
        if ($this->statusCode >= 300 AND $this->statusCode <= 308) {
            exit();
        }
    }

    /**
     * Send the response content to the client.
     *
     * @return Response The response object itself for method chaining.
     */
    public function sendContent() : Response
    {
        echo $this->content;

        return $this;
    }


    /**
     * Set a cookie to be sent in the response.
     *
     * @param string $name The name of the cookie.
     * @param string $value The value of the cookie.
     * @param int|null $expires The expiration time of the cookie as a Unix timestamp.
     * @param string $path The path on the server where the cookie will be available.
     * @param string|null $domain The domain that the cookie is available to.
     * @param bool $secure Indicates whether the cookie should only be sent over a secure HTTPS connection.
     * @param bool $httpOnly Indicates whether the cookie should be accessible only through the HTTP protocol.
     *
     * @return Response The response object itself for method chaining.
     */
    protected function setCookie(string $name, string $value, $expires = null, string $path = '/', $domain = null, bool $secure = false, bool $httpOnly = true) : Response
    {
        $cookie = [
            'name' => $name,
            'value' => $value,
            'expires' => $expires,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httpOnly' => $httpOnly
        ];

        $this->cookies[] = $cookie;

        return $this;
    }

    /**
     * Set a redirect header to be sent in the response.
     *
     * @param string $url The URL to redirect to.
     *
     * @return Response The response object itself for method chaining.
     */
    public function setRedirect(string $url) : Response
    {
        $this->setHeader(['Location' => $url]);

        return $this;
    }


}