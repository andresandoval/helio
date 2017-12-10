<?php
/**
 * Created by PhpStorm.
 * User: asandoval
 * Date: 06/12/2017
 * Time: 17:30
 */

namespace Helium\Mapper;


class JsonToObject implements Mapper {

    /**
     * @param             $inputObject
     * @param string|null $className
     * @return null|object
     * @throws \Lithium\Exceptions\ClassNotFoundException
     * @throws \Lithium\Exceptions\MissingParameterException
     * @throws \Lithium\Exceptions\NullPointerException
     * @throws \Lithium\Exceptions\UnsupportedTypeException
     */
    static function map($inputObject, string $className = null) {
        if (!self::isValidInput($inputObject))
            return null;
        $jsonArray = \json_decode($inputObject, true);
        if(\is_null($jsonArray))
            return null;
        return ArrayToObject::map($jsonArray, $className);

    }

    /**
     * @param $input
     * @return bool
     */
    static function isValidInput($input): bool {
        return \strlen(\trim($input)) > 0;
    }

}