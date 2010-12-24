<?php

namespace Ecole;

use Nette\Object;
use Nette\Security\IAuthenticator;
use Nette\Security\AuthenticationException;
use Nette\Security\Identity;

class Authenticator extends Object implements IAuthenticator {
	const SCHOOL = 2;


    public function authenticate(array $credentials) {
		$username = $credentials[self::USERNAME];
		$password = $credentials[self::PASSWORD];
		$school = $credentials[self::SCHOOL];

		$conn = new External\IZK\Connection($username, $password, $school);

		if (!$conn->tryLogIn()) throw new AuthenticationException(NULL, self::INVALID_CREDENTIAL);

		$conn->close();

		return new Identity($username, array(), array(
			'username' => $username,
			'password' => $password,
			'school'   => $school,
		));
	}
}
