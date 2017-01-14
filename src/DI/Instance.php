<?php

namespace Deimos\DI;

class Instance
{

    /**
     * @var DI
     */
    protected $container;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var Argument
     */
    protected $arguments;

    /**
     * any object
     *
     * @var mixed
     */
    protected $object;

    /**
     * Instance constructor.
     *
     * @param DI       $container
     * @param string   $class
     * @param Argument $arguments
     */
    public function __construct(DI $container, $class, Argument $arguments)
    {
        $this->container = $container;
        $this->class     = $class;
        $this->arguments = $arguments;
    }

    /**
     * @return mixed
     */
    protected function instance()
    {
        $class = new \ReflectionClass($this->class);

        if ($class->getConstructor() === null)
        {
            return $class->newInstance();
        }

        return $class->newInstanceArgs($this->arguments->get());
    }

    /**
     * @return mixed
     */
    public function get()
    {
        if (!$this->object)
        {
            $this->object = $this->instance();
        }

        return $this->object;
    }

}