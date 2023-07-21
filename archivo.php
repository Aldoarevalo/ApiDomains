<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Función para obtener un dominio específico
function get_domain($domain) {
    $url = 'https://domain.apitool.info/v2/domains/' . urlencode($domain);
    $headers = [
        'http' => [
            'header' => "X-TOKEN: 1683066-ccd0d3e2-c12f-40ba-9ee9-3cb7e36e1fd5\r\n",
            'method' => 'GET'
        ]
    ];

    $context = stream_context_create($headers);
    $result = file_get_contents($url, false, $context);

    if ($result === false) {
        return 'Error: No se pudo obtener el dominio';
    }

    // Decodificamos el resultado JSON
    return json_decode($result, true); // Convertimos el resultado a un array asociativo
}

// Verificamos si se proporcionó el parámetro 'domain' desde Postman
if (isset($_GET['domain'])) {
    $domain_to_check = $_GET['domain'];
    echo 'domain';
    // Verificamos si el dominio a verificar es válido
    if (filter_var($domain_to_check, FILTER_VALIDATE_DOMAIN)) {
        $domain_info = get_domain($domain_to_check);
        echo json_encode($domain_info); // Devolvemos los datos del dominio en formato JSON
       
    } else {
        echo json_encode(array('error' => 'El dominio proporcionado no es válido'));
    }
} elseif (isset($_GET['but'])) { // Revisamos si se proporcionó el parámetro 'get_all_domains' desde Postman
    echo 'aacbut';
    $all_domains_info = get_domains();
    echo json_encode($all_domains_info); // Devolvemos la lista de todos los dominios en formato JSON
    
} elseif (isset($_GET['domain2'])) { // Revisamos si se proporcionó el parámetro 'domain2' desde Postman
    $domain_to_check = $_GET['domain2'];
    echo 'domain2';
    // Verificamos si el dominio a verificar es válido
    if (filter_var($domain_to_check, FILTER_VALIDATE_DOMAIN)) {
        $domain_info = get_domain2($domain_to_check);
        echo $domain_info; // Devolvemos los datos del dominio y sus coincidencias en formato JSON
       
    } else {
        echo json_encode(array('error' => 'El segundo dominio proporcionado no es válido'));
    }
} else {
    // Si no se proporcionó ninguno de los parámetros necesarios desde Postman
    echo json_encode(array('error' => 'Debes proporcionar un dominio a verificar o usar los parámetros "get_all_domains" o "domain2"'));
}

// Función para obtener todos los dominios
function get_domains() {
    $url = 'https://domain.apitool.info/v2/domains';
    $headers = [
        'http' => [
            'header' => "X-TOKEN: 1683066-ccd0d3e2-c12f-40ba-9ee9-3cb7e36e1fd5\r\n",
            'method' => 'GET'
        ]
    ];

    $context = stream_context_create($headers);
    $result = file_get_contents($url, false, $context);

    if ($result === false) {
        return 'Error: No se pudieron obtener los dominios';
    }
    return json_decode($result, true); // Convertimos el resultado a un array asociativo
    return $result;
}


// Función para obtener un segundo dominio específico y sus coincidencias
function get_domain2($domain) {
    $url = 'https://domain.apitool.info/v2/domains/' . urlencode($domain);
    $headers = [
        'http' => [
            'header' => "X-TOKEN: 1683066-ccd0d3e2-c12f-40ba-9ee9-3cb7e36e1fd5\r\n",
            'method' => 'GET'
        ]
    ];

    $context = stream_context_create($headers);
    $result = file_get_contents($url, false, $context);

    if ($result === false) {
        return 'Error: No se pudo obtener el dominio';
    }

    // Llamamos a get_domains() para obtener todos los dominios nuevamente
    $all_domains_info = get_domains2();

    // Convertimos los resultados a arrays asociativos
    $all_domains_data = json_decode($all_domains_info, true);
    //$all_domains_data = $all_domains_info;

    // Filtrar dominios coincidentes
    $filtered_domains = array_filter($all_domains_data, function ($all_domain) use ($domain) {
        $current_domain = explode(".", $all_domain['domain']);
        $target_domain = explode(".", $domain);
        return $current_domain[0] === $target_domain[0];
    });

    if (!empty($filtered_domains)) {
        // Si hay dominios coincidentes, los agregamos al resultado
        $coincidences = array_values($filtered_domains); // Tomamos solo los valores para reindexar el array
        return json_encode($coincidences);
    } else {
        // Si no hay coincidencias, devolvemos solo el detalle del dominio proporcionado
        return $result;
    }
}




// Función para obtener todos los dominios nuevamente
function get_domains2() {
    $url = 'https://domain.apitool.info/v2/domains';
    $headers = [
        'http' => [
            'header' => "X-TOKEN: 1683066-ccd0d3e2-c12f-40ba-9ee9-3cb7e36e1fd5\r\n",
            'method' => 'GET'
        ]
    ];

    $context = stream_context_create($headers);
    $result = file_get_contents($url, false, $context);

    if ($result === false) {
        return 'Error: No se pudieron obtener los dominios';
    }

    return $result;
}
