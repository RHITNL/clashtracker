<?php
class apiException extends Exception {
	public function __construct($reason, $message) {
		$this->reason = $reason;
		$this->message = $message;
	}

	public function getReasonMessage(){
		if(isset($this->message) && isset($this->reason)){
			return $this->message . ' - ' . $this->reason;
		}elseif(isset($this->message)){
			return $this->message;
		}elseif(isset($this->reason)){
			return $this->reason;
		}else{
			return 'An exception occured while contacting API.';
		}
	}
}