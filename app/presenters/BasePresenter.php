<?php

namespace App;

use Nette;
use Nette\Application\UI\Form;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter{

	/** @var \Model\Repository\EListRepository */
	protected $listRepository;

	public function beforeRender(){
		$this->template->lists = $this->listRepository->findAll();
	}

	public function handleSignOut(){
		$this->getUser()->logout();
		$this->redirect('Sign:in');
	}

	protected function createComponentNewListForm(){

		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:in');
		}

		$form = new Form();
		$form->addText('title', 'Název:', null, 50)
			->addRule(Form::FILLED, 'Musíte zadat název seznamu úkolů.');
		$form->addSubmit('create', 'Vytvořit');
		$form->onSuccess[] = $this->newListFormSubmitted;
		return $form;
	}

	public function newListFormSubmitted(Form $form){
		$list = new \Model\Entity\EList();
		$list->title = $form->values->title;
		$id = $this->listRepository->persist($list);
		$this->flashMessage('Seznam úkolů založen.', 'success');
		$this->redirect('Task:default', $id);
	}

	public function injectListRepository(\Model\Repository\EListRepository $listRepository){
		$this->listRepository = $listRepository;
	}

}
