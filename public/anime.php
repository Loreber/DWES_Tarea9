<?php

/**
 * Página de detalle de un anime
 *
 * Muestra la información completa de un anime obtenida de la API de Jikan.
 */

require_once __DIR__ . '/../src/api.php';

// Obtener el ID del anime desde la URL
$animeId = 0;
if (isset($_GET['id'])) {
  $animeId = intval($_GET['id']);
}

// Si no hay ID válido, mostrar error
if ($animeId <= 0) {
  // Establecer el código de estado HTTP 400 Bad Request
  http_response_code(400);
  $tituloPagina = 'Error';
  $mensajeError = 'No se proporcionó un ID de anime.';
} else {
  // Consultar la API para obtener los detalles del anime
  $response = consultarAnime($animeId);

  // Verificar si hubo error en la respuesta
  if (isset($response['error'])) {
    $codigoEstado = 500;
    if (isset($response['status'])) {
      $codigoEstado = $response['status'];
    }
    // Establecer el código de estado HTTP correspondiente al error
    http_response_code($codigoEstado);
    $tituloPagina = 'Error';
    $mensajeError = $response['error'];
  } else {
    $anime = $response['data'];
    $mensajeError = null;
  }
}

// Si no hubo error, extraer y normalizar los datos del anime
if ($mensajeError === null) {
  // Obtener el título principal (preferiblemente en inglés)
  $titulo = $anime['title'];
  if (!empty($anime['title_english'])) {
    $titulo = $anime['title_english'];
  }

  // Obtener el título japonés si está disponible
  $tituloJapones = '';
  if (!empty($anime['title_japanese'])) {
    $tituloJapones = $anime['title_japanese'];
  }

  // Obtener la URL de la imagen del anime
  $imagen = $anime['images']['jpg']['large_image_url'];

  // Obtener la puntuación del anime y formatearla
  $puntuacion = $anime['score'];
  if ($puntuacion > 0) {
    $puntuacion = number_format($puntuacion, 2);
  } else {
    $puntuacion = 'N/A';
  }

  // Obtener el tipo de anime (TV, Movie, OVA, etc.)
  $tipo = 'N/A';
  if (!empty($anime['type'])) {
    $tipo = $anime['type'];
  }

  // Obtener el número de episodios
  $episodios = 'N/A';
  if (!empty($anime['episodes'])) {
    $episodios = intval($anime['episodes']);
  }

  // Obtener el estado del anime (Airing, Finished, etc.)
  $estado = 'N/A';
  if (!empty($anime['status'])) {
    $estado = $anime['status'];
  }

  // Obtener la duración de cada episodio
  $duracion = 'N/A';
  if (!empty($anime['duration'])) {
    $duracion = $anime['duration'];
  }

  // Obtener la clasificación por edades
  $clasificacion = 'N/A';
  if (!empty($anime['rating'])) {
    $clasificacion = $anime['rating'];
  }

  // Obtener la temporada de emisión
  $temporada = 'N/A';
  if (!empty($anime['season'])) {
    $temporada = ucfirst($anime['season']);
  }

  // Obtener el año de emisión
  $anio = '';
  if (!empty($anime['year'])) {
    $anio = $anime['year'];
  }

  // Obtener el período de emisión
  $emitido = 'N/A';
  if (isset($anime['aired']['string'])) {
    $emitido = $anime['aired']['string'];
  }

  // Obtener el ranking del anime
  $ranking = 'N/A';
  if (!empty($anime['rank'])) {
    $ranking = $anime['rank'];
  }

  // Obtener la popularidad del anime
  $popularidad = 'N/A';
  if (!empty($anime['popularity'])) {
    $popularidad = $anime['popularity'];
  }

  // Obtener el número de favoritos del anime
  $favoritos = 'N/A';
  if (isset($anime['favorites'])) {
    $favoritos = number_format($anime['favorites']);
  }

  // Obtener nombres de géneros
  $nombresGeneros = [];
  if (!empty($anime['genres'])) {
    foreach ($anime['genres'] as $genero) {
      $nombresGeneros[] = $genero['name'];
    }
  }
  if (!empty($nombresGeneros)) {
    $generos = implode(', ', $nombresGeneros);
  } else {
    $generos = 'N/A';
  }

  // Obtener nombres de temas
  $nombresTemas = [];
  if (!empty($anime['themes'])) {
    foreach ($anime['themes'] as $tema) {
      $nombresTemas[] = $tema['name'];
    }
  }
  if (!empty($nombresTemas)) {
    $temas = implode(', ', $nombresTemas);
  } else {
    $temas = 'N/A';
  }

  // Obtener nombres de estudios
  $nombresEstudios = [];
  if (!empty($anime['studios'])) {
    foreach ($anime['studios'] as $estudio) {
      $nombresEstudios[] = $estudio['name'];
    }
  }
  if (!empty($nombresEstudios)) {
    $estudios = implode(', ', $nombresEstudios);
  } else {
    $estudios = 'N/A';
  }

  // Obtener nombres de productores
  $nombresProductores = [];
  if (!empty($anime['producers'])) {
    foreach ($anime['producers'] as $productor) {
      $nombresProductores[] = $productor['name'];
    }
  }
  if (!empty($nombresProductores)) {
    $productores = implode(', ', $nombresProductores);
  } else {
    $productores = 'N/A';
  }

  // Obtener la fuente original del anime
  $fuente = 'N/A';
  if (!empty($anime['source'])) {
    $fuente = $anime['source'];
  }

  // Obtener los servicios de streaming donde se puede ver el anime
  $streaming = [];
  if (!empty($anime['streaming'])) {
    $streaming = $anime['streaming'];
  }

  // Obtener la sinopsis del anime 
  $sinopsis = '';
  if (!empty($anime['synopsis'])) {
    $sinopsis = $anime['synopsis'];
  }

  // Obtener la URL de MyAnimeList del anime
  $malUrl = '#';
  if (!empty($anime['url'])) {
    $malUrl = $anime['url'];
  }

  // Establecer el título de la página como el título del anime
  $tituloPagina = $titulo;
}

