<?php

namespace Firstkb\FrameworkBundle\Http;

class RequestBag
{
    /**
     * The input data for the request bag.
     *
     * @var array
     */
    protected $input;

    /**
     * Create a new request bag instance.
     *
     * @param array $input The input data for the request bag.
     */
    public function __construct(array $input = [])
    {
        $this->input = $input;
    }

    /**
     * Get a value from the input data by key.
     *
     * @param string|array $key The key(s) to retrieve from the input data.
     * @param bool $filter Whether or not to filter the input value.
     * @param int $length The maximum length of the filtered input value.
     *
     * @return string|array|bool|int The value(s) from the input data or an empty string if not found.
     */
    public function get($key, bool $filter = false, int $length = 250)
    {
        if (is_array($key)) {
            $return = [];
            foreach ($key as $item) {
                $return[$item] = isset($this->input[$item]) ? ($filter ? $this->filter($this->input[$item], $length) : $this->input[$item]) : '';
            }
            return $return;
        }
        return isset($this->input[$key]) ? ($filter ? $this->filter($this->input[$key], $length) : $this->input[$key]) : '';
    }

    /**
     * Get all values from the input data.
     *
     * @return array All values from the input data.
     */
    public function all() :array
    {
        return $this->input;
    }

    /**
     * Filter a string value.
     *
     * @param string $string The string to filter.
     * @param int $length The maximum length of the filtered string.
     *
     * @return string The filtered string.
     */
    protected function filter(string $string, int $length = 250) : string
    {
        $filtered = filter_var($string, FILTER_SANITIZE_FULL_SPECIAL_CHARS); // php < 8.0 FILTER_SANITIZE_STRING
        $filtered = str_replace(['&lt;', '&gt;'], ['<', '>'], $filtered);
        $filtered = str_replace(['&quot;', '&#039;'], ['"', "'"], $filtered);
        $filtered = strip_tags($filtered);
        $filtered = htmlspecialchars($filtered, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (mb_strlen($filtered) > $length) {
            $filtered = substr($filtered, 0, $length);
        }
        return $filtered;
    }
}
