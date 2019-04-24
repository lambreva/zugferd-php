<?php

namespace Pyrexx\ZUGFeRD\Model;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;

/**
 * Class ContextParameterID
 */
class ContextParameterID
{
    /**
     * @Type("string")
     * @XmlElement(cdata=false, namespace="urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:100")
     * @SerializedName("ID")
     */
    private $id;

    /**
     * ContextParameterID constructor.
     *
     * @param string $type
     */
    public function __construct(string $type)
    {
        switch ($type) {
            case Document::TYPE_MINIMUM:
                $id = 'urn:zugferd.de:2p0:minimum';
                break;
            case Document::TYPE_BASIC_WL:
                $id = 'urn:zugferd.de:2p0:basicwl';
                break;
            case Document::TYPE_COMFORT:
                $id = 'urn:cen.eu:en16931:2017';
                break;
            case Document::TYPE_EXTENDED:
                $id = 'urn:cen.eu:en16931:2017#conformant#urn:zugferd.de:2p0:extended';
                break;
            case Document::TYPE_BASIC:
            default:
                $id = 'urn:cen.eu:en16931:2017#compliant#urn:zugferd.de:2p0:basic';
                break;
        }

        $this->id = $id;
    }
}
