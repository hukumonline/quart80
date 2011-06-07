<?php

/**
 * Pandamp_BeanContext
 */
/**
 * Pandamp_BeanContext_Exception
 */
/**
 * Static class with utility methods for working on classes implementing the
 * Pandamp_BeanContext-interface and overall introspection of classes/object-instances.
 *
 * @category   Pandamp
 * @package    Pandamp_BeanContext
 *
 */
class Pandamp_BeanContext_Inspector {

    /**
     * @const string
     */
    const GETTER = 'get';

    /**
     * @const string
     */
    const SETTER = 'set';

    /**
     * @array
     */
    private static $cache = array();

    /**
     * Returns the reflection class for the object out of the cache.
     *
     * @param Object
     *
     * @return ReflectionClass
     */
    private static function getReflectionClass($object)
    {
        $class = get_class($object);
        if (!isset(self::$cache[$class]['class'])) {
            self::$cache[$class]['class'] = new ReflectionClass($object);
        }

        return self::$cache[$class]['class'];
    }

    /**
     * Returns reflection properties for the class the object represents
     * out of the cache.
     *
     * @param Object
     *
     * @return Array|ReflectionProperty
     */
    private static function getReflectionProperties($object)
    {
        $class = get_class($object);

        if (!isset(self::$cache[$class]['properties'])) {
            $refClass = self::getReflectionClass($object);
            self::$cache[$class]['properties'] = $refClass->getProperties();
        }

        return self::$cache[$class]['properties'];
    }

    /**
     * Returns reflection methods for the class the object represents
     * out of the cache.
     *
     * @param Object
     *
     * @return Array|ReflectionMethod
     */
    private static function getReflectionMethods($object)
    {
        $class = get_class($object);

        if (!isset(self::$cache[$class]['methods'])) {
            $refClass = self::getReflectionClass($object);
            self::$cache[$class]['methods'] = $refClass->getMethods();
        }

        return self::$cache[$class]['methods'];
    }

    /**
     * Checks wether a property is transient. A property is transient,
     * if an underscore is part of the property-name. This method
     * will also check if a property with the same name is available
     * that starts with an underscore.
     *
     * @param string $propertyName
     * @param Object $object
     *
     * @return boolean true, if the property is transient, otherwise false
     */
    private static function isTransient($propertyName, $object)
    {
        if (strpos($propertyName, '_') !== false) {
            return true;
        }

        $refClass = self::getReflectionClass($object);

        try {
            $prop = $refClass->getProperty('_'.$propertyName);
            return true;
        } catch (ReflectionException $e) {

        }

        return false;
    }

    public static function getProperties($object)
    {
        $properties = self::getReflectionProperties($object);
        $methods    = self::getReflectionMethods($object);

        $accessorType = self::GETTER;

        $data = array();

        /**
         * @todo better regex for properties containing only letters
         */
        for ($i = 0, $len = count($properties); $i < $len; $i++) {
            $name = $properties[$i]->getName();
            if (strpos($name, '_') !== false) {
                continue;
            }

            $method = self::getAccessorForProperty($object, $name, $accessorType);
            if ($method === null) {
                continue;
            }

            try {
                $data[$name] = $object->$method();
            } catch (Exception $e) {
                throw new Pandamp_BeanContext_Exception($e->getMessage());
            }
        }

        return $data;
    }

    /**
     * Tries to instantiate a class based on the passed arguments.
     *
     * @param string $className The class that should be created. Must
     * implement the interface Pandamp_BeanContext
     * @param array $properties An associative array with key/value-pairs,
     * representing the object properties to set. The Constructor will search for
     * appropriate getter/setter methods and call them, passing the values as
     * arguments.
     * @param boolean $ignoreMissingSetter true to not throw an error if a property
     * occures for which no setter is available
     *
     * @return Object
     * @throws Pandamp_BeanContext_Exception if any error during object
     * instantiating or class loading occured
     */
    public static function create($className, $properties, $ignoreMissingSetter = false)
    {
        self::loadClass($className);

        if (!class_exists($className, false)) {
            throw new Pandamp_BeanContext_Exception("$className not found.");
        }

        $object = new $className;

        if (!($object instanceof Pandamp_BeanContext)) {
            throw new Pandamp_BeanContext_Exception("$className does not implement Pandamp_BeanContext.");
        }

        try {
            foreach ($properties as $property => $value) {
                /**
                 * @todo better regex for anything except letters
                 */
                if (self::isTransient($property, $object) === false) {
                    self::setProperty($object, $property, $value, $ignoreMissingSetter);
                }
            }
        } catch (Exception $e) {
            if (($e instanceof Pandamp_BeanContext_Exception)) {
                throw $e;
            } else {
                throw new Pandamp_BeanContext_Exception($e->getMessage() . '[originally thrown by '.get_class($e).']');
            }
        }

        return $object;
    }

    /**
     * Helper for getting a getter-method based on the property passed
     * as the argument.
     *
     * @param Object $object The object in which the accessor should be looked up
     * @param string $propertyName The name of the property which accessor has to
     * be looked up
     * @param string $accessorType Which accessor-method to look up, either 'set'
     * or 'get'
     *
     * @return mixed <tt>null</tt>, if no appropriate getter method exists
     * for the property, otherwise the method name.
     */
    private static function getAccessorForProperty($object, $propertyName, $accessorType = self::GETTER)
    {
        $prefix = 'get';

        switch ($accessorType) {
            case self::GETTER:
                $prefix = 'get';
            break;

            case self::SETTER:
                $prefix = 'set';
            break;

            default:
                return null;
        }

        $method = '';

        if ($accessorType === self::SETTER) {
            if (strpos($propertyName, 'is') === 0) {
                $method = 'set'.substr($propertyName, 2);
            } else {
                $method = 'set'.ucfirst($propertyName);
            }
        } else if ($accessorType === self::GETTER) {
            if (strpos($propertyName, 'is') === 0) {
                $method = 'is'.ucfirst($propertyName);
            } else {
                $method = 'get'.ucfirst($propertyName);
            }
        }

        if (method_exists($object, $method)) {
            return $method;
        }

        return null;
    }

    private static function setProperty($object, $property, $value, $ignoreMissing = false)
    {
        $method = self::getAccessorForProperty($object, $property, self::SETTER);

        if ($method === null && $ignoreMissing == false) {
            throw new Pandamp_BeanContext_Exception("Setter for $property not found.")  ;
        } else if ($method === null) {
            return;
        }

        $object->$method($value);
    }

    /**
     * Helper for loading a class.
     *
     */
    public static function loadClass($className)
    {
        if (!class_exists($className, false)) {
            $path = str_replace('_', '/', $className);
            include_once $path.'.php';
        }
    }

}