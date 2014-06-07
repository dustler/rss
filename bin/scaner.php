<?php

use FastFeed\Factory;

require_once __DIR__ . "/../src/bootstrap.php";

$entityManager = App\Registry::getEntityManager();

$q = $entityManager->createQuery("select r from App\Model\Resource r where r.lastPubDate < ?1 or r.lastPubDate is null");
$q->setParameter(1, date("Y-m-d H:i:s", time()));

$resources = $q->getResult();

/**
 * @var App\Model\Resource $resource
 */
foreach ($resources as $resource) {

    $resourceId = $resource->getId();
    $resourceLink = $resource->getLink();
    $resourceLastPub = $resource->getLastPubDate() == null ? 0 : $resource->getLastPubDate()->getTimestamp();

    $fastFeed = Factory::create();
    $fastFeed->addFeed('default', $resourceLink);
    $items = $fastFeed->fetch('default');

    $newItems = array();

    if (count($items) > 100) {
        $items = array_slice($items, 0, 100);
    }

    foreach ($items as $item) {
        $itemPubDate = $item->getDate();
        if ($itemPubDate === false) {
            $itemPubDate = time();
        } else {
            $itemPubDate = $itemPubDate->getTimestamp();
        }

        $newItem = new App\Model\Item();
        $newItem->setResourceId($resourceId)
            ->setLink($item->getId())
            ->setContent($item->getContent())
            ->setPubDate(new DateTime(date("Y-m-d H:i:s", $itemPubDate)))
            ->setCreatedAt(new DateTime('now'));

        $newItems[] = $newItem;
    }

    /**
     * @var App\Model\Item $newItem
     */
    foreach ($newItems as $newItem) {
        $item = $entityManager->getRepository('App\Model\Item')->findOneBy(array(
            "resourceId" => $resourceId,
            "link" => $newItem->getLink()
        ));

        if ($item === null) {
            $entityManager->persist($newItem);
        }
    }

    $resource->setLastPubDate(new DateTime('now'));
    $entityManager->persist($resource);

    $entityManager->flush();
}