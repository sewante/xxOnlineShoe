<?php 
defined('BASEPATH') OR exit('No direct script access is allowed!');

/**
	File handles online payment using the GTPay online payment third party
*/
class OnlinePayment extends CI_Controller {

	public function __construct() {
		parent::__construct();

		$this->load->helper('url');

		// load the GTPay payment library
		include APPPATH.'third_party/GTPayConnector.php';
		include APPPATH.'third_party/VpcConfig.php';

		//load models
		$this->load->model("transactions");
	}

	/**
		generate orderID
	*/

	/**
		Validate Post data
	*/
	public function validatePostData () {
		$message = "success";

		// check if the payer's name has been provided
		$customerName = $this->input->post('gtp_PayerName'); //$_POST['gtp_PayerName'];
		if(($customerName == "") || ($customerName == null)) {
			$message = "Provide Your name please!";
			return $message;
		}

		// validate other fields

		return $message;
	}

	/**
		handle post request
		collect post data from the form and call processPayment()
	*/
	public function handlePost() {

		$data["msg"] = "";
		$data["shoename"] = $this->input->post('gtp_TransDetails');
		$data["shoeimagePath"] = $this->input->post('shoeimage');
		$data["shoeprice"] = $this->input->post('gtp_Amount');
		$data["currency"] = $this->input->post('gtp_Currency');
		$data["order"] = $this->input->post('gtp_OrderId');
		$data["product"] = $this->input->post('gtp_TransDetails');

		$message = $this->validatePostData();
		if(strcmp($message, "success") != 0) {
			$data["msg"] = $message;
			$data['page_title'] = "xxOnline shoe | error";

			$this->load->view('partials/header', $data);
			$this->load->view('item', $data);
			$this->load->view('partials/footer');
		}

		else {
			$this->processPayment();
		}
	}

	/**
		gtpay integration
		process the payment details and send them to the vpc
	*/
	public function processPayment() {

		//create the GTPayConnector instance
		$gtpayConnector = new GTPayConnector();

		//create the VpcConfig instance
		$vpcConfig = new VpcConfig();

		//get the vpc xml object
		$vpcXMLConf = "";
		$vpcXMLConf  = $vpcConfig->loadVpcConfig();

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
		$vpcUrl = $vpcConfig->getVpcURL($vpcXMLConf);
		$vpcSalt = $vpcConfig->getVpvSalt($vpcXMLConf);
		$vpcSaltType = $vpcConfig->getVpcSaltType($vpcXMLConf);
		$marchantcode = $vpcConfig->getCustomerCode($vpcXMLConf);

		//the transaction log
		$transactionLog = array();

		// set the salt
		$gtpayConnector->setSalt($vpcSalt);

		//remove the data that you dont need to send to the payment client
		unset($_POST['shoeimage']);
		unset($_POST['submit']);
		// add the customer code to the post data
		$_POST['gtp_CustomerCode'] = $marchantcode;

		//sort the post data before encrypting
		ksort($_POST);

		//add the Virtual Payment Client post data to the transaction data
		foreach ($_POST as $key => $value) {
			
			if(strlen($value) > 0) {
				$gtpayConnector->addTransactionFields($key, $value);

				// add the post data to the transactionLog
				$transactionLog[$key] = $value;
			}
		}
		//get the date and time when the payment request is made
		$date = new DateTime();
		$date->setTimeZone(new DateTimeZone('UTC'));
		$transactionDate = $date->format('Y-m-d\TH-i-s\Z');

		//add the date of the transaction
		$transactionLog["transactDate"] = $transactionDate;

		//log the trasaction on your database
		$this->transactions->logTransaction($transactionLog);

		// set the salt type
		$gtpayConnector->setSaltType($vpcSaltType);

		// make oneway hash of the Transaction and add it to the digital order
		$transactionHash = $gtpayConnector->hashAllTransactionData();

		$gtpayConnector->addTransactionFields("gtp_TransDate", $transactionDate);
		$gtpayConnector->addTransactionFields("gtp_SecureHash", $transactionHash);
		$gtpayConnector->addTransactionFields("gtp_SecureHashType", $vpcSaltType);

		//obtain the redirection url
		$vpcUrl = $gtpayConnector->getDigitalOrderURL($vpcUrl);

		// send the payment request
		header("Location: ".$vpcUrl);
	}

}