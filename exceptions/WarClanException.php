<?php
class WarClanException extends ClashTrackerException {
	public function __construct($description) {
		parent::__construct($description);
	}
}