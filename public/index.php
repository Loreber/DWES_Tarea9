<?php

/**
 * Página principal - Animes favoritos
 *
 * Lee los datos de los animes favoritos desde un archivo JSON
 * y los muestra en una cuadrícula de tarjetas.
 */

require_once __DIR__ . '/../src/api.php';

// Carga inicial de favoritos desde JSON local
$listaAnime = obtenerFavoritos();
?>
<?php include __DIR__ . '/../templates/base.php' ?>

<?php startblock('titulo') ?>Inicio<?php endblock() ?>

<?php startblock('contenido') ?>

<section class="hero">
  <h1 class="hero-titulo">Mis Animes Favoritos</h1>
  <p class="hero-subtitulo">Una selección de series de anime que recomiendo, con datos de la API de Jikan.</p>
</section>

<?php if (!empty($listaAnime)): ?>

  <!-- Listado de tarjetas generado dinámicamente -->
  <section class="cuadricula">
    <?php foreach ($listaAnime as $anime): ?>
      <?php
      // Extraer datos base del anime para pintar la tarjeta
      $titulo = $anime['title'];
      $imagen = $anime['images']['jpg']['large_image_url'];

      // Mostrar puntuación formateada o N/A si no existe
      $puntuacion = $anime['score'];
      if ($puntuacion > 0) {
        $puntuacion = number_format($puntuacion, 2);
      } else {
        $puntuacion = 'N/A';
      }

      // Obtener el número de episodios
      $episodios = 'N/A';
      if (!empty($anime['episodes'])) {
        $episodios = intval($anime['episodes']);
      }
      // Obtener el tipo de anime (TV, Movie, OVA, etc.)
      $tipo = 'N/A';
      if (!empty($anime['type'])) {
        $tipo = $anime['type'];
      }

      // Convertir array de géneros en texto legible
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

      <!-- Tarjeta de anime -->
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
              - <?= $episodios ?> episodios
            <?php endif; ?>
          </p>
          <p class="tarjeta-generos"><?= htmlspecialchars($generos) ?></p>
        </div>
      </a>
    <?php endforeach; ?>
  </section>

<?php else: ?>
  <p class="estado-vacio">No se pudieron cargar los animes.</p>
<?php endif; ?>

<?php endblock() ?>