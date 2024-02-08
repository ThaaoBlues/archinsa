<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
        $page = str_replace(".php","",basename($_SERVER['SCRIPT_FILENAME']));
    ?>
    <title><?=$titre_page?></title>
    <link rel="stylesheet" src="css/<?=$page?>.css">

</head>