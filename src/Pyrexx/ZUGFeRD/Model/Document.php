<?php

namespace Pyrexx\ZUGFeRD\Model;

use Pyrexx\ZUGFeRD\Model\Trade\Trade;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlNamespace;
use JMS\Serializer\Annotation\XmlRoot;

/**
 * Class Document
 *
 * @XmlRoot("rsm:CrossIndustryInvoice")
 * @XmlNamespace(uri="urn:un:unece:uncefact:data:standard:QualifiedDataType:100", prefix="a")
 * @XmlNamespace(uri="urn:un:unece:uncefact:data:standard:CrossIndustryInvoice:100", prefix="rsm")
 * @XmlNamespace(uri="urn:un:unece:uncefact:data:standard:QualifiedDataType:10", prefix="qdt")
 * @XmlNamespace(uri="urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:100", prefix="ram")
 * @XmlNamespace(uri="http://www.w3.org/2001/XMLSchema", prefix="xs")
 * @XmlNamespace(uri="urn:un:unece:uncefact:data:standard:UnqualifiedDataType:100", prefix="udt")
 */
class Document
{
    const TYPE_MINIMUM = 'MINIMUM';
    const TYPE_BASIC_WL = 'BASIC WL';
    const TYPE_BASIC = 'BASIC';
    const TYPE_COMFORT = 'EN 16931';
    const TYPE_EXTENDED = 'EXTENDED';

    /**
     * @Type("Pyrexx\ZUGFeRD\Model\DocumentContext")
     * @XmlElement(namespace="urn:un:unece:uncefact:data:standard:CrossIndustryInvoice:100")
     * @SerializedName("ExchangedDocumentContext")
     */
    private $context;

    /**
     * @Type("Pyrexx\ZUGFeRD\Model\Header")
     * @XmlElement(namespace="urn:un:unece:uncefact:data:standard:CrossIndustryInvoice:100")
     * @SerializedName("ExchangedDocument")
     */
    private $header;

    /**
     * @var Trade
     * @Type("Pyrexx\ZUGFeRD\Model\Trade\Trade")
     * @XmlElement(cdata=false, namespace="urn:un:unece:uncefact:data:standard:CrossIndustryInvoice:100")
     * @SerializedName("SupplyChainTradeTransaction")
     */
    private $trade;

    /**
     * Document constructor.
     *
     * @param string $type
     */
    public function __construct($type = self::TYPE_BASIC)
    {
        $this->context = new DocumentContext($type);
        $this->header = new Header();
        $this->trade = new Trade();
    }

    /**
     * @return DocumentContext
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return Header
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param Header $header
     */
    public function setHeader($header)
    {
        $this->header = $header;
    }

    /**
     * @return \Pyrexx\ZUGFeRD\Model\Trade\Trade
     */
    public function getTrade()
    {
        return $this->trade;
    }

    /**
     * @param \Pyrexx\ZUGFeRD\Model\Trade\Trade $trade
     *
     */
    public function setTrade(Trade $trade)
    {
        $this->trade = $trade;
    }
}
