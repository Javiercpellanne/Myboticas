<?php

class GeneradorXML
{
	function CrearXMLFactura($nombrexml, $emisor, $cliente, $comprobante, $detalle)
   {
		$doc = new DOMDocument();
		$doc->formatOutput = FALSE;
		$doc->preserveWhiteSpace = TRUE;
		$doc->encoding = 'utf-8';

      $xml = '<?xml version="1.0" encoding="utf-8"?>
      <Invoice xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ccts="urn:un:unece:uncefact:documentation:2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2" xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2">
         <ext:UBLExtensions>
            <ext:UBLExtension>
               <ext:ExtensionContent/>
            </ext:UBLExtension>
         </ext:UBLExtensions>
         <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
         <cbc:CustomizationID schemeAgencyName="PE:SUNAT">2.0</cbc:CustomizationID>
         <cbc:ProfileID schemeName="Tipo de Operacion" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo17">'.$comprobante['toperacion'].'</cbc:ProfileID>
         <cbc:ID>'.$comprobante['serie'].'-'.$comprobante['numero'].'</cbc:ID>
         <cbc:IssueDate>'.$comprobante['femision'].'</cbc:IssueDate>
         <cbc:IssueTime>'.$comprobante['hemision'].'</cbc:IssueTime>
         <cbc:DueDate>'.$comprobante['fvencimiento'].'</cbc:DueDate>
         <cbc:InvoiceTypeCode listAgencyName="PE:SUNAT" listName="Tipo de Documento" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01" listID="'.$comprobante['toperacion'].'" name="Tipo de Operacion">'.$comprobante['tcomprobante'].'</cbc:InvoiceTypeCode>
         <cbc:DocumentCurrencyCode listID="ISO 4217 Alpha" listName="Currency" listAgencyName="United Nations Economic Commission for Europe">'.$comprobante['moneda'].'</cbc:DocumentCurrencyCode>
                  <cbc:LineCountNumeric>'.count($detalle).'</cbc:LineCountNumeric>
          <cac:Signature>
            <cbc:ID>'.$comprobante['serie'].'-'.$comprobante['numero'].'</cbc:ID>
            <cac:SignatoryParty>
               <cac:PartyIdentification>
                  <cbc:ID>'.$emisor['ruc'].'</cbc:ID>
               </cac:PartyIdentification>
               <cac:PartyName>
                  <cbc:Name><![CDATA['.$emisor['nombres'].']]></cbc:Name>
               </cac:PartyName>
            </cac:SignatoryParty>
            <cac:DigitalSignatureAttachment>
               <cac:ExternalReference>
                  <cbc:URI>#SignatureSP</cbc:URI>
               </cac:ExternalReference>
            </cac:DigitalSignatureAttachment>
         </cac:Signature>
         <cac:AccountingSupplierParty>
            <cac:Party>
               <cac:PartyIdentification>
                  <cbc:ID schemeID="6" schemeName="Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$emisor['ruc'].'</cbc:ID>
               </cac:PartyIdentification>
               <cac:PartyName>
                  <cbc:Name><![CDATA['.$emisor['nombres'].']]></cbc:Name>
               </cac:PartyName>
               <cac:PartyTaxScheme>
                  <cbc:RegistrationName><![CDATA['.$emisor['nombres'].']]></cbc:RegistrationName>
                  <cbc:CompanyID schemeID="6" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$emisor['ruc'].'</cbc:CompanyID>
                  <cac:TaxScheme>
                     <cbc:ID schemeID="6" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$emisor['ruc'].'</cbc:ID>
                  </cac:TaxScheme>
               </cac:PartyTaxScheme>
               <cac:PartyLegalEntity>
                  <cbc:RegistrationName><![CDATA['.$emisor['nombres'].']]></cbc:RegistrationName>
                  <cac:RegistrationAddress>
                     <cbc:ID schemeName="Ubigeos" schemeAgencyName="PE:INEI">'.$emisor['distrito'].'</cbc:ID>
                     <cbc:AddressTypeCode listAgencyName="PE:SUNAT" listName="Establecimientos anexos">'.$emisor['codigo'].'</cbc:AddressTypeCode>
                     <cbc:CityName><![CDATA['.$emisor['nprovincia'].']]></cbc:CityName>
                     <cbc:CountrySubentity><![CDATA['.$emisor['ndepartamento'].']]></cbc:CountrySubentity>
                     <cbc:District><![CDATA['.$emisor['ndistrito'].']]></cbc:District>
                     <cac:AddressLine>
                        <cbc:Line><![CDATA['.$emisor['direccion'].']]></cbc:Line>
                     </cac:AddressLine>
                     <cac:Country>
                        <cbc:IdentificationCode listID="ISO 3166-1" listAgencyName="United Nations Economic Commission for Europe" listName="Country">PE</cbc:IdentificationCode>
                     </cac:Country>
                  </cac:RegistrationAddress>
               </cac:PartyLegalEntity>
               <cac:Contact>
                  <cbc:Name><![CDATA[]]></cbc:Name>
               </cac:Contact>
            </cac:Party>
         </cac:AccountingSupplierParty>
         <cac:AccountingCustomerParty>
            <cac:Party>
               <cac:PartyIdentification>
                  <cbc:ID schemeID="'.$cliente->tdocumento.'" schemeName="Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$cliente->documento.'</cbc:ID>
               </cac:PartyIdentification>
               <cac:PartyName>
                  <cbc:Name><![CDATA['.$cliente->nombres.']]></cbc:Name>
               </cac:PartyName>
               <cac:PartyTaxScheme>
                  <cbc:RegistrationName><![CDATA['.$cliente->nombres.']]></cbc:RegistrationName>
                  <cbc:CompanyID schemeID="'.$cliente->tdocumento.'" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$cliente->documento.'</cbc:CompanyID>
                  <cac:TaxScheme>
                     <cbc:ID schemeID="'.$cliente->tdocumento.'" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$cliente->documento.'</cbc:ID>
                  </cac:TaxScheme>
               </cac:PartyTaxScheme>
               <cac:PartyLegalEntity>
                  <cbc:RegistrationName><![CDATA['.$cliente->nombres.']]></cbc:RegistrationName>
                  <cac:RegistrationAddress>
                     <cbc:ID schemeName="Ubigeos" schemeAgencyName="PE:INEI"/>
                     <cbc:CityName><![CDATA[]]></cbc:CityName>
                     <cbc:CountrySubentity><![CDATA[]]></cbc:CountrySubentity>
                     <cbc:District><![CDATA[]]></cbc:District>
                     <cac:AddressLine>
                        <cbc:Line><![CDATA['.$cliente->direccion.']]></cbc:Line>
                     </cac:AddressLine>
                     <cac:Country>
                        <cbc:IdentificationCode listID="ISO 3166-1" listAgencyName="United Nations Economic Commission for Europe" listName="Country"/>
                     </cac:Country>
                  </cac:RegistrationAddress>
               </cac:PartyLegalEntity>
            </cac:Party>
         </cac:AccountingCustomerParty>';

         if ($comprobante['detraccion']!='') {
            $detraccion=json_decode($comprobante['detraccion']);
            $xml.='<cac:PaymentMeans>
             <cbc:ID>Detraccion</cbc:ID>
             <cbc:PaymentMeansCode listName="Medio de pago" listAgencyName="PE:SUNAT" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo59">'.$detraccion->medio.'</cbc:PaymentMeansCode>
             <cac:PayeeFinancialAccount>
               <cbc:ID>'.$detraccion->ncuenta.'</cbc:ID>
             </cac:PayeeFinancialAccount>
           </cac:PaymentMeans>
           <cac:PaymentTerms>
             <cbc:ID>Detraccion</cbc:ID>
             <cbc:PaymentMeansID schemeName="Codigo de detraccion" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo54">'.$detraccion->codigo.'</cbc:PaymentMeansID>
             <cbc:PaymentPercent>'.$detraccion->factor.'</cbc:PaymentPercent>
             <cbc:Amount currencyID="PEN">'.$detraccion->monto.'</cbc:Amount>
           </cac:PaymentTerms>';
         }

         if ($comprobante['condicion']==1) {
            $xml.='<cac:PaymentTerms>
               <cbc:ID>FormaPago</cbc:ID>
               <cbc:PaymentMeansID>Contado</cbc:PaymentMeansID>
            </cac:PaymentTerms>';
         } else {
            $xml.='<cac:PaymentTerms>
               <cbc:ID>FormaPago</cbc:ID>
               <cbc:PaymentMeansID>Credito</cbc:PaymentMeansID>
               <cbc:Amount currencyID="'.$comprobante['moneda'].'">'.$comprobante['total'].'</cbc:Amount>
            </cac:PaymentTerms>';

            $feinicial=$comprobante['fpago'];
            for ($i=1; $i <= $comprobante['cuotas'] ; $i++) {
               if ($i == $comprobante['cuotas']) {
                  $cuota = $comprobante['total'] - ($comprobante['mcuota'] * ($comprobante['cuotas'] - 1));;
              } else {
                  $cuota = $comprobante['mcuota'];
              }
               $xml.='<cac:PaymentTerms>
                     <cbc:ID>FormaPago</cbc:ID>
                     <cbc:PaymentMeansID>Cuota'.zerofill($i,3).'</cbc:PaymentMeansID>
                     <cbc:Amount currencyID="'.$comprobante['moneda'].'">'.$cuota.'</cbc:Amount>
                     <cbc:PaymentDueDate>'.$feinicial.'</cbc:PaymentDueDate>
               </cac:PaymentTerms>';
               $suma=tiempoCuota($comprobante['pcuota']);
               $feinicial=SumarFecha($suma,$feinicial);
            }
         }

         if($comprobante['descuentos']!=''){
            $descuentos=json_decode($comprobante['descuentos']);
            $xml.='<cac:AllowanceCharge>
                 <cbc:ChargeIndicator>false</cbc:ChargeIndicator>
                 <cbc:AllowanceChargeReasonCode listName="Cargo/descuento" listAgencyName="PE:SUNAT"
                    listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo53">'.$descuentos->codigo.'</cbc:AllowanceChargeReasonCode>
                 <cbc:MultiplierFactorNumeric>'.$descuentos->factor.'</cbc:MultiplierFactorNumeric>
                 <cbc:Amount currencyID="'.$comprobante['moneda'].'">'.$descuentos->monto.'</cbc:Amount>
                 <cbc:BaseAmount currencyID="'.$comprobante['moneda'].'">'.$descuentos->base.'</cbc:BaseAmount>
            </cac:AllowanceCharge>';
         }

         if($comprobante['retencion']!=''){
            $descuentos=json_decode($comprobante['retencion']);
            $xml.='<cac:AllowanceCharge>
                 <cbc:ChargeIndicator>false</cbc:ChargeIndicator>
                 <cbc:AllowanceChargeReasonCode listName="Cargo/descuento" listAgencyName="PE:SUNAT"
                    listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo53">'.$descuentos->codigo.'</cbc:AllowanceChargeReasonCode>
                 <cbc:MultiplierFactorNumeric>'.$descuentos->factor.'</cbc:MultiplierFactorNumeric>
                 <cbc:Amount currencyID="'.$comprobante['moneda'].'">'.$descuentos->monto.'</cbc:Amount>
                 <cbc:BaseAmount currencyID="'.$comprobante['moneda'].'">'.$descuentos->base.'</cbc:BaseAmount>
            </cac:AllowanceCharge>';
         }

         $xml.='<cac:TaxTotal>
            <cbc:TaxAmount currencyID="'.$comprobante['moneda'].'">'.$comprobante['tigv'].'</cbc:TaxAmount>';
         if($comprobante['tgravado']>0){
         $xml.='<cac:TaxSubtotal>
               <cbc:TaxableAmount currencyID="'.$comprobante['moneda'].'">'.$comprobante['tgravado'].'</cbc:TaxableAmount>
               <cbc:TaxAmount currencyID="'.$comprobante['moneda'].'">'.$comprobante['tigv'].'</cbc:TaxAmount>
               <cac:TaxCategory>
                  <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">S</cbc:ID>
                  <cac:TaxScheme>
                     <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">1000</cbc:ID>
                     <cbc:Name>IGV</cbc:Name>
                     <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                  </cac:TaxScheme>
               </cac:TaxCategory>
            </cac:TaxSubtotal>';
         }

         if($comprobante['texonerado']>0){
         $xml.='<cac:TaxSubtotal>
               <cbc:TaxableAmount currencyID="'.$comprobante['moneda'].'">'.$comprobante['texonerado'].'</cbc:TaxableAmount>
               <cbc:TaxAmount currencyID="'.$comprobante['moneda'].'">0.00</cbc:TaxAmount>
               <cac:TaxCategory>
                  <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
                  <cac:TaxScheme>
                     <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9997</cbc:ID>
                     <cbc:Name>EXO</cbc:Name>
                     <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                  </cac:TaxScheme>
               </cac:TaxCategory>
            </cac:TaxSubtotal>';
         }

         if($comprobante['tinafecto']>0){
         $xml.='<cac:TaxSubtotal>
               <cbc:TaxableAmount currencyID="'.$comprobante['moneda'].'">'.$comprobante['tinafecto'].'</cbc:TaxableAmount>
               <cbc:TaxAmount currencyID="'.$comprobante['moneda'].'">0.00</cbc:TaxAmount>
               <cac:TaxCategory>
                  <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">O</cbc:ID>
                  <cac:TaxScheme>
                     <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9998</cbc:ID>
                     <cbc:Name>INA</cbc:Name>
                     <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                  </cac:TaxScheme>
               </cac:TaxCategory>
            </cac:TaxSubtotal>';
         }

         if($comprobante['tgratuito']>0){
         $gigv=round($comprobante['tgratuito']*0.18,2);
         $xml.='<cac:TaxSubtotal>
               <cbc:TaxableAmount currencyID="'.$comprobante['moneda'].'">'.$comprobante['tgratuito'].'</cbc:TaxableAmount>
               <cbc:TaxAmount currencyID="'.$comprobante['moneda'].'">'.$gigv.'</cbc:TaxAmount>
               <cac:TaxCategory>
                  <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">Z</cbc:ID>
                  <cac:TaxScheme>
                     <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9996</cbc:ID>
                     <cbc:Name>GRA</cbc:Name>
                     <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                  </cac:TaxScheme>
               </cac:TaxCategory>
            </cac:TaxSubtotal>';
         }

         $xml.='</cac:TaxTotal>
         <cac:LegalMonetaryTotal>
            <cbc:LineExtensionAmount currencyID="'.$comprobante['moneda'].'">'.$comprobante['subtotal'].'</cbc:LineExtensionAmount>
            <cbc:TaxInclusiveAmount currencyID="'.$comprobante['moneda'].'">'.$comprobante['total'].'</cbc:TaxInclusiveAmount>
            <cbc:PayableAmount currencyID="'.$comprobante['moneda'].'">'.$comprobante['total'].'</cbc:PayableAmount>
         </cac:LegalMonetaryTotal>';

         $numeracion=1;
         foreach ($detalle as $k=>$v){
         $xml.='<cac:InvoiceLine>
            <cbc:ID>'.$numeracion.'</cbc:ID>
            <cbc:InvoicedQuantity unitCode="'.$v['unidad'].'" unitCodeListID="UN/ECE rec 20" unitCodeListAgencyName="United Nations Economic Commission for Europe">'.$v['cantidad'].'</cbc:InvoicedQuantity>
            <cbc:LineExtensionAmount currencyID="'.$comprobante['moneda'].'">'.$v['total'].'</cbc:LineExtensionAmount>
            <cac:PricingReference>
               <cac:AlternativeConditionPrice>
                  <cbc:PriceAmount currencyID="'.$comprobante['moneda'].'">'.$v['precio'].'</cbc:PriceAmount>
                  <cbc:PriceTypeCode listName="Tipo de Precio" listAgencyName="PE:SUNAT" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo16">'.$v['tprecio'].'</cbc:PriceTypeCode>
               </cac:AlternativeConditionPrice>
            </cac:PricingReference>';
            if($v['descuentos']!=''){
               $descuentos=json_decode($v['descuentos']);
               $xml.='<cac:AllowanceCharge>
                    <cbc:ChargeIndicator>false</cbc:ChargeIndicator>
                    <cbc:AllowanceChargeReasonCode listName="Cargo/descuento" listAgencyName="PE:SUNAT"
                       listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo53">'.$descuentos->codigo.'</cbc:AllowanceChargeReasonCode>
                    <cbc:MultiplierFactorNumeric>'.$descuentos->factor.'</cbc:MultiplierFactorNumeric>
                    <cbc:Amount currencyID="'.$comprobante['moneda'].'">'.$descuentos->monto.'</cbc:Amount>
                    <cbc:BaseAmount currencyID="'.$comprobante['moneda'].'">'.$descuentos->base.'</cbc:BaseAmount>
               </cac:AllowanceCharge>';
            }
            $xml.='<cac:TaxTotal>
               <cbc:TaxAmount currencyID="'.$comprobante['moneda'].'">'.$v['igv'].'</cbc:TaxAmount>
               <cac:TaxSubtotal>
                  <cbc:TaxableAmount currencyID="'.$comprobante['moneda'].'">'.$v['total'].'</cbc:TaxableAmount>
                  <cbc:TaxAmount currencyID="'.$comprobante['moneda'].'">'.$v['igv'].'</cbc:TaxAmount>
                  <cac:TaxCategory>
                     <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">'.$v['codigo_tributo'][0].'</cbc:ID>
                     <cbc:Percent>18</cbc:Percent>
                     <cbc:TaxExemptionReasonCode listAgencyName="PE:SUNAT" listName="Afectacion del IGV" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo07">'.$v['tafectacion'].'</cbc:TaxExemptionReasonCode>
                     <cac:TaxScheme>
                        <cbc:ID schemeID="UN/ECE 5153" schemeName="Codigo de tributos" schemeAgencyName="PE:SUNAT">'.$v['codigo_tributo'][1].'</cbc:ID>
                        <cbc:Name>'.$v['codigo_tributo'][2].'</cbc:Name>
                        <cbc:TaxTypeCode>'.$v['codigo_tributo'][3].'</cbc:TaxTypeCode>
                     </cac:TaxScheme>
                  </cac:TaxCategory>
               </cac:TaxSubtotal>';

            $xml.='</cac:TaxTotal>
            <cac:Item>
               <cbc:Description><![CDATA['.$v['descripcion'].']]></cbc:Description>
               <cac:SellersItemIdentification>
                  <cbc:ID><![CDATA['.$v['idproducto'].']]></cbc:ID>
               </cac:SellersItemIdentification>';
            // $xml.='<cac:CommodityClassification>
            //       <cbc:ItemClassificationCode listID="UNSPSC" listAgencyName="GS1 US" listName="Item Classification">10191509</cbc:ItemClassificationCode>
            //    </cac:CommodityClassification>';
             $xml.='</cac:Item>
            <cac:Price>
               <cbc:PriceAmount currencyID="'.$comprobante['moneda'].'">'.$v['valor'].'</cbc:PriceAmount>
            </cac:Price>
         </cac:InvoiceLine>';
         $numeracion++;
         }
      $xml.='</Invoice>';

      $doc->loadXML($xml);
      $doc->save($nombrexml.'.xml');
   }

