<?php
/**
 * Created by PhpStorm.
 * User: asandoval
 * Date: 06/12/2017
 * Time: 9:03
 */

namespace Helium\Helpers;


use Helium\Metadata\JsonIgnore;
use Lithium\Exceptions\MissingParameterException;
use Lithium\Exceptions\NullPointerException;
use Lithium\Exceptions\UnsupportedTypeException;
use Lithium\Helpers\RegexpHelper;
use Lithium\Worker\DocMetadata;

final class ReflectionHelper {

    private static function isAssocArray(array $arr) {
        if (array() === $arr) return false;
        return \array_keys($arr) !== \range(0, \count($arr) - 1);
    }

    /**
     * @param array  $objectArray
     * @param string $className
     * @return null|object
     * @throws MissingParameterException
     * @throws NullPointerException
     * @throws UnsupportedTypeException
     * @throws \Lithium\Exceptions\ClassNotFoundException
     */
    public static function setObjectProperties(array $objectArray, string $className) {
        /** @var \ReflectionClass $reflectionClass */

        if (\is_null($objectArray) || !\is_array($objectArray) || \count($objectArray) <= 0)
            return null;
        $object = \Lithium\Helpers\ReflectionHelper::newClassNameInstance($className, $reflectionClass);
        foreach ($objectArray as $propertyName => $propertyValue) {
            $setterName = RegexpHelper::getCleanSetterName($propertyName);
            if (!$reflectionClass->hasMethod($setterName))
                continue;
            $reflectionSetter = $reflectionClass->getMethod($setterName);
            if (\is_null($reflectionSetter))
                throw new NullPointerException("Could not create reference for method $setterName in $className");
            if ($reflectionSetter->getNumberOfParameters() != 1)
                throw new MissingParameterException("Too many parameters for $setterName in class $className");
            $doc = $reflectionSetter->getDocComment();
            if (false != $doc) {
                $jsonIgnore = DocMetadata::get($doc, JsonIgnore::class);
                if (!\is_null($jsonIgnore))
                    continue;
            }
            $reflectionParameter = $reflectionSetter->getParameters()[0];
            if (\is_null($propertyValue)) {
                if ($reflectionParameter->isOptional()) {
                    $reflectionSetter->invoke($object);
                    continue;
                } else if ($reflectionParameter->allowsNull()) {
                    $reflectionSetter->invokeArgs($object, [null]);
                    continue;
                }
                throw  new NullPointerException("Could not call $setterName in $className with null parameter");
            }
            if ($reflectionParameter->hasType()) {
                $reflectionParameterClass = $reflectionParameter->getClass();
                if (\is_null($reflectionParameterClass)) { //im scalar
                    if (!\is_array($propertyValue))
                        $propertyValue = [$propertyValue];
                    if (!$reflectionParameter->isVariadic() && \count($propertyValue) > 1) {
                        $propertyValue = [$propertyValue[0]];
                    }
                    $reflectionSetter->invokeArgs($object, $propertyValue);
                    continue;
                }
                //im a custom class
                if (!\is_array($propertyValue))
                    throw new UnsupportedTypeException("Unsuported type " . gettype($propertyValue) .
                        "for method $setterName in class $className");

                if ($reflectionParameter->isVariadic()) {
                    if (self::isAssocArray($propertyValue))
                        throw new UnsupportedTypeException("For calling method $setterName in class $className" .
                            " you must define a numeric array, not associative");
                    $tmpPropertyValue = [];
                    foreach ($propertyValue as $subPropertyArray) {
                        $tmpPropertyValue[] =
                            self::setObjectProperties($subPropertyArray, $reflectionParameterClass->getName());
                    }
                    $propertyValue = $tmpPropertyValue;
                } else {
                    if (!self::isAssocArray($propertyValue))
                        throw new UnsupportedTypeException("For calling method $setterName in class $className" .
                            " you must define an associative array, not numeric");
                    $propertyValue = [self::setObjectProperties($propertyValue, $reflectionParameterClass->getName())];
                }

                $reflectionSetter->invokeArgs($object, $propertyValue);

            } else {
                $reflectionSetter->invokeArgs($object, [$propertyValue]);
            }
        }
        return $object;
    }

    /**
     * @param object $object
     * @param bool   $rootObject
     * @return array|null|string
     * @throws NullPointerException
     */
    public static function getObjectProperties($object, bool $rootObject = true) {
        if (\is_null($object))
            return $rootObject ? [] : null;
        if (\is_array($object))
            return $object;
        if (!\is_object($object)) {
            $objectValue = (string)$object;
            return $rootObject ? [$objectValue] : $objectValue;
        }

        $reflectionObject = new \ReflectionClass($object);
        if (\is_null($reflectionObject))
            throw new NullPointerException("Could not create class reference in node object");

        $publicMethods = $reflectionObject->getMethods(\ReflectionMethod::IS_PUBLIC);

        $getterMethods = \array_filter($publicMethods,
            function (\ReflectionMethod $method) use ($reflectionObject) {
                if (!\Helium\Helpers\RegexpHelper::isGetterMethod($method->getName()))
                    return false;
                if ($method->getNumberOfRequiredParameters() > 0)
                    throw new MissingParameterException("Too many parameters for method {$method->getName()} " .
                        "in class {$reflectionObject->getName()}");
                $doc = $method->getDocComment();
                if (false == $doc)
                    return true;
                $jsonIgnore = DocMetadata::get($doc, JsonIgnore::class);
                return \is_null($jsonIgnore);
            });

        $array = [];
        foreach ($getterMethods as $method) {
            $tmpArrayKey = \Helium\Helpers\RegexpHelper::getterToPropertyName($method->getName());
            $tmpArrayValue = $method->invoke($object);
            if (\is_object($tmpArrayValue)) {
                $tmpArrayValue = self::getObjectProperties($tmpArrayValue, false);
            } else if (\is_array($tmpArrayValue)) {
                if (self::isAssocArray($tmpArrayValue)) {
                    $tmpArrayValue = self::getObjectProperties($tmpArrayValue, false);
                } else {
                    $tmpTmpArrayValue = [];
                    foreach ($tmpArrayValue as $value) {
                        $tmpTmpArrayValue[] = self::getObjectProperties($value, false);
                    }
                    $tmpArrayValue = $tmpTmpArrayValue;
                }
            }
            $array[$tmpArrayKey] = $tmpArrayValue;
        }
        return $array;
    }
}