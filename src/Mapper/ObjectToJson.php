<?php
/**
 * Created by PhpStorm.
 * User: asandoval
 * Date: 06/12/2017
 * Time: 17:28
 */

namespace Helium\Mapper;


class ObjectToJson implements Mapper {

    /**
     * @param object      $inputObject
     * @param string|null $className
     * @return string
     * @throws \Lithium\Exceptions\NullPointerException
     */
    static function map($inputObject, string $className = null) {
        $array = ObjectToArray::map($inputObject);
        return \json_encode($array);
    }

    /**
     * @param $input
     * @return bool
     */
    static function isValidInput($input): bool {
        return true;
    }

}