<?php

namespace Firstkb\FrameworkBundle\Controller;

use Firstkb\FrameworkBundle\Http\Request;
use Firstkb\FrameworkBundle\Http\Response;
use Firstkb\FrameworkBundle\Routing\Router;
use Firstkb\FrameworkBundle\Template\Template;
use Exception;

class AbstractController
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Template
     */
    protected $template;

    /**
     * @var Router
     */
    protected $router;

    public function __construct(Request $request, Router $router, Template $template)
    {
        $this->request = $request;
        $this->router = $router;
        $this->template = $template;
    }

    protected function redirect(string $url, int $status = 302)
    {
        if ($status < 300 || $status > 308) {
            throw new Exception('status not for redirect', 500);
        }
        $response = new Response('', $status);
        $response->setRedirect($url);
        return $response->send();
    }

    protected function getRouteUrl(string $name, array $params = [])
    {
        return $this->router->getRoute($name, $params);
    }

    protected function json(array $data, int $status = 200, array $headers = [])
    {
        $jsonResponse = json_encode($data);

        $defaultHeaders = [
            'Content-Type' => 'application/json; charset=UTF-8',
            'Content-Length' => mb_strlen($jsonResponse)
        ];

        $headers = array_merge($headers, $defaultHeaders);

        $response = new Response($jsonResponse, $status, $headers);
        return $response->send();

    }

    /**
     * Returns a rendered view.
     */
    protected function renderView(string $view, array $parameters = [])
    {

        return $this->template->render($view, $parameters);
    }

    /**
     * Renders a view.
     */
    protected function render(string $view, array $parameters = [])
    {
        $this->addFunctionToTemplate();
        $content = $this->renderView($view, $parameters);

        $response = new Response($content);
        return $response->send();
    }

    protected function addFunctionToTemplate()
    {
        $this->template->registerFunction('path', function ($name, $params = []) {
            return $this->router->getRoute($name, $params);
        });
        $this->template->registerFunction('request', function () {
            return $this->request;
        });
        $this->template->registerFunction('show_array', function ($array = []) {
            echo is_array($array) ? '<pre>' . print_r($array, true) . '</pre>' : '';
        });
    }

}