<?php 

session_start();

require_once("vendor/autoload.php");

// Rotas.
use \Slim\Slim;

// Página do site.
use \Hcode\Page;

// Página de admin.
use \Hcode\PageAdmin;

// Criar usuário.
use \Hcode\Model\User;

// Criar categorias
use \Hcode\Model\Category;

$app = new Slim();

$app->config('debug', true);

// Página inicial.
$app->get('/', function() {
    $page = new Page();

    // Template utilizado.
    $page->setTpl("index");
});

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

$app->get('/admin/categories', function() {
	User::verifyLogin();
	$categories = Category::listAll();

	$page = new PageAdmin();
	$page->setTpl("categories", [
		"categories"=>$categories
	]);
});

$app->get('/admin/categories/create', function() {
	User::verifyLogin();
	$page = new PageAdmin();
	$page->setTpl("categories-create");
});

$app->post('/admin/categories/create', function() {
	User::verifyLogin();
	$category = new Category();
	$category->setData($_POST);
	$category->save();
	header("Location: /admin/categories");
	exit;
});

$app->get('/admin/categories/:idcategory/delete', function($idcategory) {
	User::verifyLogin();
	$category = new Category();
	$category->get((int)$idcategory);
	$category->delete();
	header("Location: /admin/categories");
	exit;
});

$app->get('/admin/categories/:idcategory', function($idcategory) {
	User::verifyLogin();
	$category = new Category();
	$category->get((int)$idcategory);
	$page = new PageAdmin();
	$page->setTpl("categories-update", array(
		"category"=>$category->getValues()
	));
});

$app->post('/admin/categories/:idcategory', function($idcategory) {
	User::verifyLogin();
	$category = new Category();
	$category->get((int)$idcategory);
	$category->setData($_POST);
	$category->save();
	header("Location: /admin/categories");
	exit;
});

$app->run();

 ?>