<?php

namespace Todo;
use Nette;

class TaskListControl extends Nette\Application\UI\Control{

	private $selected;

	/** @var \Model\Repository\TaskRepository */
	private $taskRepository;

	/** @var boolean */
	public $displayUser = TRUE;

	/** @var boolean */
	public $displayList = FALSE;

	public function __construct(array $selected, \Model\Repository\TaskRepository $taskRepository){
		parent::__construct(); // vždy je potřeba volat rodičovský konstruktor
		$this->selected = $selected;
		$this->taskRepository = $taskRepository;
	}

	public function render(){
		$this->template->setFile(__DIR__ . '/TaskList.latte');
		$this->template->tasks = $this->selected;
		$this->template->displayUser = $this->displayUser;
		$this->template->displayList = $this->displayList;
		$this->template->render();
	}

	public function handleMarkDone($taskId){
		$task = $this->taskRepository->find($taskId);
		$task->done = 1;
		$this->taskRepository->persist($task);

		$this->presenter->redirect('this');
	}
}