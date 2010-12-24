<?php

use Nette\Security\AuthenticationException;
use Ecole\Authenticator;
use Nette\Application\AppForm;

class SignPresenter extends BasePresenter {


	public function actionIn() {
		if ($this->getUser()->loggedIn) throw new Nette\Application\BadRequestException(NULL, 403);

		$form = $this['loginForm'];
		$form->onSubmit[] = callback($this, 'login');
		$form->setDefaults(array(
			'staysignedin' => TRUE,
		));
	}

	public function actionOut() {
		if (!$this->getUser()->loggedIn) throw new Nette\Application\BadRequestException(NULL, 403);

		$this->getUser()->logout(TRUE);

		$this->redirect('in');
	}

	protected function createComponentLoginForm() {
		$form = new AppForm;

		$form->addText('username', 'Meno')
				->setRequired('Prosím zadajte meno');
		$form->addPassword('password', 'Heslo')
				->setRequired('Prosím zadajte heslo');
		$form->addSelect('school', 'Škola', array(
			'gy14029' => 'Škola pre mimoriadne nadané deti a Gymnázium, Teplická 7',
		));
		$form->addCheckbox('staysignedin', 'Zostať prihlásený');
		$form->addSubmit('send', 'Prihlásiť');

		return $form;
	}

	public function login(AppForm $form) {
		$values = $form->values;

		try {
			if ($values['staysignedin']) {
				$this->getUser()->setExpiration('+ 14 days', FALSE);
			} else {
				$this->getUser()->setExpiration('+ 20 minutes', TRUE);
			}
			$this->getUser()->login($values['username'], $values['password'], $values['school']);
			$this->redirect('Marks:default');
		} catch (AuthenticationException $e) {
			if ($e->getCode() == Authenticator::INVALID_CREDENTIAL) {
				$form->addError('Nesprávne meno alebo heslo');
			}
		}
	}
        
}