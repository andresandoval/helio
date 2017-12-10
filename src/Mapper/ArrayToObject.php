<?php
/**
 * Created by PhpStorm.
 * User: asandoval
 * Date: 06/12/2017
 * Time: 8:57
 */

namespace Helium\Mapper;


use Helium\Helpers\ReflectionHelper;

class ArrayToObject implements Mapper {


    /**
     * @param array  $array
     * @param string $className
     * @return null|object
     * @throws \Lithium\Exceptions\ClassNotFoundException
     * @throws \Lithium\Exceptions\MissingParameterException
     * @throws \Lithium\Exceptions\NullPointerException
     * @throws \Lithium\Exceptions\UnsupportedTypeException
     */
    public static function map($array, string $className = null) {
        if (!self::isValidInput($array))
            return null;
        return ReflectionHelper::setObjectProperties($array, $className);
    }

    /**
     * @param array $input
     * @return bool
     */
    static function isValidInput($input): bool {
        return !(\is_null($input) || !\is_array($input) || \count($input) <= 0);
    }

}