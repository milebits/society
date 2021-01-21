<?php


namespace Milebits\Society\Helpers;

if (!function_exists('constExists')) {
    /**
     * @param $class
     * @param string $constant
     * @return bool
     */
    function constExists($class, string $constant)
    {
        if (is_object($class)) $class = get_class($class);
        return defined(sprintf("%s::%s", $class, $constant));
    }
}

if (!function_exists('propVal')) {
    /**
     * @param $class
     * @param string $name
     * @param null $default
     * @return mixed|null
     */
    function propVal($class, string $name, $default = null)
    {
        if (is_object($class)) $class = get_class($class);
        if (!property_exists($class, $name))
            return $default;
        return $class::$$name ?? $default;
    }
}

if (!function_exists('constVal')) {
    /**
     * @param $class
     * @param string $constant
     * @param null $default
     * @return mixed|null
     */
    function constVal($class, string $constant, $default = null)
    {
        if (!propVal($class, $constant)) return $default;
        return constant(sprintf("%s::%s", $class, $constant)) ?? $default;
    }
}
