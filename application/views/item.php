
		<!-- Body -->
		<div class="row">

			<div class="col-md-3">
				<!-- Side menu -->
			</div>

			<div class="col-md-9">
				<table class="table">
					<tr>
						<td>
							<div class="row">
								<div class="col-xs-4">
									<img id="shoe_image" src="<?php echo base_url().$shoeimagePath;?>" width="300" height="250">
								</div>
								<div class="col-xs-2">
									
								</div>
								<div class="col-sm-6 pull-right" style="float: right;">
									<h5 id="shoe_name"> <?php echo $product; ?> </h5>
									<p class="text">$<?= $shoeprice; ?></p>
									<form method="POST" action="<?php echo site_url("onlinepayment/handlePost"); ?>" class="form">
										<div class="=row">
											<input type="hidden" name="gtp_Amount" value="<?php echo $shoeprice ?>">
											<input type="hidden" name="gtp_Currency" value="<?php echo $currency ?>">
											<input type="hidden" name="gtp_TransDetails" value="<?php echo $product ?>">
											<input type="hidden" name="gtp_OrderId" value="<?php echo $order ?>">
											<input type="hidden" name="shoeimage" value="<?php echo $shoeimagePath ?>">
										</div>
										<div class="row form-group">
											<div class="col-sm-4">
												<label class="control-label">Your Name:</label>
											</div>
											<div class="col-sm-8">
												<input class="form-control" type="text" name="gtp_PayerName">
											</div>
										</div>
										<div class="row form-group">
											<div class="col-sm-8">
												
											</div>
											<div class="col-sm-4 form-group">
												<input class="form-control btn btn-success" type="submit" name="submit" value="Buy">
											</div>
										</div>
										<!-- Error message DIV -->
										<div class="row form-group">
											<div class="col-sm-4">
												
											</div>
											<?php 
												if(($msg != "") || ($msg != null)) {
													echo "<div class='col-sm-8 alert alert-danger'>".$msg."</div>";
												}
											?>
										</div>
									</form>
								</div>
							</div>
						</td>
					</tr>
				</table>

			</div>

		</div
