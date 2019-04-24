<?php

namespace Pyrexx\ZUGFeRD;

use Pyrexx\ZUGFeRD\Model\Document;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

/**
 * Class Reader
 *
 * @package Pyrexx\ZUGFeRD
 */
class Reader
{
    private $serializer;

    /**
     * Reader constructor.
     *
     * @param Serializer $serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param $xml
     *
     * @return mixed|Document
     */
    public function getDocument($xml)
    {
        return $this->serializer->deserialize($xml, 'Pyrexx\ZUGFeRD\Model\Document', 'xml');
    }

    /**
     * @return Reader
     */
    public static function create()
    {
        $serializer = SerializerBuilder::create()
            ->setDebug(true)
            ->build();

        return new self($serializer);
    }
}
