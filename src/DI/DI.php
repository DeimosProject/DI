<?php

namespace Deimos\DI;

abstract class DI
{

    /**
     * @var mixed[]
     */
    protected $storage = [];

    /**
     * @var self
     */
    protected $self;

    /**
     * @var self[]
     */
    protected static $instances = [];

    /**
     * Container constructor.
     *
     * @param bool $init
     */
    public function __construct($init = true)
    {
        $this->self = $this;

        if ($init)
        {
            $this->configure();
        }

        if (!isset(self::$instances[static::class]))
        {
            self::$instances[static::class] = $this;
        }
    }

    /**
     * @return DI
     */
    protected static function requireInstance()
    {
        return self::$instances[static::class];
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function call($name, array $arguments = [])
    {
        $path = $this->path($name);
        $row  = $this->getFirst($path);
        $last = array_pop($path);

        if (!empty($path))
        {
            $this->steps($row, $path);
        }

        $isCallable = is_callable($row);

        if (!$isCallable && !$last)
        {
            return $row;
        }

        return call_user_func_array(
            $isCallable ? $row : [$row, $last],
            (new Argument($this, $arguments))->get()
        );
    }

    /**
     * @param string   $name
     * @param callable $callback
     */
    protected function group($name, callable $callback)
    {
        list ($self, $this->self) = [$this->self, new Group()];
        $self->storage[$name] = $this->self;
        $callback();
        $this->self = $self;
    }

    /**
     * @param mixed $row
     * @param array $keys
     */
    protected function steps(&$row, array $keys)
    {
        foreach ($keys as $name)
        {
            $row = $row->{$name}();
        }
    }

    protected function build($name, callable $callback)
    {
        $this->value($name, $callback);
    }

    protected function path($name)
    {
        return explode('.', $name);
    }

    protected function getFirst(&$path)
    {
        $name   = array_shift($path);
        $object = $this->self->storage[$name];

        return $this->getInstance($object);
    }

    public function get($name)
    {
        $path = $this->path($name);
        $row  = $this->getFirst($path);
        $last = array_pop($path);

        if (!empty($path))
        {
            $this->steps($row, $path);
        }

        if ($last && is_object($row))
        {
            if ($row instanceof self)
            {
                return $this->getInstance($row->self->storage[$last]);
            }

            $row = $row->{$last}();
        }

        return $this->getInstance($row);
    }

    protected function getInstance($row)
    {
        if ($row instanceof Instance)
        {
            return $row->get();
        }

        return $row;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, array $arguments = [])
    {
        return $this->call($name, $arguments);
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return static::requireInstance()->call($name, $arguments);
    }

    /**
     * @param       $name
     * @param       $row
     * @param array $arguments
     */
    protected function value($name, $row, array $arguments = [])
    {
        $argument  = new Argument($this, $arguments);
        $arguments = $argument->get();
        unset($argument);

        $this->self->storage[$name] =
            is_callable($row) ?
                call_user_func_array($row, $arguments) : $row;
    }

    /**
     * @param $name
     * @param $callback
     */
    protected function callback($name, $callback)
    {
        $this->self->storage[$name] = $callback;
    }

    protected function instance($name, $class, array $arguments = [])
    {
        $this->self->storage[$name] = new Instance(
            $this,
            $class,
            new Argument($this, $arguments)
        );
    }

    /**
     * configure
     */
    abstract protected function configure();

}