?>
<?php include __DIR__ . '/../templates/base.php' ?>

<?php startblock('titulo') ?><?= htmlspecialchars($tituloPagina) ?><?php endblock() ?>

<?php startblock('contenido') ?>

<?php if ($mensajeError !== null): ?>
  <!-- Mensaje de error cuando falla la validación o consulta a la API -->
  <div class="alerta-error">
    <strong>Error:</strong> <?= htmlspecialchars($mensajeError) ?>
  </div>
<?php else: ?>

  <!-- Vista de detalle completa del anime -->
  <article class="detalle">
    <a href="index.php" class="enlace-volver">&larr; Volver a la lista</a>

    <!-- Cabecera: póster y datos principales -->
    <div class="detalle-cabecera">
      <div class="detalle-poster">
        <img src="<?= htmlspecialchars($imagen) ?>" alt="<?= htmlspecialchars($titulo) ?>" class="detalle-imagen">
      </div>

      <div class="detalle-info">
        <h1 class="detalle-titulo"><?= htmlspecialchars($titulo) ?></h1>
        <?php if ($tituloJapones !== ''): ?>
          <p class="detalle-subtitulo"><?= htmlspecialchars($tituloJapones) ?></p>
        <?php endif; ?>

        <div class="detalle-etiquetas">
          <span class="etiqueta etiqueta-puntuacion"><?= htmlspecialchars($puntuacion) ?> ★</span>
          <span class="etiqueta"><?= htmlspecialchars($tipo) ?></span>
          <span class="etiqueta"><?= htmlspecialchars($estado) ?></span>
          <?php if ($anio !== ''): ?>
            <span class="etiqueta"><?= htmlspecialchars($temporada) ?> <?= $anio ?></span>
          <?php endif; ?>
        </div>

        <table class="detalle-tabla">
          <tr>
            <th>Episodios</th>
            <td><?= htmlspecialchars((string) $episodios) ?></td>
          </tr>
          <tr>
            <th>Duración</th>
            <td><?= htmlspecialchars($duracion) ?></td>
          </tr>
          <tr>
            <th>Emisión</th>
            <td><?= htmlspecialchars($emitido) ?></td>
          </tr>
          <tr>
            <th>Fuente</th>
            <td><?= htmlspecialchars($fuente) ?></td>
          </tr>
          <tr>
            <th>Clasificación</th>
            <td><?= htmlspecialchars($clasificacion) ?></td>
          </tr>
          <tr>
            <th>Estudios</th>
            <td><?= htmlspecialchars($estudios) ?></td>
          </tr>
          <tr>
            <th>Productores</th>
            <td><?= htmlspecialchars($productores) ?></td>
          </tr>
          <tr>
            <th>Géneros</th>
            <td><?= htmlspecialchars($generos) ?></td>
          </tr>
          <tr>
            <th>Temas</th>
            <td><?= htmlspecialchars($temas) ?></td>
          </tr>
        </table>

        <!-- Bloque de métricas resumen -->
        <div class="detalle-estadisticas">
          <div class="estadistica">
            <span class="estadistica-valor">#<?= htmlspecialchars((string) $ranking) ?></span>
            <span class="estadistica-etiqueta">Ranking</span>
          </div>
          <div class="estadistica">
            <span class="estadistica-valor">#<?= htmlspecialchars((string) $popularidad) ?></span>
            <span class="estadistica-etiqueta">Popularidad</span>
          </div>
          <div class="estadistica">
            <span class="estadistica-valor"><?= htmlspecialchars($favoritos) ?></span>
            <span class="estadistica-etiqueta">Favoritos</span>
          </div>
        </div>
      </div>
    </div>

    <?php if ($sinopsis !== ''): ?>
      <!-- Sinopsis solo si la API devuelve texto -->
      <section class="detalle-seccion">
        <h2>Sinopsis (En inglés)</h2>
        <p class="detalle-sinopsis"><?= htmlspecialchars($sinopsis) ?></p>
      </section>
    <?php endif; ?>

    <?php if (!empty($streaming)): ?>
      <!-- Enlaces externos a plataformas de streaming -->
      <section class="detalle-seccion">
        <h2>Dónde verlo</h2>
        <div class="enlaces-streaming">
          <?php foreach ($streaming as $servicio): ?>
            <?php if (!empty($servicio['url'])): ?>
              <a href="<?= htmlspecialchars($servicio['url']) ?>" target="_blank" class="enlace-streaming">
                <?= htmlspecialchars($servicio['name']) ?>
              </a>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>

    <!-- Botón a la ficha original en MyAnimeList -->
    <div class="detalle-seccion" style="text-align: center;">
      <a href="<?= htmlspecialchars($malUrl) ?>" target="_blank" class="enlace-mal">
        Más Información
      </a>
    </div>

  </article>

<?php endif; ?>

<?php endblock() ?>