<?php

namespace App;

use Nette\Application\UI\Form;
use Nette\DateTime;


class TaskPresenter extends BasePresenter{

	/** @var \Model\Entity\EList */
	private $list;

	/** @var \Model\Repository\EListRepository */
	protected $listRepository;

	/** @var \Model\Repository\UserRepository */
	private $userRepository;

	/** @var \Model\Repository\TaskRepository */
	private $taskRepository;

	protected function startup(){
		parent::startup();

		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:in');
		}
	}

	public function actionDefault($id){
		try{
			$this->list = $this->listRepository->find($id);
		}catch(\Exception $e){
			$this->setView('notFound');
		}
	}

	public function renderDefault(){
		$this->template->list = $this->list;
	}

	protected function createComponentTaskForm(){
		$userPairs = $this->userRepository->createStatement()->fetchPairs('id', 'name');

		$form = new Form();
		$form->addText('text', 'Úkol:', null, 100)
			->addRule(Form::FILLED, 'Je nutné zadat text úkolu.');
		$form->addSelect('userId', 'Pro:', $userPairs)
			->setPrompt('- Vyberte -')
			->addRule(Form::FILLED, 'Je nutné vybrat, komu je úkol přiřazen.')
			->setDefaultValue($this->getUser()->getId());;
		$form->addSubmit('create', 'Vytvořit');
		$form->onSuccess[] = $this->taskFormSubmitted;
		return $form;
	}

	public function taskFormSubmitted(Form $form){
		$task = new \Model\Entity\Task();
		$task->list = $this->list;
		$task->text = $form->values->text;
		$task->user = $this->userRepository->find($form->values->userId);

		$date = new DateTime();
		$task->created = $date->format('Y-m-d H:i:s');

		$this->taskRepository->persist($task);
		$this->flashMessage('Úkol přidán.', 'success');
		$this->redirect('this');
	}

	protected function createComponentTaskList(){
		if ($this->list === NULL) {
			$this->error('Wrong action');
		}
		return new \Todo\TaskListControl($this->list->tasks, $this->taskRepository);
	}

	public function injectListRepository(\Model\Repository\EListRepository $listRepository){
		$this->listRepository = $listRepository;
	}

	public function injectUserRepository(\Model\Repository\UserRepository $listRepository){
		$this->userRepository = $listRepository;
	}

	public function injectTaskRepository(\Model\Repository\TaskRepository $taskRepository){
		$this->taskRepository = $taskRepository;
	}
}