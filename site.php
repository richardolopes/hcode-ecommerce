<?php

use \Hcode\Page;

// Página inicial.
$app->get('/', function() {
    $page = new Page();

    // Template utilizado.
    $page->setTpl("index");
});


?>