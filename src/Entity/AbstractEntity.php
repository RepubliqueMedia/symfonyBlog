<?php

namespace App\Entity;

use Doctrine\Common\Inflector\Inflector;

/*
 * #Auto\Set    : has auto getter only
 * #Auto\Public      : has auto getter & setter
 * #Auto\Get    : has auto setter only
 *
 */

class AbstractEntity
{
/*
    public function __call(string $method, array $arguments)
    {


        //exit('ok');
        //dd($method,$arguments);
        //set or get
        $prefix = substr($method, 0, 3);
        $name = lcfirst(substr($method, 3));

        switch ($prefix) {

            case 'set':
                if (property_exists($this, $name)) {
                    try {
                        $reflection = new \ReflectionProperty($this, $name);

                        if ($this->isPublicable($reflection, 'set')) {

                            if (!$reflection->isProtected()) {
                                throw new \BadMethodCallException(
                                    'property ' . $name . ' must be defined as protected in ' . get_class($this) . ' !'
                                );
                            }

                            $this->$name = $value;
                            // entity setter must return this ... or clone ?
                            return $this;
                        }

                    } catch (\ReflectionException $exception) {
                        dd($exception);
                    }
                }
                throw new \BadMethodCallException(
                    'Undefined method ' . $method . ' in ' . get_class($this) .
                    ' and can\'t find an auto property ' . $name
                );

                return;
                break;

            case 'get':
                if (property_exists($this, $name)) {
                    try {
                        $reflection = new \ReflectionProperty($this, $name);
                        if ($this->isPublicable($reflection, 'get')) {
                            // can get protected field
                            if ($reflection->isProtected()) {
                                return $this->{$name};
                            }
                            // access to private field
                            $reflection->setAccessible(true);
                            return $reflection->getValue($this);
                        }
                    } catch (\ReflectionException $exception) {

                    }
                }
                throw new \BadMethodCallException(
                    'Undefined method ' . $method . ' in ' . get_class($this) .
                    ' and can\'t find an auto property ' . $name
                );
                return;
                break;
        }

        //property call

        throw new \BadMethodCallException(
            'Undefined method '.$method.' in '.get_class($this)
        );

    }
*/
    public function __get($name)
    {
        //exit('ok');
        //dd($name);
        //camelcase method name
        $method = lcfirst(Inflector::classify('get_' . $name));

        if (method_exists($this, $method)) {
            try {
                $reflection = new \ReflectionMethod($this, $method);
                if ($reflection->isPublic()) {
                    // we've got a public method for this
                    return $this->{$method}();
                }
            } catch (\ReflectionException $exception) {

            }
        }

        if(property_exists($this, $name)){
            try{
                $reflection = new \ReflectionProperty($this, $name);
                if($this->isPublicable($reflection,'get')){
                    // can get protected field
                    if($reflection->isProtected()){
                        return $this->{$name};
                    }
                    // access to private field
                    $reflection->setAccessible(true);
                    return $reflection->getValue($this);
                }
            }catch(\ReflectionException $exception){

            }
        }

        throw new \BadMethodCallException(
            'Undefined property ' . $name . ' in ' . get_class($this)
        );
    }

    public function __set($name, $value)
    {
        dd($name);
        // camelcase method name
        $method = lcfirst(Inflector::classify('set_' . $name));

        // setTruc() exist
        if (method_exists($this, $method)) {
            try {
                $reflection = new \ReflectionMethod($this, $method);
                if ($reflection->isPublic()) {
                    // we've got a public method for this
                    return $this->{$method}($value);
                }
            } catch (\ReflectionException $exception) {

            }

        }

        /*
         * Auto\Set mode
         */
        if (property_exists($this, $name)) {
            if (property_exists($this, $name)) {
                try {
                    $reflection = new \ReflectionProperty($this, $name);

                    if ($this->isPublicable($reflection, 'set')) {

                        if (!$reflection->isProtected()) {
                            throw new \BadMethodCallException(
                                'property ' . $name . ' must be defined as protected in ' . get_class($this) . ' !'
                            );
                        }

                        $this->$name = $value;
                        // entity setter must return this ... or clone ?
                        return $this;
                    }

                } catch (\ReflectionException $exception) {
                    dd($exception);
                }
            }

        }

        throw new \BadMethodCallException(
            'Undefined property ' . $name . ' in ' . get_class($this)
        );
    }

    /*
     * Check if one property have autogetter/setter annotation
     */
    private function isPublicable(\ReflectionProperty $reflection, $mode): bool
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