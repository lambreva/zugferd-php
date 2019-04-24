<?php

namespace Pyrexx\ZUGFeRD\Model\Trade\Item;

use Pyrexx\ZUGFeRD\Model\Trade\Tax\TradeTax;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;

/**
 * Class SpecifiedTradeSettlement
 *
 * @package Pyrexx\ZUGFeRD\Model\Trade\Item
 */
class SpecifiedTradeSettlement
{
    /**
     * @var TradeTax
     * @Type("Pyrexx\ZUGFeRD\Model\Trade\Tax\TradeTax")
     * @XmlElement(cdata=false, namespace="urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:100")
     * @SerializedName("ApplicableTradeTax")
     */
    private $tradeTax;

    /**
     * @var SpecifiedTradeMonetarySummation
     * @Type("Pyrexx\ZUGFeRD\Model\Trade\Item\SpecifiedTradeMonetarySummation")
     * @XmlElement(cdata=false, namespace="urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:100")
     * @SerializedName("SpecifiedTradeSettlementLineMonetarySummation")
     */
    private $monetarySummation;

    /**
     * @return \Pyrexx\ZUGFeRD\Model\Trade\Tax\TradeTax
     */
    public function getTradeTax()
    {
        return $this->tradeTax;
    }

    /**
     * @param \Pyrexx\ZUGFeRD\Model\Trade\Tax\TradeTax $tradeTax
     *
     * @return self
     */
    public function setTradeTax($tradeTax)
    {
        $this->tradeTax = $tradeTax;

        return $this;
    }

    /**
     * @return \Pyrexx\ZUGFeRD\Model\Trade\Item\SpecifiedTradeMonetarySummation
     */
    public function getMonetarySummation()
    {
        return $this->monetarySummation;
    }

    /**
     * @param \Pyrexx\ZUGFeRD\Model\Trade\Item\SpecifiedTradeMonetarySummation $monetarySummation
     *
     * @return self
     */
    public function setMonetarySummation($monetarySummation)
    {
        $this->monetarySummation = $monetarySummation;

        return $this;
    }
}
