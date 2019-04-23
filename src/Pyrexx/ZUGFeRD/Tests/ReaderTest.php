<?php

namespace Pyrexx\ZUGFeRD\Tests;

use Pyrexx\ZUGFeRD\CodeList\Country;
use Pyrexx\ZUGFeRD\CodeList\Currency;
use Pyrexx\ZUGFeRD\CodeList\DateFormat;
use Pyrexx\ZUGFeRD\CodeList\DocumentType;
use Pyrexx\ZUGFeRD\CodeList\PaymentMethod;
use Pyrexx\ZUGFeRD\CodeList\TaxCategory;
use Pyrexx\ZUGFeRD\CodeList\TaxId;
use Pyrexx\ZUGFeRD\CodeList\TaxType;
use Pyrexx\ZUGFeRD\Helper\AnnotationRegistryHelper;
use Pyrexx\ZUGFeRD\Model\Date;
use Pyrexx\ZUGFeRD\Model\Document;
use Pyrexx\ZUGFeRD\Model\Header;
use Pyrexx\ZUGFeRD\Model\Note;
use Pyrexx\ZUGFeRD\Model\Trade\Agreement;
use Pyrexx\ZUGFeRD\Model\Trade\Item\LineItem;
use Pyrexx\ZUGFeRD\Model\Trade\Settlement;
use Pyrexx\ZUGFeRD\Model\Trade\Trade;
use Pyrexx\ZUGFeRD\Model\Trade\TradeParty;
use Pyrexx\ZUGFeRD\Model\UnitCode;
use Pyrexx\ZUGFeRD\Reader;
use PHPUnit\Framework\TestCase;

class ReaderTest extends TestCase
{
    /**
     * @before
     */
    public function setupAnnotationRegistry()
    {
        AnnotationRegistryHelper::registerAutoloadNamespace();
    }

    public function testGetDocument()
    {
        $zugferdXML = file_get_contents(__DIR__ . '/ZUGFeRD-2.0-reader.xml');

        $reader = Reader::create();

        $doc = $reader->getDocument($zugferdXML);
        $this->assertInstanceOf(Document::class, $doc);

        $this->checkHeader($doc->getHeader());
        $this->checkTrade($doc->getTrade());
    }

