<?php 
defined("BASEPATH") OR exit("No direct script access is allowed!");

/**
	This file stablishes connection nto the GTPay payment service
*/
class GtPayConnector {

	// some variables
	private $transactionData;
	private $hashInput;
	private $salt;
	private $saltType;

	// the constructor
	public function __construct() {
		$this->transactionData = "";
		$this->hashInput = "";
		$this->salt = "";
		$this->saltType = "";
	}

	/* Add data fields to the transactionData and the hash input*/
	public function addTransactionFields($field, $value) {

		// ensure there is a corresponding value for the supplied field
		if((strlen($field) == 0) && (strlen($value) == 0)) {
			return false;
		}

		$this->transactionData .= (($this->transactionData == "") ? "" : "&") . urldecode($field) ."=". urldecode($value);

		// Add the transaction  information to the data to be posted to the Payment Server
		// if($this->transactionData == "") {
		// 	$this->transactionData .= "".urldecode($field)."=".urldecode($value);
		// }
		// else {
		// 	$this->transactionData .= "&".urldecode($field)."=".urldecode($value);
		// }

		// Add the key's value to the hash input (only used for 3 party)
		$this->hashInput .= $field . "=" . $value . "&";

		return true;
	}

	/* Set the hashing algorithm */
	public function setSaltType($saltType) {
		$this->saltType = $saltType;
	}

	/* Set the hashing salt */
	public function setSalt($salt) {
		$this->salt = $salt;
	}

	/* Hash all the transaction data fileds */
	public function hashAllTransactionData() {
		// trim at &
		$this->hashInput = rtrim($this->hashInput, "&");
		
		return strtoupper(hash_hmac("SHA256", $this->hashInput.$this->saltType, pack("H*",$this->salt)));
	}

	/* Get the digital order / url with all the data */
	public function getDigitalOrderURL($paymentURL) {

		$redirectURL = $paymentURL."?".$this->transactionData;

		return $redirectURL;
	}
}
/* End of file GTpayConnector.php */