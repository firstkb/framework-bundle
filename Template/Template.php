<?php

namespace Firstkb\FrameworkBundle\Template;

use Exception;

class Template
{
    /**
     * @var string root path to the application
     */
    protected $rootPath;

    /**
     * @var TemplateFunctions
     */
    protected $templateFunctions;

    /**
     * Template constructor.
     *
     * @param string $rootPath The root path of the application
     */
    public function __construct(string $rootPath)
    {
        $this->rootPath = $rootPath;
        $this->templateFunctions = new TemplateFunctions();
    }

    /**
     * Registers a new function that can be called from the templates.
     *
     * @param string   $name     The name of the function
     * @param callable $function The function to register
     */
    public function registerFunction(string $name, callable $function)
    {
        $this->templateFunctions->registerFunction($name, $function);
    }

    /**
     * Renders a template file with the given data.
     *
     * @param string $template The name of the template file to render
     * @param array  $data     The data to pass to the template
     *
     * @return string The rendered content of the template file
     *
     * @throws Exception If the template file is not found
     */
    public function render(string $template, array $data = []) : string
    {
        $templatePath = $this->rootPath . '/templates/' . $template . '.php';
        if (!file_exists($templatePath)) {
            throw new Exception('Template file /templates/' . $template . '.php not found');
        }

        ob_start();
        extract($data);
        $core = $this->templateFunctions;
        require $templatePath;
        return ob_get_clean();
    }
}
