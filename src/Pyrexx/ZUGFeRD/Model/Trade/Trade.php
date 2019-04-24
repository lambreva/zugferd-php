<?php

namespace Pyrexx\ZUGFeRD\Model\Trade;

use Pyrexx\ZUGFeRD\Model\Trade\Item\LineItem;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlList;

/**
 * Class Trade
 *
 * @package Pyrexx\ZUGFeRD\Model\Trade
 */
class Trade
{
    /**
     * @var LineItem[]
     * @Type("array<Pyrexx\ZUGFeRD\Model\Trade\Item\LineItem>")
     * @XmlList(inline=true, entry="IncludedSupplyChainTradeLineItem", namespace="urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:100")
     */
    private $lineItems = [];

    /**
     * @var Agreement
     * @Type("Pyrexx\ZUGFeRD\Model\Trade\Agreement")
     * @XmlElement(cdata=false, namespace="urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:100")
     * @SerializedName("ApplicableHeaderTradeAgreement")
     */
    private $agreement;

    /**
     * @var Delivery
     * @Type("Pyrexx\ZUGFeRD\Model\Trade\Delivery")
     * @XmlElement(cdata=false, namespace="urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:100")
     * @SerializedName("ApplicableHeaderTradeDelivery")
     */
    private $delivery;

    /**
     * @var Settlement
     * @Type("Pyrexx\ZUGFeRD\Model\Trade\Settlement")
     * @XmlElement(cdata=false, namespace="urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:100")
     * @SerializedName("ApplicableHeaderTradeSettlement")
     */
    private $settlement;

    /**
     * Trade constructor.
     */
    public function __construct()
    {
        $this->agreement = new Agreement();
        $this->delivery = new Delivery();
        $this->settlement = new Settlement();
    }

    /**
     * @return Agreement
     */
    public function getAgreement()
    {
        return $this->agreement;
    }

    /**
     * @param Agreement $agreement
     *
     * @return self
     */
    public function setAgreement(Agreement $agreement)
    {
        $this->agreement = $agreement;
        return $this;
    }

    /**
     * @return \Pyrexx\ZUGFeRD\Model\Trade\Delivery
     */
    public function getDelivery()
    {
        return $this->delivery;
    }

    /**
     * @param \Pyrexx\ZUGFeRD\Model\Trade\Delivery $delivery
     *
     * @return self
     */
    public function setDelivery($delivery)
    {
        $this->delivery = $delivery;
        return $this;
    }

    /**
     * @return \Pyrexx\ZUGFeRD\Model\Trade\Settlement
     */
    public function getSettlement()
    {
        return $this->settlement;
    }

    /**
     * @param \Pyrexx\ZUGFeRD\Model\Trade\Settlement $settlement
     *
     * @return self
     */
    public function setSettlement($settlement)
    {
        $this->settlement = $settlement;
        return $this;
    }

    /**
     * @return \Pyrexx\ZUGFeRD\Model\Trade\Item\LineItem[]
     */
    public function getLineItems()
    {
        return $this->lineItems;
    }

    /**
     * @param \Pyrexx\ZUGFeRD\Model\Trade\Item\LineItem $lineItem
     *
     * @return self
     */
    public function addLineItem(LineItem $lineItem)
    {
        $this->lineItems[] = $lineItem;
        return $this;
    }
}
