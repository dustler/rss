<?php

namespace App;

class Registry
{
    protected static $registry = array();

    protected static function set($name, $value)
    {
        self::$registry[$name] = $value;
    }

    protected static function get($name)
    {
        return self::$registry[$name];
    }

    public static function setEntityManager(\Doctrine\ORM\EntityManager $entityManager)
    {
        self::set("entityManager", $entityManager);
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public static function getEntityManager()
    {
        return self::get("entityManager");
    }
}