   function CrearXMLNotaCredito($nombrexml, $emisor, $cliente, $comprobante, $detalle)
   {
      $doc = new DOMDocument();
      $doc->formatOutput = FALSE;
      $doc->preserveWhiteSpace = TRUE;
      $doc->encoding = 'utf-8';

      $xml = '<?xml version="1.0" encoding="UTF-8"?>
      <CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2">
         <ext:UBLExtensions>
            <ext:UBLExtension>
               <ext:ExtensionContent />
            </ext:UBLExtension>
         </ext:UBLExtensions>
         <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
         <cbc:CustomizationID>2.0</cbc:CustomizationID>
         <cbc:ID>'.$comprobante['serie'].'-'.$comprobante['numero'].'</cbc:ID>
         <cbc:IssueDate>'.$comprobante['femision'].'</cbc:IssueDate>
         <cbc:IssueTime>'.$comprobante['hemision'].'</cbc:IssueTime>
         <cbc:DocumentCurrencyCode>'.$comprobante['moneda'].'</cbc:DocumentCurrencyCode>
         <cac:DiscrepancyResponse>
            <cbc:ReferenceID>'.$comprobante['referencia'].'</cbc:ReferenceID>
            <cbc:ResponseCode>'.$comprobante['tnota'].'</cbc:ResponseCode>
            <cbc:Description>'.$comprobante['motivo'].'</cbc:Description>
         </cac:DiscrepancyResponse>
         <cac:BillingReference>
            <cac:InvoiceDocumentReference>
               <cbc:ID>'.$comprobante['referencia'].'</cbc:ID>
               <cbc:DocumentTypeCode>'.$comprobante['treferencia'].'</cbc:DocumentTypeCode>
            </cac:InvoiceDocumentReference>
         </cac:BillingReference>
         <cac:Signature>
            <cbc:ID>'.$emisor['ruc'].'</cbc:ID>
            <cbc:Note><![CDATA['.$emisor['ncomercial'].']]></cbc:Note>
            <cac:SignatoryParty>
               <cac:PartyIdentification>
                  <cbc:ID>'.$emisor['ruc'].'</cbc:ID>
               </cac:PartyIdentification>
               <cac:PartyName>
                  <cbc:Name><![CDATA['.$emisor['nombres'].']]></cbc:Name>
               </cac:PartyName>
            </cac:SignatoryParty>
            <cac:DigitalSignatureAttachment>
               <cac:ExternalReference>
                  <cbc:URI>#SIGN-EMPRESA</cbc:URI>
               </cac:ExternalReference>
            </cac:DigitalSignatureAttachment>
         </cac:Signature>
         <cac:AccountingSupplierParty>
            <cac:Party>
               <cac:PartyIdentification>
                  <cbc:ID schemeID="6">'.$emisor['ruc'].'</cbc:ID>
               </cac:PartyIdentification>
               <cac:PartyName>
                  <cbc:Name><![CDATA['.$emisor['ncomercial'].']]></cbc:Name>
               </cac:PartyName>
               <cac:PartyLegalEntity>
                  <cbc:RegistrationName><![CDATA['.$emisor['nombres'].']]></cbc:RegistrationName>
                  <cac:RegistrationAddress>
                     <cbc:ID>'.$emisor['distrito'].'</cbc:ID>
                     <cbc:AddressTypeCode>0000</cbc:AddressTypeCode>
                     <cbc:CitySubdivisionName>NONE</cbc:CitySubdivisionName>
                     <cbc:CityName>'.$emisor['nprovincia'].'</cbc:CityName>
                     <cbc:CountrySubentity>'.$emisor['ndepartamento'].'</cbc:CountrySubentity>
                     <cbc:District>'.$emisor['ndistrito'].'</cbc:District>
                     <cac:AddressLine>
                        <cbc:Line><![CDATA['.$emisor['direccion'].']]></cbc:Line>
                     </cac:AddressLine>
                     <cac:Country>
                        <cbc:IdentificationCode>PE</cbc:IdentificationCode>
                     </cac:Country>
                  </cac:RegistrationAddress>
               </cac:PartyLegalEntity>
            </cac:Party>
         </cac:AccountingSupplierParty>
         <cac:AccountingCustomerParty>
         <cac:Party>
            <cac:PartyIdentification>
               <cbc:ID schemeID="'.$cliente->tdocumento.'">'.$cliente->documento.'</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyLegalEntity>
               <cbc:RegistrationName><![CDATA['.$cliente->nombres.']]></cbc:RegistrationName>
               <cac:RegistrationAddress>
                  <cac:AddressLine>
                     <cbc:Line><![CDATA['.$cliente->direccion.']]></cbc:Line>
                  </cac:AddressLine>
                  <cac:Country>
                     <cbc:IdentificationCode>'.$cliente->idpais.'</cbc:IdentificationCode>
                  </cac:Country>
               </cac:RegistrationAddress>
            </cac:PartyLegalEntity>
         </cac:Party>
      </cac:AccountingCustomerParty>
      <cac:TaxTotal>
         <cbc:TaxAmount currencyID="'.$comprobante['moneda'].'">'.$comprobante['tigv'].'</cbc:TaxAmount>';
         if($comprobante['tgravado']>0){
         $xml.='<cac:TaxSubtotal>
            <cbc:TaxableAmount currencyID="'.$comprobante['moneda'].'">'.$comprobante['tgravado'].'</cbc:TaxableAmount>
            <cbc:TaxAmount currencyID="'.$comprobante['moneda'].'">'.$comprobante['tigv'].'</cbc:TaxAmount>
            <cac:TaxCategory>
               <cac:TaxScheme>
                  <cbc:ID>1000</cbc:ID>
                  <cbc:Name>IGV</cbc:Name>
                  <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
               </cac:TaxScheme>
            </cac:TaxCategory>
         </cac:TaxSubtotal>';
         }

         if($comprobante['texonerado']>0){
            $xml.='<cac:TaxSubtotal>
               <cbc:TaxableAmount currencyID="'.$comprobante['moneda'].'">'.$comprobante['texonerado'].'</cbc:TaxableAmount>
               <cbc:TaxAmount currencyID="'.$comprobante['moneda'].'">0.00</cbc:TaxAmount>
               <cac:TaxCategory>
                  <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
                  <cac:TaxScheme>
                     <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9997</cbc:ID>
                     <cbc:Name>EXO</cbc:Name>
                     <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                  </cac:TaxScheme>
               </cac:TaxCategory>
            </cac:TaxSubtotal>';
         }

         if($comprobante['tinafecto']>0){
            $xml.='<cac:TaxSubtotal>
               <cbc:TaxableAmount currencyID="'.$comprobante['moneda'].'">'.$comprobante['tinafecto'].'</cbc:TaxableAmount>
               <cbc:TaxAmount currencyID="'.$comprobante['moneda'].'">0.00</cbc:TaxAmount>
               <cac:TaxCategory>
                  <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
                  <cac:TaxScheme>
                     <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9998</cbc:ID>
                     <cbc:Name>INA</cbc:Name>
                     <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                  </cac:TaxScheme>
               </cac:TaxCategory>
            </cac:TaxSubtotal>';
         }

         if($comprobante['tgratuito']>0){
         $gigv=round($comprobante['tgratuito']*0.18,2);
         $xml.='<cac:TaxSubtotal>
               <cbc:TaxableAmount currencyID="'.$comprobante['moneda'].'">'.$comprobante['tgratuito'].'</cbc:TaxableAmount>
               <cbc:TaxAmount currencyID="'.$comprobante['moneda'].'">'.$gigv.'</cbc:TaxAmount>
               <cac:TaxCategory>
                  <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">Z</cbc:ID>
                  <cac:TaxScheme>
                     <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9996</cbc:ID>
                     <cbc:Name>GRA</cbc:Name>
                     <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                  </cac:TaxScheme>
               </cac:TaxCategory>
            </cac:TaxSubtotal>';
         }

         $xml.='</cac:TaxTotal>
         <cac:LegalMonetaryTotal>
            <cbc:PayableAmount currencyID="'.$comprobante['moneda'].'">'.$comprobante['total'].'</cbc:PayableAmount>
         </cac:LegalMonetaryTotal>';
         $numeracion=1;
         foreach($detalle as $v){
            $xml.='<cac:CreditNoteLine>
               <cbc:ID>'.$numeracion.'</cbc:ID>
               <cbc:CreditedQuantity unitCode="'.$v['unidad'].'">'.$v['cantidad'].'</cbc:CreditedQuantity>
               <cbc:LineExtensionAmount currencyID="'.$comprobante['moneda'].'">'.$v['total'].'</cbc:LineExtensionAmount>
               <cac:PricingReference>
                  <cac:AlternativeConditionPrice>
                     <cbc:PriceAmount currencyID="'.$comprobante['moneda'].'">'.$v['precio'].'</cbc:PriceAmount>
                     <cbc:PriceTypeCode>'.$v['tprecio'].'</cbc:PriceTypeCode>
                  </cac:AlternativeConditionPrice>
               </cac:PricingReference>
               <cac:TaxTotal>';
               if ($v['codigo_tributo'][1]=='9996') {
                  $xml.='<cbc:TaxAmount currencyID="'.$comprobante['moneda'].'">0</cbc:TaxAmount>';
               } else {
                  $xml.='<cbc:TaxAmount currencyID="'.$comprobante['moneda'].'">'.$v['igv'].'</cbc:TaxAmount>';
               }
               $xml.='<cac:TaxSubtotal>
                     <cbc:TaxableAmount currencyID="'.$comprobante['moneda'].'">'.$v['total'].'</cbc:TaxableAmount>
                     <cbc:TaxAmount currencyID="'.$comprobante['moneda'].'">'.$v['igv'].'</cbc:TaxAmount>
                     <cac:TaxCategory>
                           <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">'.$v['codigo_tributo'][0].'</cbc:ID>
                           <cbc:Percent>18</cbc:Percent>
                        <cbc:TaxExemptionReasonCode listAgencyName="PE:SUNAT" listName="Afectacion del IGV" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo07">'.$v['tafectacion'].'</cbc:TaxExemptionReasonCode>
                        <cac:TaxScheme>
                           <cbc:ID schemeID="UN/ECE 5153" schemeName="Codigo de tributos" schemeAgencyName="PE:SUNAT">'.$v['codigo_tributo'][1].'</cbc:ID>
                           <cbc:Name>'.$v['codigo_tributo'][2].'</cbc:Name>
                           <cbc:TaxTypeCode>'.$v['codigo_tributo'][3].'</cbc:TaxTypeCode>
                        </cac:TaxScheme>
                     </cac:TaxCategory>
                  </cac:TaxSubtotal>
               </cac:TaxTotal>
               <cac:Item>
                  <cbc:Description><![CDATA['.$v['descripcion'].']]></cbc:Description>
                  <cac:SellersItemIdentification>
                     <cbc:ID>'.$v['idproducto'].'</cbc:ID>
                  </cac:SellersItemIdentification>
               </cac:Item>
               <cac:Price>
                  <cbc:PriceAmount currencyID="'.$comprobante['moneda'].'">'.$v['valor'].'</cbc:PriceAmount>
               </cac:Price>
            </cac:CreditNoteLine>';
            $numeracion++;
         }
      $xml.='</CreditNote>';

      $doc->loadXML($xml);
      $doc->save($nombrexml.'.xml');
   }

