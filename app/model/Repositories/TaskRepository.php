<?php

namespace Model\Repository;

class TaskRepository extends Repository{

	public function findIncomplete(){
		return $this->createEntities(
			$this->findBy(array('done' => false))
			->orderBy('created ASC')->fetchAll()
		);
	}

	public function findIncompleteByUser($userId){
		return $this->createEntities(
			$this->findBy(array('done' => false, 'user_id' => $userId))
				->orderBy('created ASC')->fetchAll()
		);
	}
}