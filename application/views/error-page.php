<!DOCTYPE html>
<html>
<head>
	<title><?= $page_titile; ?></title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/bootstrap.min.css">
</head>
<body>
	<div class="container">
		<div class="jumbotron">
			<h2 class="text">An Error Occured</h2>
			<div class="alert alert-danger">
				<p>
					<?php 
						if(($responseData != null) || ($responseData != "")) {

							var_dump($responseData);
						}
						else {

							echo $error_msg;
						}
					 ?>
				</p>
				
			</div>
			
		</div>
		