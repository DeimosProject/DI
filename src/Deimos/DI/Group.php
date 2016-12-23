<?php

namespace Deimos\DI;

class Group extends Container
{

    /**
     * @var string
     */
    protected $path;

    /**
     * Group constructor.
     *
     * @param $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @param string $name
     *
     * @return callable|mixed
     */
    public function get($name)
    {
        return $this->processGet($name);
    }

    /**
     * @param string $name
     * @param array $params
     *
     * @return callable|mixed
     */
    public function call($name, $params)
    {
        return $this->processCall($name, $params);
    }

    /**
     * @param $name
     * @param $params
     *
     * @return callable|mixed
     */
    public function __call($name, $params)
    {
        return $this->processCall($name, $params);
    }

    /**
     * @param $name
     *
     * @return string
     */
    protected function selfPath($name)
    {
        return $this->path . '.' . $name;
    }

}