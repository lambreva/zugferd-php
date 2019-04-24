<?php

namespace Pyrexx\ZUGFeRD;


class SchemaValidator
{
    /**
     * Validates the given XML-string against the ZUGFeRD XSD-files.
     *
     * @param string $xml
     *
     * @return bool
     */
    public static function isValid($xml)
    {
        $xmlValidate = new \DOMDocument();
        $xmlValidate->loadXML($xml);

        return $xmlValidate->schemaValidate(__DIR__ . '/Assets/Schema/EN16931/zugferd2p0_en16931.xsd');
    }
}
