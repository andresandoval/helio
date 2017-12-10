<?php
/**
 * Created by PhpStorm.
 * User: asandoval
 * Date: 06/12/2017
 * Time: 9:01
 */

namespace Helium\Mapper;


interface Mapper {

    static function map($inputObject, string $className = null);

    static function isValidInput($input) :bool;

}