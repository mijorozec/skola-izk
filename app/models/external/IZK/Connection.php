<?php

namespace Ecole\External\IZK;

use Nette\Object;
use phpQuery\phpQuery;
use \Curl;

class Connection extends Object {

	const LOGIN_URI = "http://www.izk.sk/knizka/prihlas.php?school=%school%&lang=1";
	const INDEX_URI = "http://www.izk.sk/knizka/index_login.php";
	const LOGIN_SUCCESSFUL_URI = 'index_login.php';
	const LOGOUT_URI = "http://www.izk.sk/knizka/logout.php";

	protected $username, $password, $school, $opened = FALSE, $connResponse;

    public function __construct($username, $password, $school) {
		$this->username = $username;
		$this->password = $password;
		$this->school = $school;
	}

	public function load($page = 'index') {
		switch ($page) {
			case 'index':
				$curl = $this->getCurl();
				$response = $curl->get(self::INDEX_URI);
				$body = $response->body;
				$body = iconv('CP1250', 'UTF-8', $body);
				return $body;
		}
	}

	public function tryLogIn() {
		$response = $this->open();
		if (strstr($response->body, 'Location: ' . self::LOGIN_SUCCESSFUL_URI))
			return TRUE;
		elseif (isset($response->headers['Location']) && strstr($response->headers['Location'], self::LOGIN_SUCCESSFUL_URI))
			return TRUE;
		else
			return FALSE;
	}

	public function open() {
		if ($this->opened) return $this->connResponse;
		$curl = $this->getCurl();
		$response = $curl->post(str_replace('%school%', $this->school, self::LOGIN_URI), array(
			'meno' => $this->username,
			'heslo' => $this->password,
		));
		$this->opened = TRUE;
		$this->connResponse = $response;
		return $response;
	}

	public function close() {
		if (!$this->opened) return;
		$curl = $this->getCurl();
		$curl->get(self::LOGOUT_URI);
	}

	protected function getCurl() {
		$curl = new Curl;
		$curl->cookie_file = TEMP_DIR . '/cookies/' . $this->username . '.txt';
		return $curl;
	}
}
