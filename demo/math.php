<?php

include_once __DIR__ . '/../vendor/autoload.php';

class GCDMath
{

    /**
     * @param int $a
     * @param int $b
     *
     * @return int
     */
    public function call($a, $b)
    {
        if (!$a)
        {
            return $b;
        }
        if (!$b)
        {
            return $a;
        }

        return $this->call($a, $a % $b);
    }

}

class LCMMath
{
    /**
     * @var GCDMath
     */
    protected $gcdMath;

    public function __construct(GCDMath $gcd)
    {
        $this->gcdMath = $gcd;
    }

    /**
     * @param int $a
     * @param int $b
     *
     * @return int
     */
    public function call($a, $b)
    {
        return $a * ($b / $this->gcdMath->call($a, $b));
    }
}

class Obj extends Deimos\DI\DI
{
    protected function configure()
    {
        $this->value('a', 13);
        $this->value('b', 16);

        $this->group('math', function ()
        {
            $this->instance('gcdClass', GCDMath::class, []);
            $this->instance('lcmClass', LCMMath::class, ['@math.gcdClass']);
        });

        $this->value('gcd', function ()
        {
            return $this->call('math.gcdClass.call', ['@a', $this->b()]);
        });

        $this->value('lcm', function ()
        {
            return $this->call('math.lcmClass.call', [$this->a(), '@b']);
        });

        $this->callback('substr', 'mb_substr');
        $this->callback('length', function ($object)
        {
            if (is_array($object))
            {
                return count($object);
            }

            return mb_strlen($object);
        });
    }
}

$obj = new Obj();

var_dump($obj->get('math.lcmClass')->call($obj->a(), $obj->b()));
var_dump($obj->get('math.gcdClass')->call($obj->a(), $obj->b()));

var_dump($obj->lcm());
var_dump($obj->gcd());
