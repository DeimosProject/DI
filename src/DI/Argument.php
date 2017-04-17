<?php

namespace Deimos\DI;

class Argument
{

    /**
     * @var DI
     */
    protected $di;

    /**
     * @var array
     */
    protected $rows;

    /**
     * Argument constructor.
     *
     * @param DI    $container
     * @param array $rows
     */
    public function __construct(DI $container, array $rows)
    {
        $this->di   = $container;
        $this->rows = $rows;
    }

    /**
     * @return array
     */
    public function get()
    {
        $arguments = [];

        foreach ($this->rows as $key => $argument)
        {
            $arguments[$key] = $argument;

            if (!empty($argument) && is_string($argument) && $argument{0} === '@')
            {
                $argument = substr($argument, 1);

                $arguments[$key] = $this->di->call($argument);
            }
        }

        return $arguments;
    }

}
