<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;

// Admin visualizar todos os usuários.
$app->get('/admin/users', function() {
	User::verifyLogin();
	$users = User::listAll();
	$page = new PageAdmin();
	$page->setTpl("users", array(
		"users"=>$users
	));
});

// Admin digitar dados do novo usuário.
$app->get('/admin/users/create', function() {
	User::verifyLogin();
	$page = new PageAdmin();
	$page->setTpl("users-create");
});

// Admin deletar um usuário.
$app->get('/admin/users/:iduser/delete', function($iduser) {
	User::verifyLogin();
	$user = new User();
	$user->get((int)$iduser);
	$user->delete();
	header("Location: /admin/users");
	exit;
});

// Admin criar novos usuários.
$app->post('/admin/users/create', function() {
	User::verifyLogin();
	$user = new User();
	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;
	// Gerar os setters
	$user->setData($_POST);
	// Procedure.
	$user->save();
	header("Location: /admin/users");
	exit;
});

// Admin digitar novos dados de um usuário já existente.
$app->get('/admin/users/:iduser', function($iduser) {
	User::verifyLogin();
	$user = new User();
	$user->get((int)$iduser);
	$page = new PageAdmin();
	$page->setTpl("users-update", array(
		"user"=>$user->getValues()
	));
});

// Admin editar dados de um usuário já existente.
$app->post('/admin/users/:iduser', function($iduser) {
	User::verifyLogin();
	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

	$user->get((int)$iduser);
	$user->setData($_POST);
	$user->update();
	header("Location: /admin/users");
	exit;
});

?>