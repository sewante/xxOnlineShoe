<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function __construct() {
		parent::__construct();

		$this->load->helper('url');
	}
	public function index()
	{
		$data['page_title'] = "xx Online Shoe";
		$this->load->view('partials/header', $data);
		$this->load->view('buy-items');
		$this->load->view('partials/footer');
	}

	/**
		Function to load the iterface for the selected item from the buy-items.php interface
		@param itemName - the name of the item being chosen
	*/
	public function item($itemName) {

		if(strcmp($itemName, "aviator_wingtop") == 0){
			$data["shoename"] = $itemName;
			$data["shoeimagePath"] = "shoes/aviator_wingtop.jfif";
			$data["shoeprice"] = "50";
			$data["currency"] = "USD";
			$data["order"] = $this->generateOrderID();
			$data["product"] = "Aviator Wingtop (Germany)";
			$data["msg"] = "";

			$_SESSION['shoeimagePath'] = "shoes/aviator_wingtop.jfif";
		}
		elseif (strcmp($itemName, "wplanter") == 0) {
			# code...
			$data["shoename"] = $itemName;
			$data["shoeimagePath"] = "shoes/walkingPlanter.jfif";
			$data["shoeprice"] = "40";
			$data["currency"] = "USD";
			$data["order"] = $this->generateOrderID();
			$data["product"] = "Walking Planter (USA)";
			$data["msg"] = "";

			$_SESSION['shoeimagePath'] = "shoes/walkingPlanter.jfif";
		}
		elseif (strcmp($itemName, "tkorea") == 0) {
			# code...
			$data["shoename"] = $itemName;
			$data["shoeimagePath"] = "shoes/trend_korea.jpg";
			$data["shoeprice"] = "16";
			$data["currency"] = "USD";
			$data["order"] = $this->generateOrderID();
			$data["product"] = "Trend Korea (S. Korea)";
			$data["msg"] = "";

			$_SESSION['shoeimagePath'] = "shoes/trend_korea.jfif";
		}
		elseif (strcmp($itemName, "bsuede") == 0) {
			# code...
			$data["shoename"] = $itemName;
			$data["shoeimagePath"] = "shoes/blue_suede.jfif";
			$data["shoeprice"] = "36";
			$data["currency"] = "USD";
			$data["order"] = $this->generateOrderID();
			$data["product"] = "Blue Suede (Italy)";
			$data["msg"] = "";

			$_SESSION['shoeimagePath'] = "shoes/blue_suede.jfif";
		}

		//go to specific item
		$data['page_title'] = "xx Online Shoe | ".$itemName;
		$this->load->view('partials/header', $data);
		$this->load->view('item', $data);
		$this->load->view('partials/footer');

	}

	/**
		Generate the order ID
	*/
	public function generateOrderID() {
		$orderNumber = mt_rand(100, 1000000);

		return strval($orderNumber);
	}
}
