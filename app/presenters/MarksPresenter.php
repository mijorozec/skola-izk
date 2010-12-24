<?php

use Nette\Application\AppForm;

class MarksPresenter extends BasePresenter {

	private $conn;

	public function startup() {
		parent::startup();

		if ($this->getUser()->loggedIn) {
			$data = $this->getUser()->getIdentity()->getData();

			$this->conn = $conn = new Ecole\External\IZK\Connection($data['username'], $data['password'], $data['school']);
			$conn->open();
		}
	}

	public function shutdown($response) {
		if ($this->conn) $this->conn->close();

		parent::shutdown($response);
	}

	public function actionDefault() {
		if (!$this->getUser()->loggedIn || !$this->conn)
				$this->redirect('Sign:in');
	}

	public function renderDefault() {
		$pretifier = new Ecole\External\IZK\Pretifier($this->conn);
		$this->template->allMarks = $pretifier->getMarks();
		$this->template->lastChange = $pretifier->getLastMarksChange();
	}

	public function handleReload() {
		$this->invalidateControl('marks');
		if (!$this->ajax) $this->redirect('this');
	}
        
}