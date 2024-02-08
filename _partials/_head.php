<?php
header("Content-Security-Policy: default-src 'self'; connect-src 'self'; script-src 'self'; img-src 'self'; font-src 'self'; media-src 'self'; frame-src 'self'; sandbox allow-forms; object-src 'none'; frame-ancestors 'none'; form-action 'self'; base-uri 'self'; worker-src 'none'; manifest-src : 'none'; prefetch-src : 'none'; navigate-to 'self';")
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
        $page = str_replace(".php","",basename($_SERVER['SCRIPT_FILENAME']));
    ?>
    <title><?=$titre_page?></title>
    <link rel="stylesheet" src="css/<?=$page?>.css">
    
</head>