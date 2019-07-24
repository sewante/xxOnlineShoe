<?php 
defined("BASEPATH") OR exit("No direct script access is allowed!");

class VpcConfig {

	private $vpcurl;
	private $customerCode;
	private $salt;
	private $saltType;
	private $vpcConfigFile;

	// define the constuctor
	public function __construct() {
		$this->vpcurl = "";
		$this->customerCode = "";
		$this->salt = "";

		$this->vpcConfigFile = APPPATH.'config/vpcConfigurations.xml';
	}

	/**
		Method to process the vpcConfig.xml file
		@return vpcXMLConf : the object representation of the xml configuration file for the vpc
	*/
	public function loadVpcConfig() {

		$vpcXMLConf = simplexml_load_file($this->vpcConfigFile);

		if($vpcXMLConf == null) {
			return null;
		}
		else {
			return $vpcXMLConf;
		}
	}

	/**
		Method to return the vpc url
		@param vpcXMLConf : the xml object representation of the vpc xml configurations file
		@return vpcurl : the url of the vpc on to which the transaction (payement details) are posted
	*/
	public function getVpcURL($vpcXMLConf) {

		if($vpcXMLConf == null) {
			return null;
		}
		$vpcurl = $vpcXMLConf->vpcURL;
		return $vpcurl;
	}
	/**
		Method to return the salt type
		@param vpcXMLConf : the xml object representation of the vpc xml configurations file
		@return saltType : the type of hashing algorithm used to create the hash the data
	*/
	public function getVpcSaltType($vpcXMLConf) {

		if($vpcXMLConf == null) {
			return null;
		}
		$saltType = $vpcXMLConf->vpcSaltType;

		return $saltType;
	}

	/**
		Method to return the salt
		@param vpcXMLConf : the xml object representation of the vpc xml configurations file
		@return salt : the hash key that is used during hashing and unhashing the data
	*/
	public function getVpvSalt($vpcXMLConf) {

		if($vpcXMLConf == null) {
			return null;
		}
		$salt = $vpcXMLConf->vpcSalt;

		return $salt;
	}

	/**
		Method to return the customer code
		@param vpcXMLConf : the xml object representation of the vpc xml configurations file
		@return customerCode : the unique identifier for the customer (for identification on the vpc side)
	*/
	public function getCustomerCode($vpcXMLConf) {

		if($vpcXMLConf == null) {
			return null;
		}
		$customerCode = $vpcXMLConf->marchantCode;

		return $customerCode;	
	}
}