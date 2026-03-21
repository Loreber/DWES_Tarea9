<?php require_once __DIR__ . '/../src/ti.php' ?>
<!DOCTYPE html>
<html lang="es">

<head>
  <!-- Metadatos y recursos globales de la aplicación -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bloque de título definido por cada página hija -->
  <title><?php startblock('titulo') ?>Animeoteca<?php endblock() ?> — Animeoteca</title>
  <link rel="stylesheet" href="styles/main.css">
</head>

<body>

  <!-- Navegación principal y buscador global -->
  <nav>
    <div class="contenedor nav-contenido">
      <a href="index.php" class="logo">Animeoteca</a>
      <form class="formulario-busqueda" action="buscar.php" method="GET">
        <!-- Valor de búsqueda opcional inyectado por cada vista -->
        <input type="search" name="q" class="campo-busqueda" placeholder="Buscar anime..." value="<?php startblock('valor-busqueda') ?><?php endblock() ?>" required>
        <button type="submit" class="boton-busqueda">🔍</button>
      </form>
    </div>
  </nav>

  <!-- Zona de contenido principal renderizada por cada página -->
  <main class="contenedor">
    <?php startblock('contenido') ?>
    <?php endblock() ?>
  </main>

  <!-- Pie fijo con autoría y fuente de datos -->
  <footer>
    <div class="contenedor pie-contenido">
      <p>Lorena Bersabé Granado - 17478390T</p>
      <p class="pie-nota">Datos de <a href="https://jikan.moe/" target="_blank">Jikan API</a> y MyAnimeList</p>
    </div>
  </footer>

</body>

</html>