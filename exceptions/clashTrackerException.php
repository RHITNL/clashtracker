<?php
class ClashTrackerException extends Exception {
	public function __construct($description) {
        parent::__construct($description);
    }
}