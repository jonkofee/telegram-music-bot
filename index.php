<?php
use Phalcon\Loader;
use Phalcon\Mvc\Application;
use Phalcon\Di\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Factory;

require 'vendor/autoload.php';

(new Loader())
	->registerDirs([
		'api/controllers/',
		'api/models/',
		'api/validations',
		'Core/helpers',
		'Core/'
	])
	->registerNamespaces([
		'Core' => 'Core',
	])->register();
$debug = (new \Phalcon\Debug())->listen();

$di = new FactoryDefault();
$di->set('config', ConfigIni::getInstance());

$di->set('request', new \Core\Request());

$di->set('router', new Router(false));

$di->set(
	'db',
	function () {
		return Factory::load($this->get('config')->database);
	}
);

$config = ConfigIni::getInstance()->bot;

$telegram = new \Longman\TelegramBot\Telegram($config->token, $config->username);
$telegram->addCommandsPaths([
	__DIR__ . '/Commands/'
]);


$di['bot'] = $telegram;

$application = new Application($di);

try {
	$router = $di->get('router');

	$router->handle();

	$dispatcher = $di->get('dispatcher');

	$dispatcher->setControllerName($router->getControllerName());

	$dispatcher->setActionName($router->getActionName());

	$dispatcher->setParams($router->getParams());

	$response = $di->get('response');

	try {
		$dispatcher->dispatch();

		$result = [
			'status' => 'OK',
			'response' => $dispatcher->getReturnedValue()
		];
	} catch (Exception $e) {
		$statusCode = $e->getCode() ? $e->getCode() : 500;

		$response->setStatusCode($statusCode);

		$result = [
			'status' => 'ERROR',
			'message' => is_array($e->getMessage()) ? $e->getMessage() : [$e->getMessage()]
		];
	}

	$response->setJsonContent($result);

	$response->sendHeaders();

	echo $response->getContent();
} catch (\Exception $e) {
	echo $e->getMessage();
}