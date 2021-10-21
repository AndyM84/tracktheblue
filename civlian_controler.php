<?php

require '\Users\User\vendor\autoload.php';

$templates = new League\Plates\Engine(__DIR__);

echo $templates->render('header_section');
echo $templates->render('civlian_form');
echo $templates->render('footer');

?>