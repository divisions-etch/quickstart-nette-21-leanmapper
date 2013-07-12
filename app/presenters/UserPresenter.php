<?php

use Nette\Application\UI\Form;
use Nette\Security as NS;

/**
 */
class UserPresenter extends BasePresenter
{
	/** @var \Model\Repository\UserRepository */
	private $userRepository;

	/** @var \Todo\Authenticator */
	private $authenticator;

	protected function startup()
	{
		parent::startup();
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:in');
		}
	}

	protected function createComponentPasswordForm()
	{
		$form = new Form();
		$form->addPassword('oldPassword', 'Staré heslo:')
			->addRule(Form::FILLED, 'Je nutné zadat staré heslo.');
		$form->addPassword('newPassword', 'Nové heslo:')
			->addRule(Form::MIN_LENGTH, 'Nové heslo musí mít alespoň %d znaků.', 6);
		$form->addPassword('confirmPassword', 'Potvrzení hesla:')
			->addRule(Form::FILLED, 'Nové heslo je nutné zadat ještě jednou pro potvrzení.')
			->addRule(Form::EQUAL, 'Zadná hesla se musejí shodovat.', $form['newPassword']);
		$form->addSubmit('set', 'Změnit heslo');
		$form->onSuccess[] = $this->passwordFormSubmitted;
		return $form;
	}


	public function passwordFormSubmitted(Form $form)
	{
		$values = $form->getValues();
		$user = $this->getUser();
		try {
			$this->authenticator->authenticate(array($user->getIdentity()->username, $values->oldPassword));
			$this->userRepository->setPassword($user->getId(), $values->newPassword);
			$this->flashMessage('Heslo bylo změněno.', 'success');
			$this->redirect('Homepage:');
		} catch (NS\AuthenticationException $e) {
			$form->addError('Zadané heslo není správné.');
		}
	}

	public function injectUserRepository(\Model\Repository\UserRepository $userRepository){
		$this->userRepository = $userRepository;
	}

	public function injectAuthenticator(\Model\UserManager $userManager){
		$this->authenticator = $userManager;
	}
}