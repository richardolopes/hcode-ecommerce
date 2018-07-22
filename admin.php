<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;

// Painel de admin.
$app->get('/admin', function() {
	// Verificar se esta logado e se tem permissão de admin.
	User::verifyLogin();
	// Painel do admin.
    $page = new PageAdmin();
    // Template utilizado.
    $page->setTpl("index");
});

// Página de login.
$app->get('/admin/login', function() {
	// Os parametros são para não utilizar o header e footer padrão.
    $page = new PageAdmin([
    	"header"=>false,
    	"footer"=>false
    ]);
    // Template utilizado.
    $page->setTpl("login");
});

// Página de verificar o login.
$app->post('/admin/login', function() {
	User::login($_POST["login"], $_POST["password"]);
	header("Location: /admin");
	exit;
});

// Sair
$app->get('/admin/logout', function() {
	User::logout();
	header("Location: /admin/login");
	exit;
});


?>