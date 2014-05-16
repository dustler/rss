<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

define("APP_DIR", realpath(__DIR__ . "/../"));

$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/src/App/Models"), false);

$conn = array(
    'driver'   => 'pdo_mysql',
    'user'     => 'root',
    'password' => '',
    'dbname'   => 'rss',
);

$entityManager = EntityManager::create($conn, $config);

App\Registry::setEntityManager($entityManager);
