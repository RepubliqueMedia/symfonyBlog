<?php

namespace App\Entity;

use Doctrine\Common\Inflector\Inflector;

/*
 * #Auto\Set    : has auto getter only
 * #Auto\Public      : has auto getter & setter
 * #Auto\Get    : has auto setter only
 *
 */

class AbstractEntity2
{
    public function __call(string $method, array $arguments)
    {
        $prefix = substr($method, 0, 3);
        $property = lcfirst(substr($method, 3));

        switch ($prefix) {
            case 'get':
                return $this->__get($property);
                break;

            case 'set':
                if (count($arguments) == 1) {
                    return $this->__set($property, $arguments[0]);
                }
                break;

            default:
                //que si on a 0 arguments !
                $property=lcfirst($method);
                if (count($arguments) == 0) {
                    return $this->__get($property);
                }
                break;

        }

    }

    public function __get($property)
    {

        //is property exist
        if (property_exists($this, $property)) {
            try {
                $reflection = new \ReflectionProperty($this, $property);
                if ($this->_isPublicable($reflection, 'get')) {
                    // can get protected field
                    if ($reflection->isProtected()) {
                        return $this->{$property};
                    }
                    // access to private field
                    $reflection->setAccessible(true);
                    return $reflection->getValue($this);
                }
            } catch (\ReflectionException $exception) {

            }
        }
    }

    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            try {
                $reflection = new \ReflectionProperty($this, $property);

                if ($this->_isPublicable($reflection, 'set')) {

                    if ($reflection->isProtected()) {
                        $this->$property = $value;
                        // entity setter must return this.
                        return $this;
                    }
                    throw new \BadMethodCallException(
                        'property ' . $property . ' must be defined as protected in ' . get_class($this) . ' !'
                    );

                }

            } catch (\ReflectionException $exception) {

            }
        }
    }

    /*
     * Check if one property have autogetter/setter annotation
     */
    private function _isPublicable(\ReflectionProperty $reflection, $mode): bool
    {
        if (!$annot = $reflection->getDocComment()) {
            //no annotation
            return false;
        }

        if (str_contains($annot, '* #Auto\Public')) {
            return true;
        }

        if ($mode == 'get' && str_contains($annot, '* #Auto\Get')) {
            return true;
        }

        if ($mode == 'set' && str_contains($annot, '* #Auto\Set')) {
            return true;
        }

        return false;
    }

}