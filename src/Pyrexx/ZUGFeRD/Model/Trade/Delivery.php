<?php

namespace Pyrexx\ZUGFeRD\Model\Trade;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use Pyrexx\ZUGFeRD\CodeList\DateFormat;

/**
 * Class Delivery
 *
 * @package Pyrexx\ZUGFeRD\Model\Trade
 */
class Delivery
{
    /**
     * @var DeliveryChainEvent
     * @Type("Pyrexx\ZUGFeRD\Model\Trade\DeliveryChainEvent")
     * @XmlElement(cdata=false, namespace="urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:100")
     * @SerializedName("ActualDeliverySupplyChainEvent")
     */
    private $chainEvent;

    /**
     * Delivery constructor.
     *
     * @param string $date
     * @param int    $format
     */
    public function __construct($date = '', $format = DateFormat::CALENDAR_DATE)
    {
        $this->chainEvent = new DeliveryChainEvent($date, $format);
    }

    /**
     * @return \Pyrexx\ZUGFeRD\Model\Trade\DeliveryChainEvent
     */
    public function getChainEvent()
    {
        return $this->chainEvent;
    }

    /**
     * @param \Pyrexx\ZUGFeRD\Model\Trade\DeliveryChainEvent $chainEvent
     */
    public function setChainEvent($chainEvent)
    {
        $this->chainEvent = $chainEvent;
    }
}
