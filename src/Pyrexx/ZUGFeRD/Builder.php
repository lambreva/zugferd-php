<?php

namespace Pyrexx\ZUGFeRD;

use Pyrexx\ZUGFeRD\Model\Document;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

/**
 * Class Builder
 *
 * @package Pyrexx\ZUGFeRD
 */
class Builder
{
    private $serializer;

    /**
     * Builder constructor.
     *
     * @param Serializer $serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param Document $document
     *
     * @return mixed|string
     */
    public function getXML(Document $document)
    {
        return $this->serializer->serialize($document, 'xml');
    }

    /**
     * @return Builder
     */
    public static function create()
    {
        $serializer = SerializerBuilder::create()
            ->setDebug(true)
            ->build();

        return new self($serializer);
    }
}
