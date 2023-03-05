<?php

namespace Firstkb\FrameworkBundle\Http;

use Firstkb\FrameworkBundle\Core\Config;

class Request
{
    /**
     * $_GET
     * @var RequestBag
     */
    public $query;

    /**
     * $_POST
     * @var RequestBag
     */
    public $request;

    /**
     * $_COOKIE
     * @var RequestBag
     */
    public $cookies;

    /**
     * $_FILE
     * @var RequestBag
     */
    public $files;

    /**
     * $_SERVER
     * @var RequestBag
     */
    public $server;

    /**
     * The configuration for the application
     *
     * @var Config
     */
    protected $config;

    /**
     * The locale for the request
     *
     * @var string
     */
    protected $locale;

    /**
     * The default locale for the application
     *
     * @var string
     */
    protected $defaultLocale = 'en';

    /**
     * An array of available locales for the application
     *
     * @var array
     */
    protected $localeArray = ['en' => ''];

    /**
     * Constructor.
     *
     * @param array $query $_GET
     * @param array $request $_POST
     * @param array $cookies $_COOKIE
     * @param array $files $_FILES
     * @param array $server $_SERVER
     * @param Config $config The configuration for the application
     */
    public function __construct(Config $config, array $query = [], array $request = [], array $cookies = [], array $files = [], array $server = [])
    {
        $this->config = $config;
        $this->init($query, $request, $cookies, $files, $server);
    }

    /**
     * Initialize the request object.
     *
     * @param array $query $_GET
     * @param array $request $_POST
     * @param array $cookies $_COOKIE
     * @param array $files $_FILES
     * @param array $server $_SERVER
     */
    protected function init(array $query = [], array $request = [], array $cookies = [], array $files = [], array $server = [])
    {
        $this->query = new RequestBag($query);
        $this->request = new RequestBag($request);
        $this->cookies = new RequestBag($cookies);
        $this->files = new RequestBag($files);
        $this->server = new RequestBag($server);
        $this->setDefaultLocale();
    }

    /**
     * Set the default locale for the application.
     */
    protected function setDefaultLocale()
    {
        $defaultLocale = $this->config->get('default_locale');
        $this->locale = $this->defaultLocale = ($defaultLocale != '') ? $defaultLocale : $this->defaultLocale;
        $localeArray = $this->config->get('locale');
        $this->localeArray = is_array($localeArray) ? $localeArray : [$this->locale => ''];
    }

    /**
     * Get the current locale for the request.
     *
     * @return string The current locale.
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Get the available locales for the application.
     *
     * @return array The available locales.
     */
    public function getLocaleArray(): array
    {
        return $this->localeArray;
    }

    /**
     * Set the locale for the request.
     *
     * @param string $locale
     */
    public function setLocale(string $locale)
    {
        $this->locale = ($locale == '') ? $this->defaultLocale : $locale;
    }
}