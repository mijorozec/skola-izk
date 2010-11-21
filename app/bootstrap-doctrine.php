<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
use Nette\Environment;

// načítáme objekt s konfigurací
$doctrine = Environment::getConfig('doctrine');

$config = new Configuration();
// nastavíme složky a namespaces
$config->setProxyDir($doctrine->proxyDir);
$config->setProxyNamespace($doctrine->proxyNamespace);
// v produkčním prostředí nechceme generovat proxy třídy automaticky
// nastavením na FALSE můžeme vynutit manuální vytváření za všech okolností
$config->setAutoGenerateProxyClasses(!Environment::isProduction());

$metadataDriver = $config->newDefaultAnnotationDriver($doctrine->entityDir);
$config->setMetadataDriverImpl($metadataDriver);

// v produkčním prostředí zkusíme použít APCCache, jinak ArrayCache
if (Environment::isProduction() && extension_loaded('apc')) {
        $cache = new Doctrine\Common\Cache\ApcCache();
} else {
        $cache = new Doctrine\Common\Cache\ArrayCache();
}

$config->setMetadataCacheImpl($cache);
$config->setQueryCacheImpl($cache);

// cleanup
unset($cache);

$eventManager = new Doctrine\Common\EventManager();
// nastavení charsetů pro MySQL
if ($doctrine->connection->driver == 'pdo_mysql') {
        $eventManager->addEventSubscriber(new \Doctrine\DBAL\Event\Listeners\MysqlSessionInit('utf8', 'utf8_slovak_ci'));
}
// Vytvoření samotného EntityManageru
$entityManager = EntityManager::create((array) $doctrine->connection, $config, $eventManager);
Environment::getContext()->addService('Doctrine\ORM\EntityManager', $entityManager);

//$config->setSQLLogger(\Nella\Panels\Doctrine2Panel::getAndRegister());

//\Doctrine\DBAL\Types\Type::addType('image', 'Kaleva\DatabaseTypes\Image');
//$entityManager->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('image', 'image');

// cleanup
unset($doctrine, $config, $metadataDriver, $eventManager, $entityManager);

Environment::setServiceAlias('Doctrine\\ORM\\EntityManager', 'EntityManager');
Environment::setServiceAlias('School\\Application\\DatabaseManager', 'DatabaseManager');