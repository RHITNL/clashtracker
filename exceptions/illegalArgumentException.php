<?php
class illegalArgumentException extends Exception {
	public function __construct($description) {
		parent::__construct($description);
	}
}