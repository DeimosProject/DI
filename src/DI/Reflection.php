<?php

namespace Deimos\DI;

class Reflection
{

    /**
     * @param $class
     * @param $arguments
     *
     * @return object
     */
    public static function classInit($class, $arguments)
    {
        $reflection = new \ReflectionClass($class);

        return $reflection->newInstanceArgs($arguments);
    }

}