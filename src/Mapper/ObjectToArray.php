<?php
/**
 * Created by PhpStorm.
 * User: asandoval
 * Date: 06/12/2017
 * Time: 17:03
 */

namespace Helium\Mapper;


use Helium\Helpers\ReflectionHelper;

class ObjectToArray implements Mapper {

    /**
     * @param object      $inputObject
     * @param string|null $className
     * @return array|null|string
     * @throws \Lithium\Exceptions\NullPointerException
     */
    static function map($inputObject, string $className = null) {
        return ReflectionHelper::getObjectProperties($inputObject);
    }

    /**
     * @param $input
     * @return bool
     */
    static function isValidInput($input): bool {
        return true;
    }


}