<?php

namespace Firstkb\FrameworkBundle\Core;

use Firstkb\FrameworkBundle\Http\Response;

class Error
{
    // A flag indicating whether to display detailed error information or not
    protected $debugMode = false;

    // Constructor that sets the error and exception handlers
    public function __construct()
    {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    // Sets the debug mode based on the environment
    public function setDebugMode($environment)
    {
        $this->debugMode = ($environment == 'dev') ? true : false;
    }

    // Handles errors
    public function handleError($code, $message, $file, $line)
    {
        // Log the error
        //$this->logError($code, $message, $file, $line);

        // Display error information if debug mode is on, else display a generic error message
        if ($this->debugMode) {
            $this->displayError($code, $message, $file, $line);
        } else {
            $this->displayGenericError();
        }

        // Stop script execution
        exit;
    }

    // Handles exceptions
    public function handleException($exception)
    {
        // Log the exception
        //$this->logException($exception);

        // Display error information if debug mode is on, else display a generic error message
        if ($this->debugMode) {
            $this->displayException($exception);
        } else {
            $this->displayGenericError();
        }

        // Stop script execution
        exit;
    }

    // Handles fatal errors
    public function handleShutdown()
    {
        $error = error_get_last();
        if ($error && ($error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR))) {
            // Log the error
            //$this->logError($error['type'], $error['message'], $error['file'], $error['line']);

            // Display error information if debug mode is on, else display a generic error message
            if ($this->debugMode) {
                $this->displayError($error['type'], $error['message'], $error['file'], $error['line']);
            } else {
                $this->displayGenericError();
            }
        }
    }

    // Logs an error
    /*protected function logError($code, $message, $file, $line)
    {
        // Implement error logging functionality here.
    }*/

    // Logs an exception
    /*protected function logException($exception)
    {
        // Implement exception logging functionality here.
    }*/

    // Displays error information
    protected function displayError($code, $message, $file, $line)
    {
        // Display error information in an HTML page
        return $this->displayHtmlErrorPage($message, $file, $line, $code);
    }

    // Displays exception information
    protected function displayException($exception)
    {
        // Display exception information in an HTML page
        return $this->displayHtmlErrorPage($exception->getMessage(), $exception->getFile(), $exception->getLine(), null, $exception->getTraceAsString());
    }

    // Displays a generic error message
    protected function displayGenericError()
    {
        // Display a generic error message in an HTML page
        return $this->displayHtmlErrorPage();
    }

    // Displays an error message in an HTML page
    protected function displayHtmlErrorPage($message = null, $file = null, $line = null, $code = null, $traceAsString = null)
    {
        $block = '<h1>Oops, something went wrong</h1><p>We\'re sorry, but an unexpected error occurred. Please try again later.</p>';
        if ($this->debugMode) {
            $block = "<p><strong>Message:</strong> {$message}</p><p><strong>File:</strong> {$file}</p><p><strong>Line:</strong> {$line}</p><pre>{$traceAsString}</pre>";
            //<p><strong>Code:</strong> {$code}</p>
        }

        $html = "<html lang='en'><head><title>Exception</title><link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css\"><style>body {background-color: #f5f5f5;}.exception {margin: 20px auto;max-width: 90%;padding: 20px;}.exception h1 {color: #c0392b;font-size: 2.5rem;margin-top: 0;margin-bottom: 0.5rem;}.exception p:first-child {margin: 10px 0;font-size: 1.2rem;font-weight: 400;line-height: 1.5;}.exception pre {background-color: #eee;border: 1px solid #ddd;border-radius: 4px;font-size: 12px;padding: 10px;white-space: pre-wrap;}</style></head><body><div class=\"exception\">{$block}</div></body></html>";

        return new Response($html, 500);
    }
}
