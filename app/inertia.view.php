<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title inertia><?php if(isset($GLOBALS['title'])) { echo $GLOBALS['title'] . ' - '; } ?>SpiritVale Info</title>
    <?php if (isset($GLOBALS['description'])) {  echo '<meta name="description" content="' . $GLOBALS['description'] . '">'; } ?>
    <?php if (isset($GLOBALS['icon'])) {  echo '<meta property="og:image" content="https://spiritvale.info/content/game/icons/' . $GLOBALS['icon'] . '.webp"/>'; } ?>

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png"/>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png"/>
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png"/>
    <link rel="manifest" href="/favicon/site.webmanifest"/>

    <x-vite-tags entrypoint="app/inertia.entrypoint.ts"/>
</head>
<body>
<?= $this->inertia() ?>
</body>
</html>