    private function checkHeader(Header $header)
    {
        $this->assertSame('RE1337', $header->getId());
        $this->assertSame(DocumentType::COMMERCIAL_INVOICE, $header->getTypeCode());

        $this->assertInstanceOf(Date::class, $header->getDate());
        $this->assertSame(DateFormat::CALENDAR_DATE, $header->getDate()->getFormat());
        $this->assertSame('20130305', $header->getDate()->getDate());

        $notes = $header->getNotes();
        $this->assertCount(3, $notes);

        $cnt = 0;
        foreach ($notes as $note) {
            $cnt++;
            $this->assertInstanceOf(Note::class, $note);

            if ($cnt === 3) {
                $this->assertSame('Easybill GmbH
            Düsselstr. 21
            41564 Kaarst

            Geschäftsführer:
            Christian Szardenings
            Ronny Keyser', $note->getContent());
                $this->assertSame('REG', $note->getSubjectCode());

            } else {
                $this->assertSame('Test Node ' . $cnt, $note->getContent());
            }
        }
    }

    private function checkTrade(Trade $trade)
    {
        $this->assertInstanceOf(Trade::class, $trade);

        $this->checkAgreement($trade->getAgreement());
        $this->checkTradeSettlement($trade->getSettlement());

        $delivery = $trade->getDelivery();
        $this->assertSame(DateFormat::CALENDAR_DATE, $delivery->getChainEvent()->getDate()->getFormat());
        $this->assertSame('20130305', $delivery->getChainEvent()->getDate()->getDate());

        $lineItems = $trade->getLineItems();
        $this->assertCount(1, $lineItems);
        $this->checkLineItem($lineItems[0]);
    }

    private function checkAgreement(Agreement $agreement)
    {
        $seller = $agreement->getSeller();
        $buyer = $agreement->getBuyer();
        $this->assertInstanceOf(Agreement::class, $agreement);
        $this->assertInstanceOf(TradeParty::class, $seller);
        $this->assertInstanceOf(TradeParty::class, $buyer);

        $sellerAddress = $seller->getAddress();
        $this->assertSame('Lieferant GmbH', $seller->getName());
        $this->assertSame('80333', $sellerAddress->getPostcode());
        $this->assertSame('München', $sellerAddress->getCity());
        $this->assertSame('Lieferantenstraße 20', $sellerAddress->getLineOne());
        $this->assertSame(Country::GERMANY, $sellerAddress->getCountryCode());

        $sellerRegistrations = $seller->getTaxRegistrations();
        $this->assertCount(2, $sellerRegistrations);

        for ($cnt = 0; $cnt < 2; $cnt++) {
            $taxRegistration = $sellerRegistrations[$cnt];
            if ($cnt == 0) {
                $this->assertSame(TaxId::FISCAL_NUMBER, $taxRegistration->getRegistration()->getSchemeID());
                $this->assertSame('201/113/40209', $taxRegistration->getRegistration()->getValue());
            } else {
                $this->assertSame(TaxId::VAT, $taxRegistration->getRegistration()->getSchemeID());
                $this->assertSame('DE123456789', $taxRegistration->getRegistration()->getValue());
            }
        }

        $buyerAddress = $buyer->getAddress();
        $this->assertSame('Kunden AG Mitte', $buyer->getName());
        $this->assertSame('69876', $buyerAddress->getPostcode());
        $this->assertSame('Frankfurt', $buyerAddress->getCity());
        $this->assertSame('Hans Muster', $buyerAddress->getLineOne());
        $this->assertSame('Kundenstraße 15', $buyerAddress->getLineTwo());
        $this->assertSame(Country::GERMANY, $buyerAddress->getCountryCode());
        $this->assertEmpty($buyer->getTaxRegistrations());
    }

    private function checkTradeSettlement(Settlement $settlement)
    {
        $this->assertSame('2013-471102', $settlement->getPaymentReference());
        $this->assertSame(Currency::EUR, $settlement->getCurrency());

        $paymentMeans = $settlement->getPaymentMeans();
        $this->assertSame(PaymentMethod::BANK_TRANSFER, $paymentMeans->getCode());
        $this->assertSame('Überweisung', $paymentMeans->getInformation());

        $payeeAccount = $paymentMeans->getPayeeAccount();
        $this->assertSame('DE08700901001234567890', $payeeAccount->getIban());
        $this->assertEmpty($payeeAccount->getAccountName());
        $this->assertEmpty($payeeAccount->getProprietary());

        $payeeInstitution = $paymentMeans->getPayeeInstitution();
        $this->assertSame('GENODEF1M04', $payeeInstitution->getBic());

        $tradeTaxes = $settlement->getTradeTaxes();
        $this->assertCount(2, $tradeTaxes);

        $tradeTax1 = $tradeTaxes[0];
        $tradeTax2 = $tradeTaxes[1];
        $this->assertSame(Currency::EUR, $tradeTax1->getCalculatedAmount()->getCurrency());
        $this->assertSame(19.25, $tradeTax1->getCalculatedAmount()->getValue());
        $this->assertSame(TaxType::VAT, $tradeTax1->getCode());
        $this->assertSame(Currency::EUR, $tradeTax1->getBasisAmount()->getCurrency());
        $this->assertSame(275.00, $tradeTax1->getBasisAmount()->getValue());
        $this->assertSame(7.00, $tradeTax1->getPercent());

        $this->assertSame(Currency::EUR, $tradeTax2->getCalculatedAmount()->getCurrency());
        $this->assertSame(37.62, $tradeTax2->getCalculatedAmount()->getValue());
        $this->assertSame(TaxType::VAT, $tradeTax2->getCode());
        $this->assertSame(Currency::EUR, $tradeTax2->getBasisAmount()->getCurrency());
        $this->assertSame(198.00, $tradeTax2->getBasisAmount()->getValue());
        $this->assertSame(19.00, $tradeTax2->getPercent());

        $monetarySummation = $settlement->getMonetarySummation();
        $this->assertSame(198.00, $monetarySummation->getLineTotal()->getValue());
        $this->assertSame(Currency::EUR, $monetarySummation->getLineTotal()->getCurrency());

        $this->assertSame(0.00, $monetarySummation->getChargeTotal()->getValue());
        $this->assertSame(Currency::EUR, $monetarySummation->getChargeTotal()->getCurrency());

        $this->assertSame(0.00, $monetarySummation->getAllowanceTotal()->getValue());
        $this->assertSame(Currency::EUR, $monetarySummation->getAllowanceTotal()->getCurrency());

        $this->assertSame(198.00, $monetarySummation->getTaxBasisTotal()->getValue());
        $this->assertSame(Currency::EUR, $monetarySummation->getTaxBasisTotal()->getCurrency());

        $this->assertSame(37.62, $monetarySummation->getTaxTotal()->getValue());
        $this->assertSame(Currency::EUR, $monetarySummation->getTaxTotal()->getCurrency());

        $this->assertSame(235.62, $monetarySummation->getGrandTotal()->getValue());
        $this->assertSame(Currency::EUR, $monetarySummation->getGrandTotal()->getCurrency());

        $paymentTerms = $settlement->getPaymentTerms();
        $this->assertSame('Zahlbar innerhalb 30 Tagen netto bis 04.04.2013, 3% Skonto innerhalb 10 Tagen bis 15.03.2013', $paymentTerms->getDescription());
        $this->assertSame('20130404', $paymentTerms->getDueDate()->getDate());
        $this->assertSame(DateFormat::CALENDAR_DATE, $paymentTerms->getDueDate()->getFormat());
    }

    private function checkLineItem(LineItem $lineItem)
    {
        $lineDocument = $lineItem->getLineDocument();
        $lineDocumentNotes = $lineDocument->getNotes();
        $this->assertSame('1', $lineDocument->getLineId());
        $this->assertCount(1, $lineDocumentNotes);
        $this->assertSame('Testcontent in einem LineDocument', $lineDocumentNotes[0]->getContent());

        $agreement = $lineItem->getTradeAgreement();
        $grossPrice = $agreement->getGrossPrice();

        $this->assertSame(9.90, $grossPrice->getAmount()->getValue());
        $this->assertSame(Currency::EUR, $grossPrice->getAmount()->getCurrency());

        $grossPriceAllowanceCharges = $grossPrice->getAllowanceCharges();
        $this->assertCount(1, $grossPriceAllowanceCharges);

        $allowanceCharge = $grossPriceAllowanceCharges[0];
        $this->assertFalse($allowanceCharge->getIndicator());
        $this->assertSame(Currency::EUR, $allowanceCharge->getActualAmount()->getCurrency());
        $this->assertSame(1.80, $allowanceCharge->getActualAmount()->getValue());

        $this->assertSame(9.90, $agreement->getNetPrice()->getAmount()->getValue());
        $this->assertSame(Currency::EUR, $agreement->getNetPrice()->getAmount()->getCurrency());

        $this->assertSame(UnitCode::PIECE, $lineItem->getDelivery()->getBilledQuantity()->getUnitCode());
        $this->assertSame(20.0000, $lineItem->getDelivery()->getBilledQuantity()->getValue());

        $settlement = $lineItem->getSettlement();
        $tradeTax = $settlement->getTradeTax();
        $this->assertSame(TaxType::VAT, $tradeTax->getCode());
        $this->assertSame(19.00, $tradeTax->getPercent());
        $this->assertSame(TaxCategory::STANDARD, $tradeTax->getCategory());

        $monetarySummationTotal = $settlement->getMonetarySummation()->getTotalAmount();
        $this->assertSame(198.00, $monetarySummationTotal->getValue());
        $this->assertSame(Currency::EUR, $monetarySummationTotal->getCurrency());

        $product = $lineItem->getProduct();
        $this->assertSame('TB100A4', $product->getSellerAssignedID());
        $this->assertSame('Trennblätter A4', $product->getName());
    }
}