   function CrearXMLResumenDocumentos($nombrexml, $emisor, $cabecera, $detalle)
   {
      $doc = new DOMDocument();
      $doc->formatOutput = FALSE;
      $doc->preserveWhiteSpace = TRUE;
      $doc->encoding = 'utf-8';   

      $xml = '<?xml version="1.0" encoding="UTF-8"?>
      <SummaryDocuments xmlns="urn:sunat:names:specification:ubl:peru:schema:xsd:SummaryDocuments-1" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1" xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2">
              <ext:UBLExtensions>
                  <ext:UBLExtension>
                      <ext:ExtensionContent />
                  </ext:UBLExtension>
              </ext:UBLExtensions>
              <cbc:UBLVersionID>2.0</cbc:UBLVersionID>
              <cbc:CustomizationID>1.1</cbc:CustomizationID>
              <cbc:ID>'.$cabecera['identificador'].'</cbc:ID>
              <cbc:ReferenceDate>'.$cabecera['fdocumento'].'</cbc:ReferenceDate>
              <cbc:IssueDate>'.$cabecera['femision'].'</cbc:IssueDate>
              <cac:Signature>
                  <cbc:ID>'.$cabecera['identificador'].'</cbc:ID>
                  <cac:SignatoryParty>
                      <cac:PartyIdentification>
                          <cbc:ID>'.$emisor->ruc.'</cbc:ID>
                      </cac:PartyIdentification>
                      <cac:PartyName>
                          <cbc:Name><![CDATA['.$emisor->nombres.']]></cbc:Name>
                      </cac:PartyName>
                  </cac:SignatoryParty>
                  <cac:DigitalSignatureAttachment>
                      <cac:ExternalReference>
                          <cbc:URI>'.$cabecera['identificador'].'</cbc:URI>
                      </cac:ExternalReference>
                  </cac:DigitalSignatureAttachment>
              </cac:Signature>
              <cac:AccountingSupplierParty>
                  <cbc:CustomerAssignedAccountID>'.$emisor->ruc.'</cbc:CustomerAssignedAccountID>
                  <cbc:AdditionalAccountID>6</cbc:AdditionalAccountID>
                  <cac:Party>
                      <cac:PartyLegalEntity>
                          <cbc:RegistrationName><![CDATA['.$emisor->nombres.']]></cbc:RegistrationName>
                      </cac:PartyLegalEntity>
                  </cac:Party>
              </cac:AccountingSupplierParty>';

              $numeracion=1;
              foreach ($detalle as $k => $v) {
                 $xml.='<sac:SummaryDocumentsLine>
                     <cbc:LineID>'.$numeracion.'</cbc:LineID>
                     <cbc:DocumentTypeCode>'.$v['tipo_comprobante'].'</cbc:DocumentTypeCode>
                     <cbc:ID>'.$v['serie'].'-'.$v['numero'].'</cbc:ID>
                     <cac:AccountingCustomerParty>
                        <cbc:CustomerAssignedAccountID>'.$v['documento'].'</cbc:CustomerAssignedAccountID>
                        <cbc:AdditionalAccountID>'.$v['tipo_documento'].'</cbc:AdditionalAccountID>
                     </cac:AccountingCustomerParty>';
                     if($v['tipo_comprobante']=='07' || $v['tipo_comprobante']=='08'){
                     $xml.='<cac:BillingReference>
                        <cac:InvoiceDocumentReference>
                           <cbc:ID>'.$v['afectado'].'</cbc:ID>
                           <cbc:DocumentTypeCode>'.$v['tipo_afectado'].'</cbc:DocumentTypeCode>
                        </cac:InvoiceDocumentReference>
                     </cac:BillingReference>';
                     }
                     $xml.='<cac:Status>
                        <cbc:ConditionCode>'.$v['condicion'].'</cbc:ConditionCode>
                     </cac:Status>
                     <sac:TotalAmount currencyID="'.$v['moneda'].'">'.$v['total'].'</sac:TotalAmount>';
                        if($v['total_gravado']>0){
                           $xml.='<sac:BillingPayment>
                           <cbc:PaidAmount currencyID="'.$v['moneda'].'">'.$v['total_gravado'].'</cbc:PaidAmount>
                               <cbc:InstructionID>01</cbc:InstructionID>
                           </sac:BillingPayment>';
                        }
                        if($v['total_exonerado']>0){
                           $xml.='<sac:BillingPayment>
                           <cbc:PaidAmount currencyID="'.$v['moneda'].'">'.$v['total_exonerado'].'</cbc:PaidAmount>
                               <cbc:InstructionID>02</cbc:InstructionID>
                           </sac:BillingPayment>';
                        }
                        if($v['total_inafecto']>0){
                           $xml.='<sac:BillingPayment>
                           <cbc:PaidAmount currencyID="'.$v['moneda'].'">'.$v['total_inafecto'].'</cbc:PaidAmount>
                               <cbc:InstructionID>03</cbc:InstructionID>
                           </sac:BillingPayment>';
                        }
                        if($v['total_gratuito']>0){
                           $xml.='<sac:BillingPayment>
                           <cbc:PaidAmount currencyID="'.$v['moneda'].'">'.$v['total_gratuito'].'</cbc:PaidAmount>
                               <cbc:InstructionID>05</cbc:InstructionID>
                           </sac:BillingPayment>';
                        }
                     $xml.='<cac:TaxTotal>
                         <cbc:TaxAmount currencyID="'.$v['moneda'].'">'.$v['total_impuesto'].'</cbc:TaxAmount>';
                         $xml.='<cac:TaxSubtotal>
                             <cbc:TaxAmount currencyID="'.$v['moneda'].'">'.$v['total_igv'].'</cbc:TaxAmount>
                             <cac:TaxCategory>
                                 <cac:TaxScheme>
                                     <cbc:ID>1000</cbc:ID>
                                     <cbc:Name>IGV</cbc:Name>
                                     <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                                 </cac:TaxScheme>
                             </cac:TaxCategory>
                         </cac:TaxSubtotal>';

                     $xml.='</cac:TaxTotal>
                 </sac:SummaryDocumentsLine>';
                 $numeracion++;
              }
      $xml.='</SummaryDocuments>';

      $doc->loadXML($xml);
      $doc->save($nombrexml.'.xml');
   }

