<?
class LootAmountException extends Exception {
	public function __construct($description, $min) {
		parent::__construct($description);
		$this->min = $min;
	}

	public function getMinimumLoot(){
		return $this->min;
	}
}