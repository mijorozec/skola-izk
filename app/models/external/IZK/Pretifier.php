<?php

namespace Ecole\External\IZK;

use Nette\Object;

class Pretifier extends Object {
	
	protected $conn, $body;

	public function __construct($conn = NULL) {
		$this->conn = $conn;
	}

	public function getLastMarksChange() {
		$body = $this->getBody();

		$regexp = "#Posledná zmena známok: <b><font[^>]+>([^<]+)</font></b>#";
		if (preg_match($regexp, $body, $matches)) {
			return $matches[1];
		} else {
			return FALSE;
		}
	}

    public function getMarks() {
		$body = $this->getBody();

		$regexp = "#<tr><td[^>]+><b><font[^>]+>([^<]+)</font></b></td><td[^>]+>([^<]+)</td></tr>#";
		preg_match_all($regexp, $body, $matches);

		$return = array();
		for ($x = 0; $x < count($matches[1]); $x++) {
			$name = trim($matches[1][$x]);
			$origMarks = trim($matches[2][$x], ", \t\n\r\0\x0B");
			$marks = $this->getIndividualMarks($origMarks);

			$return[$name] = array(
				'readable' => $marks,
				'original' => $origMarks,
			);
		}

		return $return;
	}

	private function getIndividualMarks($marks) {
		if (!$marks) return array();

		$regexp = "#([0-9,\.]+)\/([0-9,\.]+)#";
		preg_match_all($regexp, $marks, $matches);

		$return = array();
		for ($x = 0; $x < count($matches[1]); $x++) {
			$return[] = array(
				$this->formatMark($matches[1][$x]),
				$this->formatMark($matches[2][$x]),
			);
		}

		return $return;
	}

	private function formatMark($mark) {
		return floatval(strtr($mark, ',', '.'));
	}

	protected function getBody() {
		if (!$this->body)
			$this->body = $this->conn->load();
		return $this->body;
	}

	public function getConnection() {
		return $this->conn;
	}

	public function setConnection(Connection $conn) {
		$this->conn = $conn;
	}
}
