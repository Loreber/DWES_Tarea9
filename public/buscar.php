<?php

/**
 * Página de búsqueda de anime
 *
 * Permite buscar animes por título usando la API de Jikan (cURL)
 * y muestra los resultados con paginación.
 */

require_once __DIR__ . '/../src/api.php';

// Obtener el término de búsqueda y la página actual
$busqueda = '';
if (isset($_GET['q'])) {
  $busqueda = trim($_GET['q']);
}

$paginaActual = 1;
if (isset($_GET['page'])) {
  $paginaActual = intval($_GET['page']);
  if ($paginaActual < 1) {
    $paginaActual = 1;
  }
}

// Variables para almacenar los resultados y errores
$listaAnime = [];
$paginacion = [];
$error      = null;

// Solo buscar si hay un término de búsqueda
if ($busqueda !== '') {
  $response = buscarAnime($busqueda, $paginaActual, 24);

  if (isset($response['error'])) {
    $error = $response['error'];
  } else {
    if (isset($response['data'])) {
      $listaAnime = $response['data'];
    }
    if (isset($response['pagination'])) {
      $paginacion = $response['pagination'];
    }
  }
}
?>
<?php include __DIR__ . '/../templates/base.php' ?>

<?php startblock('titulo') ?>
<?php if ($busqueda !== ''): ?>Buscar: <?= htmlspecialchars($busqueda) ?><?php else: ?>Buscar Anime<?php endif; ?>
<?php endblock() ?>

<?php startblock('valor-busqueda') ?><?= htmlspecialchars($busqueda) ?><?php endblock() ?>

<!-- Contenido principal -->
<?php startblock('contenido') ?>

<!-- Cabecera de la búsqueda -->
<div class="cabecera-busqueda">
  <h1 class="titulo-busqueda">
    <?php if ($busqueda !== ''): ?>
      Resultados de "<?= htmlspecialchars($busqueda) ?>"
    <?php else: ?>
      Buscar Anime
    <?php endif; ?>
  </h1>
  <?php if ($busqueda === ''): ?>
    <p class="subtitulo-busqueda">Usa el buscador de arriba para encontrar series de anime.</p>
  <?php endif; ?>
</div>

<!-- Mostrar errores si los hay -->
<?php if ($error !== null): ?>
  <div class="alerta-error">
    <strong>Error:</strong> <?= htmlspecialchars($error) ?>
  </div>
<?php endif; ?>

<?php if (!empty($listaAnime)): ?>

  <!-- Cuadrícula de resultados -->
  <section class="cuadricula">
    <?php foreach ($listaAnime as $anime): ?>
      <?php
      $titulo = $anime['title'];
      $imagen = $anime['images']['jpg']['large_image_url'];

      // Formatear la puntuación
      $puntuacion = $anime['score'];
      if ($puntuacion > 0) {
        $puntuacion = number_format($puntuacion, 2);
      } else {
        $puntuacion = 'N/A';
      }

      $episodios = intval($anime['episodes']);

      $tipo = $anime['type'];
      if (empty($tipo)) {
        $tipo = 'N/A';
      }

      // Obtener los géneros
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
      ?>
      <a href="anime.php?id=<?= $anime['mal_id'] ?>" class="tarjeta">
        <div class="tarjeta-img-contenedor">
          <img src="<?= htmlspecialchars($imagen) ?>" alt="<?= htmlspecialchars($titulo) ?>" class="tarjeta-img" loading="lazy">
          <span class="tarjeta-puntuacion"><?= htmlspecialchars($puntuacion) ?> ★</span>
        </div>
        <div class="tarjeta-cuerpo">
          <h3 class="tarjeta-titulo"><?= htmlspecialchars($titulo) ?></h3>
          <p class="tarjeta-meta">
            <?= htmlspecialchars($tipo) ?>
            <?php if ($episodios): ?>
              - <?= $episodios ?> eps
            <?php endif; ?>
          </p>
          <p class="tarjeta-generos"><?= htmlspecialchars($generos) ?></p>
        </div>
      </a>
    <?php endforeach; ?>
  </section>

  <!-- Paginación -->
  <?php if (!empty($paginacion)): ?>
    <div class="paginacion">
      <?php if ($paginaActual > 1): ?>
        <a href="buscar.php?q=<?= urlencode($busqueda) ?>&page=<?= $paginaActual - 1 ?>" class="boton-pagina">Anterior</a>
      <?php endif; ?>

      <span class="info-pagina">
        Página <?= $paginaActual ?>
        <?php if (isset($paginacion['last_visible_page'])): ?>
          de <?= $paginacion['last_visible_page'] ?>
        <?php endif; ?>
      </span>

      <?php if (!empty($paginacion['has_next_page'])): ?>
        <a href="buscar.php?q=<?= urlencode($busqueda) ?>&page=<?= $paginaActual + 1 ?>" class="boton-pagina">Siguiente</a>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <!-- Mensaje de error -->
<?php elseif ($busqueda !== ''): ?>
  <p class="estado-vacio">No se encontraron resultados para "<?= htmlspecialchars($busqueda) ?>". Prueba con otro término.</p>
<?php endif; ?>

<?php endblock() ?>