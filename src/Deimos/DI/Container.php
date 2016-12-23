<?php

namespace Deimos\DI;

abstract class Container
{

    /**
     * @var mixed[]
     */
    protected $values = [];

    /**
     * @var callable[]
     */
    protected $callbacks = [];

    /**
     * @var callable[]
     */
    protected $buildCallbacks = [];

    /**
     * @param string $key
     * @param mixed  $value
     */
    protected function addValue($key, $value)
    {
        $this->values[$key] = $value;
    }

    /**
     * @param string   $key
     * @param callable $callback
     */
    protected function addCallback($key, callable $callback)
    {
        $this->callbacks[$key] = $callback;
    }

    /**
     * @param string   $key
     * @param callable $callback
     */
    protected function addBuildCallback($key, callable $callback)
    {
        $this->buildCallbacks[$key] = $callback;
    }

    /**
     * @param $name
     *
     * @return callable|mixed
     *
     * @throws \InvalidArgumentException
     */
    protected function processGet($name)
    {
        return $this->getBySplitPath($this->splitPath($name));
    }

    /**
     * @param $name
     * @param $params
     *
     * @return callable|mixed
     *
     * @throws \InvalidArgumentException
     */
    protected function processCall($name, $params)
    {
        return $this->getBySplitPath($this->splitPath($name), true, $params);
    }

    /**
     * @param $name
     *
     * @return array
     */
    protected function splitPath($name)
    {
        return explode('.', $name);
    }

    /**
     * @param array $path
     * @param bool  $isCall
     * @param array $params
     *
     * @return callable|mixed
     *
     * @throws \InvalidArgumentException
     */
    protected function getBySplitPath(array $path, $isCall = false, array $params = [])
    {
        $value = array_shift($path);

        if (empty($path))
        {
            return $this->getValue($value, $isCall, $params);
        }

        $value = $this->getValue($value);

        if ($value instanceof self)
        {
            return $value->getBySplitPath($path, $isCall, $params);
        }

        $last = array_pop($path);
        $this->steps($value, $path);

        if ($isCall)
        {
            return call_user_func_array([$value, $last], $params);
        }

        return $value->$last();
    }

    /**
     * @param       $value
     * @param array $path
     */
    protected function steps(&$value, array $path)
    {
        foreach ($path as $step)
        {
            $value = $value->$step();
        }
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    protected function buildCallback($name)
    {
        $this->values[$name] = call_user_func($this->buildCallbacks[$name]);
        unset($this->buildCallbacks[$name]);

        return $this->values[$name];
    }

    /**
     * @param string $name
     * @param bool   $isCallable
     * @param null   $callParams
     *
     * @return callable|mixed
     *
     * @throws \InvalidArgumentException
     */
    protected function getValue($name, $isCallable = false, $callParams = null)
    {
        if (!array_key_exists($name, $this->values))
        {
            if (isset($this->buildCallbacks[$name]))
            {
                return $this->buildCallback($name);
            }

            if (isset($this->callbacks[$name]))
            {
                if ($isCallable)
                {
                    return call_user_func_array($this->callbacks[$name], $callParams);
                }

                return $this->callbacks[$name];
            }

            $selfPath = $this->selfPath($name);

            throw new \InvalidArgumentException("'$selfPath' is not defined");
        }

        return $this->values[$name];
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function selfPath($name)
    {
        return $name;
    }

}