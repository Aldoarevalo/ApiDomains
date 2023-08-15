<?php

// Configuración de la conexión a la base de datos

$numero = "1452846";

$server = 'base';
$database = 'base';
$username = 'sa';
$password = 'base';
$pdo = new PDO("sqlsrv:Server=$server;Database=$database", $username, $password);

// Consulta para obtener los datos de la factura
$sql = 'SELECT top 1 * FROM enviofacturas WHERE CODIGOVENTA = :numero';
$stmt = $pdo->prepare($sql);
//$stmt->bindValue(':numero', $_GET['numero']);

$stmt->bindParam(':numero', $numero);

$stmt->execute();
$factura = $stmt->fetch(PDO::FETCH_ASSOC);

// Creación del array con los datos de la factura
$documento = array(
    'procesarDocumento' => array(
        array(
            'rDE' => array(
                'dVerFor' => '150',
                'DE' => array(
                    'dSisFact' => '1',
                    'gOpeDE' => array(
                        'iTipEmi' => '1',
                        'dDesTipEmi' => 'Normal'
                    ),
                    'gTimb' => array(
                        'iTiDE' => '1',
                        'dDesTiDE' => 'Factura electrónica',
                        'dNumTim' => '12557716',
                        'dEst' => '001',
                        'dPunExp' => '001',
                        'dNumDoc' => '000001',
                        'dSerieNum' => 'AA'
                    ),
                    'gDatGralOpe' => array(
                        'dFeEmiDE' => $factura['dFeEmiDE'],
                        'gOpeCom' => array(
                            'iTipTra' => '2',
                            'iTImp' => '1',
                            'cMoneOpe' => 'PYG'
                        ),
                        'gEmis' => array(
                            'dRucEm' => '80094652',
                            'dDVEmi' => '9',
                            'dNomEmi' => 'DE generado en ambiente de prueba - sin valor comercial ni fiscal'
                        ),
                        'gDatRec' => array(
                            'iNatRec' => '1',
                            'dRucRec' => $factura['dRucRec'],
                            'dDVRec' => '',
                            'iTiContRec' => '2',
                            'iTiOpe' => '1',
                            'cPaisRec' => 'PRY',
                            'dNomRec' => $factura['dNomRec'],
                            'dDirRec' => '',
                            'dNumCasRec' => '',
                            'cDepRec' => '',
                            'cCiuRec' => ''
                        )
                    ),
                    'gDtipDE' => array(
                        'gCamFE' => array(
                            'iIndPres' => '1'
                        ),
                        'gCamCond' => array(
                            'iCondOpe' => '2',
                            'gPagCred' => array(
                                'iCondCred' => '2',
                                'dCuotas' => '1'
                            )
                        ),
                        'gCamItem' => array()
                    ),
                ),
            ),
        ),
    ),
);

//$//factura_id = 1; // este es el ID de la factura de la que se quieren obtener los detalles
$stmt = $pdo->prepare("SELECT * FROM enviofacturas WHERE CODIGOVENTA = :numero");
$stmt->bindParam(":numero", $numero);
$stmt->execute();
$detalles_factura = $stmt->fetchAll(PDO::FETCH_ASSOC);


$gCamItem = array();
foreach ($detalles_factura as $detalle) {
    $item = array(
        "dCodInt" => $detalle["dCodInt"],
        "dDesProSer" => "Nombre del producto",
        "dCantProSer" => $detalle["dCantProSer"],
        "gValorItem" => array(
            "dPUniProSer" => $detalle["dPUniProSer"],
            "dTotBruOpeItem" => $detalle["dCantProSer"] * $detalle["dPUniProSer"],
            "gValorRestaItem" => array(
                "dDescItem" => $detalle["dDescItem"],
                "dPorcDesIt" => "0.00",
                "dDescGloItem" => "0.00",
                "dAntPreUniIt" => "0.00",
                "dAntGloPreUniIt" => "0.00",
                "dTotOpeItem" => $detalle["dCantProSer"] * $detalle["dPUniProSer"] - $detalle["dDescItem"]
            )
        ),
        "gCamIVA" => array(
            "iAfecIVA" => "1",
            "dDesAfecIVA" => "Gravado IVA",
            "dPropIVA" => "100",
            "dTasaIVA" => "10",//$detalle["impuesto"],
            "dBasGravIVA" => $detalle["dCantProSer"] * $detalle["dPUniProSer"],
            "dLiqIVAItem" => $detalle["dCantProSer"] * $detalle["dPUniProSer"] * /*$detalle["impuesto"]*/"10" / 100
        )
    );
    array_push($gCamItem, $item);
     $items = $gCamItem;
     
}

//$documento["gCamItem"] = $gCamItem;
//$json = json_encode($documento);
//echo $json;
//
//$data = array("gCamDocumento" => $documento, "gCamItem" => $gCamItem);
//echo json_encode($data, JSON_PRETTY_PRINT);


// agregar el arreglo de items al documento
$documento["procesarDocumento"][0]["rDE"]["DE"]["gDtipDE"]["gCamItem"] = $items;

// convertir el documento a formato JSON y mostrarlo
$json = json_encode($documento);
echo $json;