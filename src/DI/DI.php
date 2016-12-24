<?php

namespace Deimos\DI;

abstract class DI extends Container
{

    /**
     * @var static
     */
    protected $self;

    /**
     * @var static[]
     */
    protected static $instances;

    /**
     * DI constructor.
     */
    public function __construct()
    {
        $this->self = $this;

        $this->configure();
        static::$instances[get_called_class()] = &$this->self;
    }

    /**
     * @param $key
     * @param $value
     */
    protected function value($key, $value)
    {
        $this->self->addValue($key, $value);
    }

    /**
     * @param $key
     * @param $callback
     */
    protected function callback($key, $callback)
    {
        $this->self->addCallback($key, $callback);
    }

    /**
     * @param $key
     * @param $callback
     */
    protected function build($key, $callback)
    {
        $this->self->addBuildCallback($key, $callback);
    }

    /**
     * @param $key
     * @param $class
     * @param $arguments
     */
    protected function instance($key, $class, $arguments)
    {
        $this->build($key, function () use ($class, $arguments)
        {
            return $this->create($class, $arguments);
        });
    }

    /**
     * @param $key
     * @param $callback
     */
    protected function group($key, $callback)
    {
        $path = $this->self->selfPath($key);

        $container = new Group($path);
        $previous  = $this->self;

        $this->self = $container;
        call_user_func($callback);
        $this->self = $previous;

        $this->addValue($key, $container);
    }

    /**
     * @param $class
     * @param $arguments
     *
     * @return object
     */
    protected function create($class, array $arguments = null)
    {
        foreach ($arguments as $key => $value)
        {
            if (is_string($value) && $value{0} === '@')
            {
                $arguments[$key] = $this->get(substr($value, 1));
            }
        }

        return Reflection::classInit($class, $arguments);
    }

    /**
     * @param $name
     * @param $params
     *
     * @return $this|callable|mixed
     *
     * @throws \InvalidArgumentException
     */
    public function __call($name, $params)
    {
        if ($name === 'get')
        {
            if (empty($params))
            {
                return static::sharedInstance();
            }

            return $this->processGet($params[0]);
        }

        if ($name === 'call')
        {
            return $this->processCall($params[0], $params[1]);
        }

        return $this->processCall($name, $params);
    }

    /**
     * @param $name
     * @param $params
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public static function __callStatic($name, $params)
    {
        return call_user_func([static::sharedInstance(), '__call'], $name, $params);
    }

    /**
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public static function sharedInstance()
    {
        $class = get_called_class();

        if (!isset(static::$instances[$class]))
        {
            throw new \InvalidArgumentException('This container has not been constructed yet');
        }

        return static::$instances[$class];
    }

    /**
     * configure DI
     */
    abstract protected function configure();

}