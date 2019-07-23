<?php
defined("BASEPATH") OR exit("No direct script access is allowed!");

include APPPATH.'third_party/GTPayConnector.php';

class UpdateTransaction extends CI_Controller {
	// load the GTPay payment library
	protected $gtPayConnector;

	public function __construct() {

		parent::__construct();
		$this->gtPayConnector = new GTPayConnector();

		// load the helpers
		$this->load->helper('url');
	}

	public function index() {
		$this->doTransactionUpdate();
	}

	/**
		* method to receive the response from the gtpay payment gateway so as to update our db about the status of the transaction
	*/
	public function doTransactionUpdate() {

		//$gtPayConnector = new GTPayConnector();

		//var_dump($gtPayConnector);
		//the salt for hashing the SHA256
		$secret = "F3BAF2F79EFDD23B6407985EB4AD40DD";

		//set the secret
		$this->gtPayConnector->setSalt($secret);

		//set the data recieved via GET
		ksort($_GET);

		// get the jason response from the gtpay payment gateway
		//$reply = file_get_contents('php://input');

		//convert the data back into php object
		//$replyData = json_decode($reply);

		//var_dump($replyData);

		//add the received data via GET to the transactionData and the hash input*/
		foreach ($_GET as $key => $value) {
			# code...
			if(($key != "secure_hash") && ($key != "vpc_SecureHashType")) {
				$this->gtPayConnector->addTransactionFields($key, $value);
			}
		}

		// obtain the one-way hash of the recived data via GET
		$receivedHash = array_key_exists("secure_hash", $_GET) ? $_GET["secure_hash"] : "";
		//hash the received data
		$secureHash = $this->gtPayConnector->hashAllTransactionData();

		$responseDt = null;
		//compare if the two hashes are the same
		if($receivedHash != $secureHash) {

			//go to the error interface
			$data['error_msg'] = "Error";
			$data['page_titile'] = "xxOnline shoe | error";
			$data['responseData'] = $responseDt;


			//$this->load->view("error-page", $data);
			//$this->load->view("partials/footer");
		}
		else {
			// deal with the GET data
			$data['error_msg'] = "Good";
			$data['page_titile'] = "xxOnline shoe | good";

			$responseDt['message'] = $_GET['message'];
			$responseDt['transId'] = $_GET['transaction_id'];

			$data['responseData'] = $responseDt;

			//$this->load->view("error-page", $data);
			//$this->load->view("partials/footer");
		}
	}
}