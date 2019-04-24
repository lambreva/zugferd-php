<?php

namespace Pyrexx\ZUGFeRD\Model;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;

/**
 * Class DocumentContext
 *
 * @package Pyrexx\ZUGFeRD\Model
 */
class DocumentContext
{
    /**
     * @var \Pyrexx\ZUGFeRD\Model\ContextParameterID
     * @Type("Pyrexx\ZUGFeRD\Model\ContextParameterID")
     * @XmlElement(namespace="urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:100")
     * @SerializedName("GuidelineSpecifiedDocumentContextParameter")
     */
    private $type;

    /**
     * DocumentContext constructor.
     *
     * @param string $type
     */
    public function __construct(string $type)
    {
        $this->type = new ContextParameterID($type);
    }
}
