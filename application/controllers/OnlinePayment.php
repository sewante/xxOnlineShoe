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

			$this->load->view('partials/header');
			$this->load->view('item', $data);
			$this->load->view('partials/footer');
		}

		else {
			$this->processPayment();
		}
	}

	/**
		gtpay integration
	*/
	public function processPayment() {

		//create the GTPayConnector istance
		$gtpayConnector = new GTPayConnector();

		// the secret/salt for hashing with SHA256/salt type
		$secret = "F3BAF2F79EFDD23B6407985EB4AD40DD";
		$marchantcode = "254";
		$gtpayURL = "http://192.168.2.32/ABGTPAY/GTPAY/GTPay_v2/GTPay.aspx";

		//the transaction log
		$transactionLog = array();

		// prepare the salt
		$gtpayConnector->setSalt($secret);

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
		$gtpayConnector->setSaltType("SHA256");

		// make oneway hash of the Transaction and add it to the digital order
		$transactionHash = $gtpayConnector->hashAllTransactionData();

		$gtpayConnector->addTransactionFields("gtp_TransDate", $transactionDate);
		$gtpayConnector->addTransactionFields("gtp_SecureHash", $transactionHash);
		$gtpayConnector->addTransactionFields("gtp_SecureHashType", "SHA256");

		//obtain the redirection url
		$gtpayURL = $gtpayConnector->getDigitalOrderURL($gtpayURL);

		// send the payment request
		header("Location: ".$gtpayURL);

	}

}