   function CrearXmlBajaDocumentos($nombrexml, $emisor, $cabecera, $detalle)
   {
      $doc = new DOMDocument();
      $doc->formatOutput = FALSE;
      $doc->preserveWhiteSpace = TRUE;
      $doc->encoding = 'utf-8';

      $xml = '<?xml version="1.0" encoding="UTF-8"?>
      <VoidedDocuments xmlns="urn:sunat:names:specification:ubl:peru:schema:xsd:VoidedDocuments-1" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
              <ext:UBLExtensions>
                  <ext:UBLExtension>
                      <ext:ExtensionContent />
                  </ext:UBLExtension>
              </ext:UBLExtensions>
              <cbc:UBLVersionID>2.0</cbc:UBLVersionID>
              <cbc:CustomizationID>1.0</cbc:CustomizationID>
              <cbc:ID>'.$cabecera['identificador'].'</cbc:ID>
              <cbc:ReferenceDate>'.$cabecera['fdocumento'].'</cbc:ReferenceDate>
              <cbc:IssueDate>'.$cabecera['femision'].'</cbc:IssueDate>
              <cac:Signature>
                  <cbc:ID>'.$cabecera['identificador'].'</cbc:ID>
                  <cac:SignatoryParty>
                      <cac:PartyIdentification>
                          <cbc:ID>'.$emisor->ruc.'</cbc:ID>
                      </cac:PartyIdentification>
                      <cac:PartyName>
                          <cbc:Name><![CDATA['.$emisor->nombres.']]></cbc:Name>
                      </cac:PartyName>
                  </cac:SignatoryParty>
                  <cac:DigitalSignatureAttachment>
                      <cac:ExternalReference>
                          <cbc:URI>'.$cabecera['identificador'].'</cbc:URI>
                      </cac:ExternalReference>
                  </cac:DigitalSignatureAttachment>
              </cac:Signature>
              <cac:AccountingSupplierParty>
                  <cbc:CustomerAssignedAccountID>'.$emisor->ruc.'</cbc:CustomerAssignedAccountID>
                  <cbc:AdditionalAccountID>6</cbc:AdditionalAccountID>
                  <cac:Party>
                      <cac:PartyLegalEntity>
                          <cbc:RegistrationName><![CDATA['.$emisor->nombres.']]></cbc:RegistrationName>
                      </cac:PartyLegalEntity>
                  </cac:Party>
              </cac:AccountingSupplierParty>';

              $numeracion=1;
              foreach ($detalle as $k => $v) {
                 $xml.='<sac:VoidedDocumentsLine>
                     <cbc:LineID>'.$numeracion.'</cbc:LineID>
                     <cbc:DocumentTypeCode>'.$v['tcomprobante'].'</cbc:DocumentTypeCode>
                     <sac:DocumentSerialID>'.$v['serie'].'</sac:DocumentSerialID>
                     <sac:DocumentNumberID>'.$v['numero'].'</sac:DocumentNumberID>
                     <sac:VoidReasonDescription><![CDATA['.$v['motivo'].']]></sac:VoidReasonDescription>
                 </sac:VoidedDocumentsLine>';
                 $numeracion++;
              }
      $xml.='</VoidedDocuments>';

      $doc->loadXML($xml);
      $doc->save($nombrexml.'.xml');
   }

