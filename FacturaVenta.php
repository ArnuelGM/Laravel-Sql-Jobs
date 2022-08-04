<?php

use GuzzleHttp\Exception\RequestException;

require_once 'Factura.php';
require_once (__DIR__ . '/../../../librerias/guzzle/guzzle.php');

session_start();

if( empty( $_GET['numero_invoice'] ) ) {
  echo "Imposible imprimir la factura. No se proporcionó un número de factura.";
  exit;
}

$api = $_SESSION['API_IFACTURA_URL'];
$numero_invoice = $_GET['numero_invoice'];
$groupDetail = '1';

$client = new GuzzleHttp\Client;

// Obtener Factura
try {
  $url = $api . "/invoices/$numero_invoice?groupDetail=$groupDetail";
  $response = $client->request('get', $url);
  $invoice = $response->getBody()->getContents();
  // $invoice = ((Object) json_decode($response->getBody()->getContents()))->data;
  $invoice = new Factura($invoice);
}
catch(Exception $e) {

  echo "Error al obtener la factura.";
  echo '<br>';

  if( $e instanceof RequestException ) {
    echo $e->getResponse()->getBody()->getContents();
  }
  else {
    echo $e->getMessage();
  }

  exit;
}

// echo "<pre>". json_encode($invoice, JSON_PRETTY_PRINT) ."</pre>";

?>
<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>FACTURA DE VENTA</title>
   <style><?php echo file_get_contents('css/facturaVenta.css') ?></style>
</head>

