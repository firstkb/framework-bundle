<?php
namespace Firstkb\FrameworkBundle\Core;

use Firstkb\FrameworkBundle\Http\Request;
use Firstkb\FrameworkBundle\Routing\Router;
use Firstkb\FrameworkBundle\Template\Template;
use Exception;
class Core
{
    /**
     * The root path of the application
     *
     * @var string
     */
    protected $rootPath;

    /**
     * The environment of the application
     *
     * @var string
     */
    protected $environment = 'dev';

    /**
     * The error handler for the application
     *
     * @var Error
     */
    protected $error;

    /**
     * The configuration for the application
     *
     * @var Config
     */
    protected $config;

    /**
     * The request object for the application
     *
     * @var Request
     */
    protected $request;

    /**
     * The template engine for the application
     *
     * @var Template
     */
    protected $template;

    /**
     * The router for the application
     *
     * @var Router
     */
    protected $router;

    /**
     * Constructs a new Core object and initializes all objects required for the application to work
     *
     * @throws Exception if the template file is not found
     *
     * @return mixed
     */
    public function __construct()
    {
        $this->rootPath = getenv('PWD');

        // Initialization of all objects required for the application to work
        $this->error = new Error();
        $this->config = new Config($this->rootPath);
        $this->setEnvironment();
        $this->request = new Request($this->config, $_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);
        $this->router = new Router($this->rootPath, $this->environment, $this->config, $this->request);
        $this->template = new Template($this->rootPath);

        return $this->router->runRoute($this->template);
    }

    /**
     * Sets the environment for the application based on the configuration file
     */
    public function setEnvironment()
    {
        $this->environment = ($this->config->get('environment') != 'prod') ? 'dev' : 'prod';
        $this->error->setDebugMode($this->environment);
    }
}
