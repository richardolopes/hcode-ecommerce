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

// Forgot
$app->get('/admin/forgot', function() {
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	$page->setTpl("forgot");
});

$app->post('/admin/forgot', function() {
	$user = User::getForgot($_POST["email"]);
	header("Location: /admin/forgot/sent");
	exit;
});

$app->get('/admin/forgot/sent', function() {
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	$page->setTpl("forgot-sent");	
});

$app->get('/admin/forgot/reset', function() {
	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));
});

$app->post('/admin/forgot/reset', function() {
	$forgot = User::validForgotDecrypt($_POST["code"]);
	User::setForgotUsed($forgot["idrecovery"]);
	$user = new User();
	$user->get((int)$forgot["iduser"]);

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, [
		"cost"=>12
	]);

	$user->setPassword($password);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	$page->setTpl("forgot-reset-success");
});

?>