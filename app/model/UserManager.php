<?php

namespace Model;

use Nette,
	Nette\Utils\Strings;


/*
CREATE TABLE users (
	id int(11) NOT NULL AUTO_INCREMENT,
	username varchar(50) NOT NULL,
	password char(60) NOT NULL,
	role varchar(20) NOT NULL,
	PRIMARY KEY (id)
);
*/

/**
 * Users management.
 */
class UserManager extends Nette\Object implements Nette\Security\IAuthenticator{

	/** @var \Model\Repository\UserRepository */
	private $userRepository;

	public function __construct(\Model\Repository\UserRepository $userRepository){
		$this->userRepository = $userRepository;
	}



	/**
	 * Performs an authentication.
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials){
		list($username, $password) = $credentials;
		//$this->add('test', 'nette');

		$row = $this->userRepository->findByName($username);

		if ($row === false) {
			throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
		}

		if ($row->password !== $this->calculateHash($password, $row->password)) {
			throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
		}

		$arr = $row->toArray();
		unset($arr['password']);
		return new Nette\Security\Identity($row->id, null, $arr);
	}



	/**
	 * Adds new user.
	 * @param  string
	 * @param  string
	 * @return void
	 */
	public function add($username, $password){
		$user = new \Model\Entity\User();
		$user->username = $username;
		$user->password = $this->calculateHash($password);

		$this->userRepository->persist($user);
	}



	/**
	 * Computes salted password hash.
	 * @param  string
	 * @return string
	 */
	public static function calculateHash($password, $salt = NULL){
		if ($password === Strings::upper($password)) { // perhaps caps lock is on
			$password = Strings::lower($password);
		}
		return crypt($password, $salt ?: '$2a$07$' . Strings::random(22));
	}

}
