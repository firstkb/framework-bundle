<?php

namespace Firstkb\FrameworkBundle\Core;

class Config
{
    protected $data;
    protected $rootPath;

    /**
     * Constructor.
     *
     * @param string $rootPath The root path of the application.
     */
    public function __construct(string $rootPath) {
        $this->rootPath = $rootPath;
        // Load the configuration data from the file into an array.
        $this->data = $this->loadConfig();
        $this->handlingStandardVariables();
    }

    /**
     * This function is a helper function that is called in the constructor of the Config class after loading the configuration data.
     */
    protected function handlingStandardVariables()
    {
        if (isset($this->data['timezone_set'])) {
            date_default_timezone_set($this->data['timezone_set']);
        }
    }

    /**
     * Get the value of a configuration parameter by key path.
     *
     * @param string $key The dot-separated key path of the parameter to retrieve.
     * @param null|string|bool $default The default value to return if the parameter is not found.
     *
     * @return null|string|bool The value of the configuration parameter if found, or the default value if not found.
     */
    public function get(string $key, $default = null) {
        // Split the key path into an array of individual keys.
        $keys = explode('.', $key);
        $value = $this->data;

        // Traverse the nested arrays to get the value of the parameter.
        foreach ($keys as $key) {
            if (!isset($value[$key])) {
                return $default;
            }
            $value = $value[$key];
        }

        return $value;
    }

    /**
     * Load the configuration data from the file.
     *
     * @return array The configuration data as an associative array.
     */
    protected function loadConfig() : array
    {
        // Get the path to the configuration files.
        $configFile = $this->rootPath . '/config/config.php';
        $configLocalFile = $this->rootPath . '/config/config.local.php';

        // Check if the local configuration file exists and load it if it does.
        if (file_exists($configLocalFile)) {
            return require $configLocalFile;
        }

        // Otherwise, load the main configuration file.
        return require $configFile;
    }
}
