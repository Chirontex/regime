<?php
/**
 * @package Regime
 */
namespace Regime\Traits;

/**
 * Add random generating methods.
 * @since 0.6.3
 */
trait Randomizer
{

    /**
     * Generage a random string of digits and letters.
     * @since 0.6.3
     * 
     * @param int $length
     * 
     * @return string
     */
    protected function generateRandomString(int $length = 32) : string
    {

        if ($length < 1) $length = 32;

        $result = '';

        $arr = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 1; $i <= $length; $i++) {

            $result .= $arr[rand(0, (count($arr) - 1))];

        }

        return $result;

    }

}