   function CrearXMLGuia($nombrexml, $emisor, $cliente, $cabecera, $items)
   {
      $doc = new DOMDocument();
      $doc->formatOutput = FALSE;
      $doc->preserveWhiteSpace = TRUE;
      $doc->encoding = 'utf-8';

      $xml = '<?xml version="1.0" encoding="utf-8"?>
      <DespatchAdvice xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" xmlns:ccts="urn:un:unece:uncefact:documentation:2" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1" xmlns="urn:oasis:names:specification:ubl:schema:xsd:DespatchAdvice-2">
          <ext:UBLExtensions>
              <ext:UBLExtension>
                  <ext:ExtensionContent/>
              </ext:UBLExtension>
          </ext:UBLExtensions>
          <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
          <cbc:CustomizationID>2.0</cbc:CustomizationID>
          <cbc:ID>'.$cabecera['serie'].'-'.$cabecera['numero'].'</cbc:ID>
          <cbc:IssueDate>'.$cabecera['femision'].'</cbc:IssueDate>
          <cbc:IssueTime>'.$cabecera['hemision'].'</cbc:IssueTime>
          <cbc:DespatchAdviceTypeCode listAgencyName="PE:SUNAT" listName="Tipo de Documento" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01">'.$cabecera['tcomprobante'].'</cbc:DespatchAdviceTypeCode>
          <cbc:Note><![CDATA['.$cabecera['observaciones'].']]></cbc:Note>
          <cac:Signature>
            <cbc:ID>'.$cabecera['serie'].'-'.$cabecera['numero'].'</cbc:ID>
            <cac:SignatoryParty>
               <cac:PartyIdentification>
                  <cbc:ID>'.$emisor['ruc'].'</cbc:ID>
               </cac:PartyIdentification>
               <cac:PartyName>
                  <cbc:Name><![CDATA['.$emisor['nombres'].']]></cbc:Name>
               </cac:PartyName>
            </cac:SignatoryParty>
            <cac:DigitalSignatureAttachment>
               <cac:ExternalReference>
                  <cbc:URI>#SignatureSP</cbc:URI>
               </cac:ExternalReference>
            </cac:DigitalSignatureAttachment>
          </cac:Signature>
          <cac:DespatchSupplierParty>
            <cac:Party>
               <cac:PartyIdentification>
                 <cbc:ID schemeID="6" schemeName="Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$emisor['ruc'].'</cbc:ID>
               </cac:PartyIdentification>
               <cac:PartyLegalEntity>
                 <cbc:RegistrationName><![CDATA['.$emisor['nombres'].']]></cbc:RegistrationName>
               </cac:PartyLegalEntity>
             </cac:Party>
          </cac:DespatchSupplierParty>
          <cac:DeliveryCustomerParty>
             <cac:Party>
               <cac:PartyIdentification>
                 <cbc:ID schemeID="'.$cliente->tdocumento.'" schemeName="Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$cliente->documento.'</cbc:ID>
               </cac:PartyIdentification>
               <cac:PartyLegalEntity>
                 <cbc:RegistrationName><![CDATA['.$cliente->nombres.']]></cbc:RegistrationName>
               </cac:PartyLegalEntity>
             </cac:Party>
          </cac:DeliveryCustomerParty>
          <cac:Shipment>
            <cbc:ID>1</cbc:ID>
            <cbc:HandlingCode listAgencyName="PE:SUNAT" listName="Motivo de traslado" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo20">'.$cabecera['codigo_motivo_traslado'].'</cbc:HandlingCode>
            <cbc:HandlingInstructions>'.$cabecera['motivo_traslado'].'</cbc:HandlingInstructions>
            <cbc:GrossWeightMeasure unitCode="'.$cabecera['unidad_peso'].'">'.$cabecera['peso'].'</cbc:GrossWeightMeasure>
            <cbc:TotalTransportHandlingUnitQuantity>'.$cabecera['paquetes'].'</cbc:TotalTransportHandlingUnitQuantity>';
            if ($cabecera['m1l']==1) {
            $xml.='<cbc:SpecialInstructions>SUNAT_Envio_IndicadorTrasladoVehiculoM1L</cbc:SpecialInstructions>';
            }
            $xml.='<cac:ShipmentStage>
                <cbc:TransportModeCode listName="Modalidad de traslado" listAgencyName="PE:SUNAT" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo18">'.$cabecera['modo_transporte'].'</cbc:TransportModeCode>
                <cac:TransitPeriod>
                    <cbc:StartDate>'.$cabecera['fecha_envio'].'</cbc:StartDate>
                </cac:TransitPeriod>';

              if($cabecera['modo_transporte']=='01' && $cabecera['transporte_nro_doc']>0){ //TRANSPORTE PUBLICO
               $xml.='<cac:CarrierParty>
                   <cac:PartyIdentification>
                       <cbc:ID schemeID="'.$cabecera['transporte_tipo_doc'].'" schemeName="Documento de Identidad" schemeAgencyName="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$cabecera['transporte_nro_doc'].'</cbc:ID>
                   </cac:PartyIdentification>
                   <cac:PartyLegalEntity>
                       <cbc:RegistrationName>'.$cabecera['transporte_nombres'].'</cbc:RegistrationName>
                   </cac:PartyLegalEntity>
               </cac:CarrierParty>';
              }

              if($cabecera['modo_transporte']=='02' && $cabecera['transporte_nro_doc']>0){ //TRANSPORTE PRIVADO
               $xml.='<cac:DriverPerson>
                  <cbc:ID schemeID="'.$cabecera['transporte_tipo_doc'].'" schemeName="Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$cabecera['transporte_nro_doc'].'</cbc:ID>
                  <cbc:FirstName>'.$cabecera['transporte_nombres'].'</cbc:FirstName>
                  <cbc:FamilyName>-</cbc:FamilyName>
                  <cbc:JobTitle>Principal</cbc:JobTitle>
                  <cac:IdentityDocumentReference>
                   <cbc:ID>'.$cabecera['licencia'].'</cbc:ID>
                  </cac:IdentityDocumentReference>
               </cac:DriverPerson>';
              }
               $xml.='</cac:ShipmentStage>';
               $xml.='<cac:Delivery>
                  <cac:DeliveryAddress>
                    <cbc:ID schemeAgencyName="PE:INEI" schemeName="Ubigeos">'.$cabecera['destino_ubigeo'].'</cbc:ID>';
                    if ($cabecera['codigo_motivo_traslado']=='04') {
                     $xml.='<cbc:AddressTypeCode listAgencyName="PE:SUNAT" listID="'.$emisor['ruc'].'" listName="Establecimientos anexos">'.$cabecera['destino_codigo'].'</cbc:AddressTypeCode>';
                    }
                    $xml.='<cac:AddressLine>
                      <cbc:Line><![CDATA['.$cabecera['destino_direccion'].']]></cbc:Line>
                    </cac:AddressLine>
                  </cac:DeliveryAddress>
                  <cac:Despatch>
                     <cac:DespatchAddress>
                         <cbc:ID schemeAgencyName="PE:INEI" schemeName="Ubigeos">'.$cabecera['partida_ubigeo'].'</cbc:ID>';
                         if ($cabecera['codigo_motivo_traslado']=='04') {
                           $xml.='<cbc:AddressTypeCode listAgencyName="PE:SUNAT" listID="'.$emisor['ruc'].'" listName="Establecimientos anexos">'.$cabecera['partida_codigo'].'</cbc:AddressTypeCode>';
                         }
                         $xml.='<cac:AddressLine>
                           <cbc:Line><![CDATA['.$cabecera['partida_direccion'].']]></cbc:Line>
                         </cac:AddressLine>
                     </cac:DespatchAddress>
                  </cac:Despatch>
               </cac:Delivery>';

               if($cabecera['modo_transporte']=='02' && $cabecera['m1l']==0){
                $xml.='<cac:TransportHandlingUnit>
                  <cac:TransportEquipment>
                    <cbc:ID>'.$cabecera['placa'].'</cbc:ID>
                  </cac:TransportEquipment>
                </cac:TransportHandlingUnit>';
               }
              $xml.='</cac:Shipment>';

              $numeracion=1;
              foreach($items as $v){
              $xml.='<cac:DespatchLine>
                  <cbc:ID>'.$numeracion.'</cbc:ID>
                  <cbc:DeliveredQuantity unitCodeListID="UN/ECE rec 20" unitCodeListAgencyName="United Nations Economic Commission for Europe" unitCode="'.$v['unidad'].'">'.$v['cantidad'].'</cbc:DeliveredQuantity>
                  <cac:OrderLineReference>
                      <cbc:LineID>'.$numeracion.'</cbc:LineID>
                  </cac:OrderLineReference>
                  <cac:Item>
                      <cbc:Description><![CDATA['.$v['descripcion'].']]></cbc:Description>
                      <cac:SellersItemIdentification>
                          <cbc:ID>'.$v['idproducto'].'</cbc:ID>
                      </cac:SellersItemIdentification>
                  </cac:Item>
              </cac:DespatchLine>';
              $numeracion++;
              }
      $xml.='</DespatchAdvice>';

      $doc->loadXML($xml);
      $doc->save($nombrexml.'.xml');
   }


}
?>
