<?php
defined("BASEPATH") OR exit("No direct script access is allowed!");

include APPPATH.'third_party/GTPayConnector.php';
include APPPATH.'third_party/VpcConfig.php';

class UpdateTransaction extends CI_Controller {
	// load the GTPay payment library
	protected $gtPayConnector;
	protected $vpcConfig;

	public function __construct() {

		parent::__construct();
		$this->gtPayConnector = new GTPayConnector();
		$this->vpcConfig = new VpcConfig();

		// load the helpers
		$this->load->helper('url');
	}

	/**
		Method called by default when the controller is invoked
		it call the doTransactionUpdate method, to update the transaction
	*/
	public function index() {
		$this->doTransactionUpdate();
	}

	/**
		Method to receive the response from the gtpay payment gateway so as to update our db about the status of the transaction
	*/
	public function doTransactionUpdate() {

		//get the vpc xml object
		$vpcXMLConf = "";
		$vpcXMLConf  = $this->vpcConfig->loadVpcConfig();

		// confirm that the configurations of vpc were loaded;
		if($vpcXMLConf == null) {

			//go to the error interface
			$data['error_msg'] = "Could not Load the Virtual Payment client Details";
			$data['page_titile'] = "xxOnline shoe | error";
			$data['responseData'] = "";

			$this->load->view("error-page", $data);
			$this->load->view("partials/footer");
			return;
		}

		//get the vpc details
		$vpcSalt = $this->vpcConfig->getVpvSalt($vpcXMLConf);
		$vpcSaltType = $this->vpcConfig->getVpcSaltType($vpcXMLConf);

		//set the salt and the salt type
		$this->gtPayConnector->setSalt($vpcSalt);
		$this->gtPayConnector->setSaltType($vpcSaltType);

		// get the jason response from the gtpay payment gateway
		$reply = file_get_contents('php://input');

		//convert the data back into php object
		$replyDataObject = json_decode($reply);

		// cast the reply data object to an array
		$replyData = (array)$replyDataObject;

		ksort($replyData);

		//add the received data via GET to the transactionData and the hash input*/
		foreach ($replyData as $key => $value) {
			# code...
			if(($key != "secure_hash") && ($key != "vpc_SecureHashType")) {
				$this->gtPayConnector->addTransactionFields($key, $value);

			}
		}

		// obtain the one-way hash of the recived data via GET
		$receivedHash = array_key_exists("secure_hash", $replyData) ? $replyData['secure_hash'] : "";
		//hash the received data
		$secureHash = $this->gtPayConnector->hashAllTransactionData();


		$responseDt = null;
		//compare if the two hashes are the same
		if($receivedHash != $secureHash) {

			//go to the error interface
			$data['error_msg'] = "Error";
			$data['page_titile'] = "xxOnline shoe | error";
			$data['responseData'] = $responseDt;
			var_dump($data);

			//$this->load->view("error-page", $data);
			//$this->load->view("partials/footer");
		}
		else {
			// deal with the GET data
			$data['error_msg'] = "Good";
			$data['page_titile'] = "xxOnline shoe | good";

			$responseDt['message'] = $replyData['message'];
			$responseDt['transId'] = $replyData['transaction_id'];

			$data['responseData'] = $responseDt;
			var_dump($data);

			//$this->load->view("error-page", $data);
			//$this->load->view("partials/footer");
		}
	}
}