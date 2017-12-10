<?php
/**
 * Created by PhpStorm.
 * User: asandoval
 * Date: 06/12/2017
 * Time: 17:12
 */

namespace Helium\Helpers;


final class RegexpHelper {

    public static function isGetterMethod(string $methodName): bool{
        return \preg_match("/^get.+$/i", $methodName);
    }

    public static function getterToPropertyName(string $getterName): string {
        $getterName = \preg_replace("/^get/", "", $getterName);
        return \lcfirst($getterName);
    }

}