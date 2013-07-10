<?php

namespace Model\Repository;

class UserRepository extends Repository{

	public function findByName($username){
		return $this->createStatement()->where('username = %s', $username)->fetch();
	}

	public function setPassword($id, $password){
		$this->connection->update($this->getTable(), array(
			'password' => \Model\UserManager::calculateHash($password)))
		->where('id = %i', $id)
		->execute();
	}
}