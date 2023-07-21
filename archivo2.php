<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Función para obtener un dominio específico
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

    return $result;
}

// Función para obtener todos los dominios
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

// Obtener el dominio específico deseado, puedes cambiar el dominio aquí

if (isset($_GET['domain2'])) {
    $domain_to_check = $_GET['domain2'];
    
    // Verificamos si el dominio a verificar es válido
    if (filter_var($domain_to_check, FILTER_VALIDATE_DOMAIN)) {
        $domain_info = get_domain2($domain_to_check);
        echo json_encode($domain_info); // Devolvemos los datos del dominio en formato JSON
    } else {
        echo json_encode(array('error' => 'El dominio proporcionado no es válido'));
    }
}

if (strpos($domain_info, 'Error') === false) {
    // Si el dominio existe, mostramos la información obtenida
    echo $domain_info;

    // Llamamos a get_domains() para obtener todos los dominios
    $all_domains_info = get_domains2();

    // Convertimos los resultados a arrays asociativos
    $all_domains_data = json_decode($all_domains_info, true);
    $domain_data = json_decode($domain_info, true);

    // Filtrar dominios coincidentes
    $filtered_domains = array_filter($all_domains_data, function ($all_domain) use ($domain_data) {
        return $all_domain['domain'] === $domain_data['domain'];
    });

    if (!empty($filtered_domains)) {
        // Si hay dominios coincidentes, los mostramos
        echo "Dominios coincidentes encontrados:\n";
        print_r($filtered_domains);
    } else {
        echo "No se encontraron otros dominios coincidentes.";
    }
} else {
    // Si el dominio no existe, mostramos un mensaje
    echo 'No se encontró información para el dominio ' . $domain_to_check;
}
