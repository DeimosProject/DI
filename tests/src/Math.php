<?php

namespace DeimosTest;

class Math
{

    /**
     * @param int $min
     * @param int $max
     *
     * @return int
     */
    public function random($min = 0, $max = null)
    {

        if (null === $max)
        {
            $max = mt_getrandmax();
        }

        return mt_rand($min, $max);

    }

    /**
     * @param float $min
     * @param float $max
     *
     * @return float
     */
    public function randomFloat($min = 0.0, $max = 1.0)
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }

    public function pow($base, $exp)
    {
        return pow($base, $exp);
    }

}