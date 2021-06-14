<?php

	/* @var Stoic\Web\PageHelper $page */

?>
<?php $this->layout('shared::admin-master', ['page' => $page]); ?>

						<!-- start page title -->
						<div class="row">
							<div class="col-12">
								<div class="page-title-box">
									<div class="page-title-right">
										<form class="form-inline">
											<div class="form-group">
												<div class="input-group">
													<input type="text" class="form-control form-control-light" id="dash-daterange">
													<div class="input-group-append">
																				<span class="input-group-text bg-primary border-primary text-white">
																					<i class="mdi mdi-calendar-range font-13"></i>
																				</span>
													</div>
												</div>
											</div>
											<a href="javascript: void(0);" class="btn btn-primary ml-2">
												<i class="mdi mdi-autorenew"></i>
											</a>
											<a href="javascript: void(0);" class="btn btn-primary ml-1">
												<i class="mdi mdi-filter-variant"></i>
											</a>
										</form>
									</div>
									<h4 class="page-title">Dashboard</h4>
								</div>
							</div>
						</div>
						<!-- end page title -->

						<div class="row">
							<div class="col-12">
								<div class="row">
									<div class="col-lg-3">
										<div class="card widget-flat">
											<div class="card-body">
												<div class="float-right">
													<i class="mdi mdi-account-multiple widget-icon"></i>
												</div>
												<h5 class="text-muted font-weight-normal mt-0" title="Daily Active Users">DAU</h5>
												<h3 class="mt-3 mb-3"><?=number_format($dau ?? 0)?></h3>
												<p class="mb-0 text-muted">
													<span class="text-info mr-2"><i class="mdi mdi-arrow-up-bold"></i> 0.00%</span>
													<span class="text-nowrap">Since yesterday</span>
												</p>
											</div> <!-- end card-body-->
										</div> <!-- end card-->
									</div> <!-- end col-->

									<div class="col-lg-3">
										<div class="card widget-flat">
											<div class="card-body">
												<div class="float-right">
													<i class="mdi mdi-account-multiple widget-icon"></i>
												</div>
												<h5 class="text-muted font-weight-normal mt-0" title="Monthly Active Users">MAU</h5>
												<h3 class="mt-3 mb-3"><?=number_format($mau ?? 0)?></h3>
												<p class="mb-0 text-muted">
													<span class="text-info mr-2"><i class="mdi mdi-arrow-up-bold"></i> 0.00%</span>
													<span class="text-nowrap">Since last month</span>
												</p>
											</div> <!-- end card-body-->
										</div> <!-- end card-->
									</div> <!-- end col-->

									<div class="col-lg-3">
										<div class="card widget-flat">
											<div class="card-body">
												<div class="float-right">
													<i class="mdi mdi-account-multiple widget-icon"></i>
												</div>
												<h5 class="text-muted font-weight-normal mt-0" title="Total Verified Users">TVU</h5>
												<h3 class="mt-3 mb-3"><?=number_format($tvu ?? 0)?></h3>
												<p class="mb-0 text-muted">
													<span class="text-info mr-2"><i class="mdi mdi-arrow-up-bold"></i> 0.00%</span>
													<span class="text-nowrap">Since last month</span>
												</p>
											</div> <!-- end card-body-->
										</div> <!-- end card-->
									</div> <!-- end col-->

									<div class="col-lg-3">
										<div class="card widget-flat">
											<div class="card-body">
												<div class="float-right">
													<i class="mdi mdi-account-multiple widget-icon"></i>
												</div>
												<h5 class="text-muted font-weight-normal mt-0" title="Total Users">TU</h5>
												<h3 class="mt-3 mb-3"><?=number_format($tu ?? 0)?></h3>
												<p class="mb-0 text-muted">
													<span class="text-info mr-2"><i class="mdi mdi-arrow-up-bold"></i> 0.00%</span>
													<span class="text-nowrap">Since last month</span>
												</p>
											</div> <!-- end card-body-->
										</div> <!-- end card-->
									</div> <!-- end col-->
								</div> <!-- end row -->
							</div> <!-- end col -->
						</div>
						<!-- end row -->