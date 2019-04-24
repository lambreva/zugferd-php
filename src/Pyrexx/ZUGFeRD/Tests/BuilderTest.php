<?php

namespace Pyrexx\ZUGFeRD\Tests;

use Pyrexx\ZUGFeRD\Builder;
use Pyrexx\ZUGFeRD\CodeList\Country;
use Pyrexx\ZUGFeRD\CodeList\Currency;
use Pyrexx\ZUGFeRD\CodeList\DateFormat;
use Pyrexx\ZUGFeRD\CodeList\PaymentMethod;
use Pyrexx\ZUGFeRD\CodeList\TaxCategory;
use Pyrexx\ZUGFeRD\CodeList\TaxId;
use Pyrexx\ZUGFeRD\CodeList\TaxType;
use Pyrexx\ZUGFeRD\Helper\AnnotationRegistryHelper;
use Pyrexx\ZUGFeRD\Model\Address;
use Pyrexx\ZUGFeRD\Model\AllowanceCharge;
use Pyrexx\ZUGFeRD\Model\Date;
use Pyrexx\ZUGFeRD\Model\Document;
use Pyrexx\ZUGFeRD\Model\Note;
use Pyrexx\ZUGFeRD\Model\Trade\Amount;
use Pyrexx\ZUGFeRD\Model\Trade\CreditorFinancialAccount;
use Pyrexx\ZUGFeRD\Model\Trade\CreditorFinancialInstitution;
use Pyrexx\ZUGFeRD\Model\Trade\Delivery;
use Pyrexx\ZUGFeRD\Model\Trade\Item\LineDocument;
use Pyrexx\ZUGFeRD\Model\Trade\Item\LineItem;
use Pyrexx\ZUGFeRD\Model\Trade\Item\Price;
use Pyrexx\ZUGFeRD\Model\Trade\Item\Product;
use Pyrexx\ZUGFeRD\Model\Trade\Item\Quantity;
use Pyrexx\ZUGFeRD\Model\Trade\Item\SpecifiedTradeAgreement;
use Pyrexx\ZUGFeRD\Model\Trade\Item\SpecifiedTradeDelivery;
use Pyrexx\ZUGFeRD\Model\Trade\Item\SpecifiedTradeMonetarySummation;
use Pyrexx\ZUGFeRD\Model\Trade\Item\SpecifiedTradeSettlement;
use Pyrexx\ZUGFeRD\Model\Trade\MonetarySummation;
use Pyrexx\ZUGFeRD\Model\Trade\PaymentMeans;
use Pyrexx\ZUGFeRD\Model\Trade\PaymentTerms;
use Pyrexx\ZUGFeRD\Model\Trade\Settlement;
use Pyrexx\ZUGFeRD\Model\Trade\Tax\TaxRegistration;
use Pyrexx\ZUGFeRD\Model\Trade\Tax\TradeTax;
use Pyrexx\ZUGFeRD\Model\Trade\Trade;
use Pyrexx\ZUGFeRD\Model\Trade\TradeParty;
use Pyrexx\ZUGFeRD\Model\UnitCode;
use Pyrexx\ZUGFeRD\SchemaValidator;
use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase
{
    /**
     * @before
     */
    public function setupAnnotationRegistry()
    {
        AnnotationRegistryHelper::registerAutoloadNamespace();
    }

    public function testGetXML()
    {
        $zugferdXML = file_get_contents(__DIR__ . '/ZUGFeRD-2.0-builder.xml');

        $doc = new Document(Document::TYPE_COMFORT);
        $doc->getHeader()
            ->setId('RE1337')
            ->setDate(new Date(new \DateTime('20130305'), DateFormat::CALENDAR_DATE))
            ->addNote(new Note('Test Node 1'))
            ->addNote(new Note('Test Node 2'))
            ->addNote(new Note('Easybill GmbH
            Düsselstr. 21
            41564 Kaarst

            Geschäftsführer:
            Christian Szardenings
            Ronny Keyser', 'REG'));

        $trade = $doc->getTrade();

        $trade->setDelivery(new Delivery('20130305', DateFormat::CALENDAR_DATE));

        $this->setAgreement($trade);
        $this->setLineItem($trade);
        $this->setSettlement($trade);

        $builder = Builder::create();
        $xml = $builder->getXML($doc);

        $this->assertSame($zugferdXML, $xml);

        SchemaValidator::isValid($xml);
    }

    /**
     * @param Trade $trade
     */
    private function setAgreement(Trade $trade)
    {
        $trade->getAgreement()
            ->setSeller(
                new TradeParty('Lieferant GmbH',
                    new Address('80333', 'Lieferantenstraße 20', null, 'München', Country::GERMANY),
                    [
                        new TaxRegistration(TaxId::FISCAL_NUMBER, '201/113/40209'),
                        new TaxRegistration(TaxId::VAT, 'DE123456789')
                    ]
                )
            )->setBuyer(
                new TradeParty('Kunden AG Mitte',
                    new Address('69876', 'Hans Muster', 'Kundenstraße 15', 'Frankfurt', Country::GERMANY)
                )
            );
    }

    /**
     * @param Trade $trade
     */
    private function setLineItem(Trade $trade)
    {
        $tradeAgreement = new SpecifiedTradeAgreement();

        $grossPrice = new Price(9.90, Currency::EUR, true);
        $grossPrice
            ->addAllowanceCharge(new AllowanceCharge(false, 1.80));

        $tradeAgreement->setGrossPrice($grossPrice);
        $tradeAgreement->setNetPrice(new Price(9.90, Currency::EUR, true));

        $lineItemTradeTax = new TradeTax();
        $lineItemTradeTax->setCode(TaxType::VAT);
        $lineItemTradeTax->setPercent(19.00);
        $lineItemTradeTax->setCategory(TaxCategory::STANDARD);

        $lineItemSettlement = new SpecifiedTradeSettlement();
        $lineItemSettlement
            ->setTradeTax($lineItemTradeTax)
            ->setMonetarySummation(new SpecifiedTradeMonetarySummation(198.00));

        $lineItem = new LineItem();
        $lineItem
            ->setTradeAgreement($tradeAgreement)
            ->setDelivery(new SpecifiedTradeDelivery(new Quantity(UnitCode::PIECE, 20.00)))
            ->setSettlement($lineItemSettlement)
            ->setProduct(new Product('TB100A4', 'Trennblätter A4'))
            ->setLineDocument(new LineDocument('1'))
            ->getLineDocument()
            ->addNote(new Note('Testcontent in einem LineDocument'));

        $trade->addLineItem($lineItem);
    }

    /**
     * @param Trade $trade
     */
    private function setSettlement(Trade $trade)
    {
        $settlement = new Settlement('2013-471102', Currency::EUR);
        $settlement->setPaymentTerms(new PaymentTerms('Zahlbar innerhalb von 20 Tagen (bis zum 05.10.2016) unter Abzug von 3% Skonto (Zahlungsbetrag = 1.766,03 €). Bis zum 29.09.2016 ohne Abzug.', new Date('20130404')));

        $settlement->setPaymentMeans(new PaymentMeans());
        $settlement->getPaymentMeans()
            ->setCode(PaymentMethod::CHECK)
            ->setInformation('Überweisung')
            ->setPayeeAccount(new CreditorFinancialAccount('DE08700901001234567890', '', ''))
            ->setPayeeInstitution(new CreditorFinancialInstitution('GENODEF1M04'));

        $tradeTax = new TradeTax();
        $tradeTax->setCode(TaxType::VAT);
        $tradeTax->setCategory(TaxCategory::STANDARD);
        $tradeTax->setPercent(7.00);
        $tradeTax->setBasisAmount(new Amount(275.00, Currency::EUR));
        $tradeTax->setCalculatedAmount(new Amount(19.25, Currency::EUR));

        $tradeTax2 = new TradeTax();
        $tradeTax2->setCode(TaxType::VAT);
        $tradeTax2->setCategory(TaxCategory::STANDARD);
        $tradeTax2->setPercent(19.00);
        $tradeTax2->setBasisAmount(new Amount(198.00, Currency::EUR));
        $tradeTax2->setCalculatedAmount(new Amount(37.62, Currency::EUR));

        $settlement
            ->addTradeTax($tradeTax)
            ->addTradeTax($tradeTax2)
            ->setMonetarySummation(
                new MonetarySummation(198.00, 0.00, 0.00, 198.00, 37.62, 235.62, 235.62, Currency::EUR)
            );

        $trade->setSettlement($settlement);
    }
}
