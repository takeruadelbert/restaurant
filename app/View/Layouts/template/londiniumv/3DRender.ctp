<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1">
        <title><?= _APP_NAME ?> - <?= _APP_CORPORATE ?></title>
        <link rel="icon" type="image/gif/png" href="<?= Router::url("/img/justlogo.png", true) ?>">
        <?php
        $this->Template->import();
        ?>
        <link rel="stylesheet" href="<?= Router::url("/template/londiniumv/css/ThreeJS/three.css", true) ?>">
        <script src="<?= Router::url("/template/londiniumv/js/plugins/ThreeJS/three.js", true) ?>"></script>
        <script src="<?= Router::url("/template/londiniumv/js/plugins/ThreeJS/tween.min.js", true) ?>"></script>
        <script src="<?= Router::url("/template/londiniumv/js/plugins/ThreeJS/TrackballControls.js", true) ?>"></script>
        <script src="<?= Router::url("/template/londiniumv/js/plugins/ThreeJS/CSS3DRenderer.js", true) ?>"></script>
    </head>
    <body class="full-width">
        <!-- Navbar -->
        
        <!-- /navbar -->
        <!-- Page container -->
        <div class="page-container">
            <!-- /sidebar -->
            <!-- Page content -->
            <div class="page-content">
                <?php
                echo $this->element(_TEMPLATE_DIR . "/$template/flash-message");

                echo $this->fetch("content");
                ?>
            </div>
            <?php
            echo $this->element(_TEMPLATE_DIR . "/$template/footer");
            ?>
            <!-- /page content -->
        </div>
        <!-- /page container -->
        <?php
        echo $this->element(_TEMPLATE_DIR . "/$template/elements");
        echo $this->element(_TEMPLATE_DIR . "/$template/modals");
        ?>
    </body>
</html>