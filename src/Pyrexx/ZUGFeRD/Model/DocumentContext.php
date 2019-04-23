<?php namespace Pyrexx\ZUGFeRD\Model;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;

class DocumentContext
{
    /**
     * @var \Pyrexx\ZUGFeRD\Model\ContextParameterID
     * @Type("Pyrexx\ZUGFeRD\Model\ContextParameterID")
     * @XmlElement(namespace="urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:100")
     * @SerializedName("GuidelineSpecifiedDocumentContextParameter")
     */
    private $type;

    public function __construct($type)
    {
        //todo: check type
        $this->type = new ContextParameterID('urn:un:unece:uncefact:data:standard:CrossIndustryInvoice:100:' . strtolower($type));
    }
}
