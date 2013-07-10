<?php

namespace App;

use Nette;


class HomepagePresenter extends BasePresenter{

	/** @var \Model\Repository\TaskRepository */
	private $taskRepository;

	protected function startup(){
		parent::startup();

		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:in');
		}
	}

	public function createComponentUserTasks(){
		$incomplete = $this->taskRepository->findIncompleteByUser($this->getUser()->getId());
		$control = new \Todo\TaskListControl($incomplete, $this->taskRepository);
		$control->displayList = TRUE;
		$control->displayUser = FALSE;
		return $control;
	}

	public function createComponentIncompleteTasks(){
		return new \Todo\TaskListControl($this->taskRepository->findIncomplete(), $this->taskRepository);
	}

	public function injectTaskRepository(\Model\Repository\TaskRepository $taskRepository){
		$this->taskRepository = $taskRepository;
	}

}