<body class="facturaVenta">
   <div class="content">
      <div class="header">
         <table style="table-layout: fixed;" width="100%">
            <tr>
               <td class="infoEmpresa">
                  <div class="infoEmpresa_divTxt">
                     <div class="infoEmpresa_divImg">
                        <img class="infoEmpresa_img"
                           src="data:image/png;base64,<?php echo base64_encode(file_get_contents('../../../imagenes/recurso/logo_emp.png')); ?>"
                           alt="tu logo aqui" />
                     </div>
                     <p class="infoEmpresa_txt"><?php echo $invoice->emisor->nombre; ?></p>
                     <p class="infoEmpresa_txt">NIT <?php echo $invoice->emisor->nit . '-' . $invoice->emisor->digito; ?></p>
                     <p class="infoEmpresa_txt">Direcci&oacute;n: <?php echo $invoice->emisor->direccion; ?></p>
                     <p class="infoEmpresa_txt">PBX: <?php echo $invoice->emisor->telefono; ?> </p>
                     <p class="infoEmpresa_txt"><?php echo $invoice->emisor->ciudad; ?></p>
                  </div>
                  <p class="infoEmpresa_txt ">Somos Responsables del Impuesto Sobre las Ventas IVA.</p>
               </td>
               <td class="infoFactura">
                  <div class="infoFactura_data_divTitle">
                     <h3 class="infoFactura_data_title"> FACTURA ELECTR&Oacute;NICA DE VENTA : <span
                           class="infoFactura_data_nro "><?php echo $invoice->numero_invoice; ?></span>
                     </h3>
                  </div>
                  <div class="infoFactura_data_info">
                     <table style="width:100%">
                        <tr>
                           <td class="infoFactura_data_info_date">
                              <div>
                                 <p class="txtBlue">FECHA DE GENERACI&Oacute;N</p>
                                 <span><?php echo date('Y-m-d H:i:s', strtotime($invoice->created_at)); ?></span>
                                 <p class="txtBlue">FECHA DE EXPEDICI&Oacute;N</p>
                                 <span><?php echo date('Y-m-d H:i:s', strtotime($invoice->fecha)); ?></span>
                                 <p class="txtBlue">CUFE</p>
                              </div>
                           </td>
                           <td class="infoFactura_data_info_divQr">
                              <?php if( $invoice->facturaElectronica ): ?>
                                 <img
                                    class="infoFactura_img"
                                    src="data:image/png;base64,<?php echo $invoice->facturaElectronica->qrimage; ?>"
                                    alt="Codigo QR"
                                 />
                              <?php endif; ?>
                           </td>
                        </tr>

                     </table>
                     <?php if( $invoice->facturaElectronica ): ?>
                     <p class="cufeTxt">
                        <?php echo $invoice->facturaElectronica->cufe; ?>
                     </p>
                     <?php endif; ?>
                  </div>

                  <div class="infoFactura_divTxt">
                     <p class="infoFactura_txt "> ESTA FACTURA DE VENTA SE ASIMILA EN TODOS SUS EFECTOS A UNA LETRA DE
                        CAMBIO SEG&Uacute;N ART 774 DEL C&Oacute;DIGO DE COMERCIO </p>
                     <p class="infoFactura_txt "> La factura se considera aceptada por el cliente, si dentro de los (10)
                        d&iacute;as calendario siguiente a su recepci&oacute;n, no reclamar&eacute; en contra de su contenido </p>
                  </div>
               </td>
            </tr>
         </table>
      </div>

      <div class="sectionOne">
         <table style="table-layout: fixed;" width="100%">
            <tr>
               <td>
                  <div class="sectionOne_tableOne">
		  <h4>Favor Cancelar esta Factura en la CUENTA DE AHORROS 95900000358  del banco Bancolombia a favor de <?php echo $invoice->emisor->nombre; ?>.</h4>
                  </div>
               </td>
               <td>
                  <div class="sectionOne_tableTwo">
                     <h4>
                     </h4>
                  </div>
               </td>
               <td>
               <div class="sectionOne_tableThree">
                     <div>
                        <p class="txt-center">CONDICI&Oacute;N DE PAGO</p>
                     </div>
                     <table style="width:100%">
                        <tr>
                           <td class="txt-center">CR&Eacute;DITO</td>
                           <td class="txt-center">CONTADO</td>
                        </tr>
                        <tr>
                           <td class="txt-center">
                              <?php echo $invoice->plazo_pago > 0 ? 'X' : '' ?>
                           </td>
                           <td class="txt-center">
                              <?php echo $invoice->plazo_pago <= 0 ? 'X' : '' ?>
                           </td>
                        </tr>
                     </table>
                  </div>
               </td>
               <td>
                  <div class="sectionOne_tableFour">
                     <div>
                        <p class="txt-center">FECHA DE VENCIMIENTO</p>
                     </div>
                     <table style="width:100%">
                        <tr>
                           <td class="txt-center">DD-MM-AAAA</td>
                        </tr>
                        <tr>
                           <td class="txt-center"> <?php echo date('d-m-Y', strtotime($invoice->fecha_vigencia)) ?> </td>
                        </tr>
                     </table>
                  </div>
               </td>
            </tr>
         </table>
      </div>
      <div class="sectionTwo">
         <table style="width: 100%;">
            <tr>
               <td class="tableTwo" style="width: 50%">
                  <table class="sectionTwo__tableOne" style="width: auto;">
                     <tbody>
                        <tr>
                           <th>CLIENTE</th>
                           <td><?php echo $invoice->empresa->nombre; ?></td>
                        </tr>
                        <tr>
                           <th>TP DE DOCUMENTO:</th>
                           <td>NIT</td>
                        </tr>
                        <tr>
                           <th>DOC DE IDENTIFICACI&Oacute;N:</th>
                           <td><?php echo $invoice->empresa->nit . '-' . $invoice->empresa->digito ?></td>
                        </tr>
                     </tbody>
                  </table>
               </td>
               <td class="tableTwo" style="width: 50%">
                  <table class="sectionTwo__tableTwo" style="width: auto;">
                     <tbody>
                        <tr>
                           <th>DIRECCI&Oacute;N:</th>
                           <td><?php echo $invoice->empresa->direccion; ?></td>
                        </tr>
                        <tr>
                           <th>TEL&Eacute;FONO:</th>
                           <td><?php echo $invoice->empresa->telefono; ?></td>
                        </tr>
                        <tr>
                           <th>CIUDAD:</th>
                           <td><?php echo $invoice->empresa->ciudad; ?></td>
                        </tr>
                     </tbody>
                  </table>
               </td>
            </tr>
         </table>
      </div>
      <div class="sectionThree">
         <table class="tableFactura">
            <thead class="tableFactura_head">
               <tr>
                  <th class="tableFactura_thLeft ">CONCEPTO</th>
                  <th class="tableFactura_thRight ">C&Oacute;DIGO</th>
                  <th class="tableFactura_thRight ">CANTIDAD</th>
                  <th class="tableFactura_thRight ">VALOR</th>
               </tr>
            </thead>
            <tbody>
               <?php foreach ($invoice->detail as $item): ?>
               <tr>
                  <td class="tableFactura_tdLeft "> 
                  <?php echo $item->procedimiento_nombre; ?>
                  </td>
                  <td class="tableFactura_tdRight "><?php echo $item->procedimiento_codigo; ?></td>
                  <td class="tableFactura_tdRight "><?php echo $item->cantidad; ?></td>
                  <td class="tableFactura_tdRight " width="120">$ <?php echo number_format($item->valor_total, 2); ?></td>
               </tr>
               <?php endforeach; ?>
               <tr>
                  <td class="tableFactura_tdRight-total tableFacturaInfo_txt-bold ">Total Items : <span><?php echo count($invoice->detail); ?></span>
                  </td>
               </tr>
               <tr>
                  <td rowspan="6">
                     <b>SON :</b> <?php echo $invoice->valor_letras; ?>
                  </td>
               </tr>
               <tr>
                  <td colspan="2" class="tdRight tdRight-bt tdRight-br ">SUBTOTAL:</td>
                  <td class="tdRight tdRight-bt tdRight-br ">$ <?php echo number_format($invoice->valor_neto, 2); ?></td>
               </tr>
               <tr>
                  <td colspan="2" class="tdRight tdRight-br ">ANTICIPO:</td>
                  <td class="tdRight tdRight-br ">$ <?php echo number_format($invoice->valor_anticipo, 2); ?></td>
               </tr>
               <tr>
                  <td colspan="2" class="tdRight tdRight-br ">DESCUENTOS:</td>
                  <td class="tdRight tdRight-br ">$ <?php echo number_format($invoice->valor_descuentos, 2); ?></td>
               </tr>
               <tr>
                  <td colspan="2" class="tdRight tdRight-br ">IVA:</td>
                  <td class="tdRight tdRight-br ">$ <?php echo number_format($invoice->valor_iva, 2); ?></td>
               </tr>
               <tr>
                  <td colspan="2" class="tdRight tdRight-fs tdRight-bb tdRight-br widthTd"> VALOR NETO A PAGAR: </td>
                  <td class="tdRight tdRight-fs tdRight-bb tdRight-br widthTd"> $<?php echo number_format($invoice->valor_total, 2); ?></td>
               </tr>
            </tbody>
         </table>
         <div class="tableFacturaInfo">
            <div class="tableFacturaInfo_divTxt ">
               <p class="tableFacturaInfo_txt-bold tableFacturaInfo-obs"> OBSERVACIONES </p>
               <p class="tableFacturaInfo_txt-justify "><?php echo $invoice->observacion; ?></p>
            </div>
            <div class="tableFacturaInfo_divTxt">
               <p class="txt-center"></p>
               <p class="txt-center">Resolución Dian No 18764026538709 Autoriza Facturación Electrónica desde JM-301 al JM-750</p>
            </div>
         </div>
      </div>
      <div class="sectionFooter">
         <table class="tableFooter" style="table-layout: fixed;">
            <tr>
               <td>Emisor :</td>
               <td>Firma de quien revisa :</td>
               <td>Datos de quien recibe a conformidad :</td>
            </tr>
            <tr>
               <td> <?php echo $invoice->usuario_nombre; ?></td>
            </tr>
         </table>
         <table class="tableFirma" style="table-layout: fixed; width: 100%">
            <tr>
               <td>
                  <div class="firma">
                     Firma:
                  </div>
               </td>
               <td>
                  <div class="firma"></div>
               </td>
               <td>
                  <div class="firma">
                     Firma:
                  </div>
               </td>
            </tr>
            <tr>
               <td>
                  <div class="firma">
                     Nombre:
                  </div>
               </td>
               <td>
                  <div class="firma"></div>
               </td>
               <td>
                  <div class="firma">
                     Nombre:
                  </div>
               </td>
            </tr>
            <tr>
               <td></td>
               <td></td>
               <td>
                  <div class="firma">
                     No documento de identidad:
                  </div>
               </td>
            </tr>
            <tr>
               <td></td>
               <td></td>
               <td>
                  <div class="firma">
                     Fecha de Recibido (dd/mm/aaaa):
                  </div>
               </td>
            </tr>
         </table>
         <div class="footer">
            <p class="txt-center">Proveedor Tecnol&oacute;gico: Factura1 SAS / Software Factura1 / Nit: 900.875.062-6</p>
         </div>
      </div>
   </div>
</body>

</html>
