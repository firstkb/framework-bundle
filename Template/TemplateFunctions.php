<?php

namespace Firstkb\FrameworkBundle\Template;

use Exception;

class TemplateFunctions
{
    /**
     * @var array an array of functions
     */
    protected $functions = [];

    /**
     * Magic method called when invoking inaccessible methods in an object context.
     *
     * @param string $name The name of the function called.
     * @param array $arguments The arguments passed to the function.
     * @return mixed The result of the function call.
     * @throws Exception If the function is not defined.
     */
    public function __call(string $name, array $arguments)
    {
        if (isset($this->functions[$name])) {
            $function = $this->functions[$name];
            return call_user_func_array($function, $arguments);
        }
        throw new Exception(sprintf('Function "%s" is not defined', $name));
    }

    /**
     * Registers a function.
     *
     * @param string $name The name of the function.
     * @param callable $function The function to register.
     */
    public function registerFunction(string $name, callable $function)
    {
        $this->functions[$name] = $function;
    }

}
