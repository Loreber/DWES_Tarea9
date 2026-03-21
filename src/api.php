<?php

/**
 * Funciones para la API de Jikan (MyAnimeList)
 *
 * Usa cURL para hacer peticiones HTTP a la API REST de Jikan
 * y obtener información de anime en formato JSON.
 *
 * @see https://docs.api.jikan.moe/
 */

/** URL base de la API */
define('JIKAN_BASE_URL', 'https://api.jikan.moe/v4');

/**
 * Hace una petición GET a una URL usando cURL y devuelve la respuesta.
 *
 * Configura cURL con opciones de seguridad (SSL) y timeouts.
 * Si hay algún error, devuelve un array con la clave 'error'.
 *
 * @param string $url URL completa a consultar.
 * @return array Datos decodificados del JSON o array con 'error'.
 */
function curlGet($url)
{
  $curl = curl_init();

  // Configurar las opciones de cURL
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
  curl_setopt($curl, CURLOPT_TIMEOUT, 30);
  curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
  ]);

  // Ejecutar la petición
  $response = curl_exec($curl);
  $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

  // Comprobar si hubo error de conexión
  if (curl_errno($curl)) {
    $error = curl_error($curl);
    curl_close($curl);
    return ['error' => "Error de cURL: {$error}", 'status' => 0];
  }

  // Cerrar la sesión de cURL
  curl_close($curl);

  // Comprobar el código de respuesta HTTP
  if ($httpCode < 200 || $httpCode >= 300) {
    return [
      'error'  => "Error HTTP: código {$httpCode}",
      'status' => $httpCode,
    ];
  }

  // Decodificar el JSON de la respuesta
  $datos = json_decode($response, true);
  if (json_last_error() !== JSON_ERROR_NONE) {
    return ['error' => 'Error al decodificar JSON: ' . json_last_error_msg()];
  }

  return $datos;
}

/**
 * Lee la lista de animes favoritos desde un archivo JSON local.
 *
 * Los datos se guardaron previamente en un archivo JSON para no tener
 * que hacer muchas peticiones a la API cada vez que se carga la página.
 *
 * @return array Lista de animes con sus datos.
 */
function obtenerFavoritos()
{
  $archivo = __DIR__ . '/../data/favoritos.json';
  $json = file_get_contents($archivo);
  $datos = json_decode($json, true);
  if (empty($datos)) {
    return [];
  }
  return $datos;
}

/**
 * Obtiene los datos completos de un anime por su ID.
 *
 * @param int $id ID del anime en MyAnimeList.
 * @return array Respuesta de la API con los datos del anime.
 */
function consultarAnime($id)
{
  $url = JIKAN_BASE_URL . "/anime/{$id}/full";
  return curlGet($url);
}

/**
 * Busca animes por título en la API.
 *
 * @param string $query Texto de búsqueda.
 * @param int $page Número de página.
 * @param int $limit Resultados por página.
 * @return array Respuesta de la API con 'data' y 'pagination'.
 */
function buscarAnime($query, $page, $limit)
{
  $url = JIKAN_BASE_URL . '/anime?q=' . urlencode($query)
    . '&page=' . $page
    . '&limit=' . $limit
    . '&sfw=true&order_by=score&sort=desc';

  return curlGet($url);
}
