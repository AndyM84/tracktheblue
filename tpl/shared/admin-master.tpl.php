<?php

	/**
	 * @var \Stoic\Web\PageHelper $page
	 * @var \Zibings\UserProfile $profile
	 * @var \AndyM84\Config\ConfigContainer $settings
	 * @var \Zibings\UserRoles $userRoles
	 * @var \Zibings\User $user
	 */

	use Zibings\SettingsStrings;
	use Zibings\UserEvents;

?>
<!DOCTYPE html>

<html lang="en">
	<head>
		<meta charset="utf-8"/>
		<title><?=$page->getTitle()?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description"/>
		<meta content="Coderthemes" name="author"/>
		<!-- App favicon -->
		<link rel="shortcut icon" href="<?=$page->getAssetPath('~/admin/assets/images/favicon.ico')?>">

		<!-- third party css -->
		<link href="<?=$page->getAssetPath('~/admin/assets/css/vendor/jquery-jvectormap-1.2.2.css')?>" rel="stylesheet" type="text/css"/>
		<!-- third party css end -->

		<!-- App css -->
		<link href="<?=$page->getAssetPath('~/admin/assets/css/icons.min.css')?>" rel="stylesheet" type="text/css"/>
		<link href="<?=$page->getAssetPath('~/admin/assets/css/app-modern.min.css')?>" rel="stylesheet" type="text/css" id="light-style"/>
		<link href="<?=$page->getAssetPath('~/admin/assets/css/app-modern-dark.min.css')?>" rel="stylesheet" type="text/css" id="dark-style"/>
<?=$this->section('stylesheets')?>
	</head>

	<body class="loading" data-layout="detached" data-layout-config='{"leftSidebarCondensed":false,"darkMode":true, "showRightSidebarOnStart": false}'>
		<!-- Topbar Start -->
		<div class="navbar-custom topnav-navbar topnav-navbar-dark">
			<div class="container-fluid">

				<!-- LOGO -->
				<a href="index.html" class="topnav-logo">
					<span class="topnav-logo-lg">
						<img src="<?=$page->getAssetPath('~/admin/assets/images/logo-light.png')?>" alt="" height="16">
					</span>
					<span class="topnav-logo-sm">
						<img src="<?=$page->getAssetPath('~/admin/assets/images/logo_sm.png')?>" alt="" height="16">
					</span>
				</a>

				<ul class="list-unstyled topbar-right-menu float-right mb-0">

					<li class="dropdown notification-list d-lg-none">
						<a class="nav-link dropdown-toggle arrow-none" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
							<i class="dripicons-search noti-icon"></i>
						</a>
						<div class="dropdown-menu dropdown-menu-animated dropdown-lg p-0">
							<form class="p-3">
								<input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
							</form>
						</div>
					</li>

					<li class="dropdown notification-list">
						<a class="nav-link dropdown-toggle arrow-none" data-toggle="dropdown" href="#" id="topbar-notifydrop" role="button" aria-haspopup="true" aria-expanded="false">
							<i class="dripicons-bell noti-icon"></i>
							<!--<span class="noti-icon-badge"></span>-->
						</a>
						<div class="dropdown-menu dropdown-menu-right dropdown-menu-animated dropdown-lg" aria-labelledby="topbar-notifydrop">

							<!-- item-->
							<div class="dropdown-item noti-title">
								<h5 class="m-0">
									<span class="float-right">
										<a href="javascript: void(0);" class="text-dark">
											<small>Clear All</small>
										</a>
									</span> Notification
								</h5>
							</div>

							<div style="max-height: 230px;" data-simplebar>
								&nbsp;
							</div>

							<!-- All-->
							<a href="javascript:void(0);" class="dropdown-item text-center text-primary notify-item notify-all">
								View All
							</a>

						</div>
					</li>

					<li class="dropdown notification-list">
						<a class="nav-link dropdown-toggle nav-user arrow-none mr-0" data-toggle="dropdown" id="topbar-userdrop" href="#" role="button" aria-haspopup="true" aria-expanded="false">
							<span>
								<span class="account-user-name"><?=$profile->displayName?></span>
								<span class="account-position"><?=$user->email?></span>
							</span>
						</a>
						<div class="dropdown-menu dropdown-menu-right dropdown-menu-animated topbar-dropdown-menu profile-dropdown" aria-labelledby="topbar-userdrop">
							<!-- item-->
							<div class=" dropdown-header noti-title">
								<h6 class="text-overflow m-0">Welcome <?=$profile->displayName?>!</h6>
							</div>

							<!-- item-->
							<a href="<?=$page->getAssetPath('~/account.php')?>" class="dropdown-item notify-item">
								<i class="mdi mdi-account-circle mr-1"></i>
								<span>My Account</span>
							</a>

							<!-- item-->
							<a href="<?=$page->getAssetPath('~/logout.php')?>" class="dropdown-item notify-item">
								<i class="mdi mdi-logout mr-1"></i>
								<span>Logout</span>
							</a>

						</div>
					</li>

				</ul>
				<a class="button-menu-mobile disable-btn">
					<div class="lines">
						<span></span>
						<span></span>
						<span></span>
					</div>
				</a>
			</div>
		</div>
		<!-- end Topbar -->

		<!-- Start Content-->
		<div class="container-fluid">

			<!-- Begin page -->
			<div class="wrapper">

				<!-- ========== Left Sidebar Start ========== -->
				<div class="left-side-menu left-side-menu-detached">

					<div class="leftbar-user">
						<a href="javascript: void(0);">
							<span class="leftbar-user-name">Administration</span>
						</a>
					</div>

					<!--- Sidemenu -->
					<ul class="metismenu side-nav">

						<li class="side-nav-title side-nav-item">Navigation</li>

						<li class="side-nav-item">
							<a href="<?=$page->getAssetPath('~/admin/index.php')?>" class="side-nav-link">
								<i class="uil-home-alt"></i>
								<span> Dashboard </span>
							</a>
						</li>

						<li class="side-nav-item">
							<a href="<?=$page->getAssetPath('~/admin/users.php')?>" class="side-nav-link">
								<i class="uil-user-circle"></i>
								<span> User Management </span>
							</a>
						</li>

						<li class="side-nav-item">
							<a href="<?=$page->getAssetPath('~/home.php')?>" class="side-nav-link">
								<i class="uil-window-maximize"></i>
								<span> Front-End </span>
							</a>
						</li>
					</ul>

					<div class="clearfix"></div>
					<!-- Sidebar -left -->

				</div>
				<!-- Left Sidebar End -->

				<div class="content-page">
					<div class="content">
						<?=$this->section('content')?>
					</div> <!-- End Content -->

					<!-- Footer Start -->
					<footer class="footer">
						<div class="container-fluid">
							<div class="row">
								<div class="col-md-6">
									&nbsp;
								</div>
								<div class="col-md-6 text-right">
									<?=(new \DateTime('now', new \DateTimeZone('UTC')))->format('Y')?> &copy; <?=$settings->get(SettingsStrings::SITE_NAME, 'ZSF')?> - System Version: v<?=$settings->get(SettingsStrings::SYSTEM_VERSION)?>
								</div>
							</div>
						</div>
					</footer>
					<!-- end Footer -->

				</div> <!-- content-page -->

			</div> <!-- end wrapper-->
		</div>
		<!-- END Container -->


		<!-- Right Sidebar -->
		<div class="right-bar">

			<div class="rightbar-title">
				<a href="javascript:void(0);" class="right-bar-toggle float-right">
					<i class="dripicons-cross noti-icon"></i>
				</a>
				<h5 class="m-0">Settings</h5>
			</div>

			<div class="rightbar-content h-100" data-simplebar>

				<div class="p-3">
					<div class="alert alert-warning" role="alert">
						<strong>Customize </strong> the overall color scheme, sidebar menu, etc.
					</div>

					<!-- Settings -->
					<h5 class="mt-3">Color Scheme</h5>
					<hr class="mt-1"/>

					<div class="custom-control custom-switch mb-1">
						<input type="radio" class="custom-control-input" name="color-scheme-mode" value="light"
									 id="light-mode-check" checked/>
						<label class="custom-control-label" for="light-mode-check">Light Mode</label>
					</div>

					<div class="custom-control custom-switch mb-1">
						<input type="radio" class="custom-control-input" name="color-scheme-mode" value="dark"
									 id="dark-mode-check"/>
						<label class="custom-control-label" for="dark-mode-check">Dark Mode</label>
					</div>

					<!-- Left Sidebar-->
					<h5 class="mt-4">Left Sidebar</h5>
					<hr class="mt-1"/>

					<div class="custom-control custom-switch mb-1">
						<input type="radio" class="custom-control-input" name="compact" value="fixed" id="fixed-check"
									 checked/>
						<label class="custom-control-label" for="fixed-check">Scrollable</label>
					</div>

					<div class="custom-control custom-switch mb-1">
						<input type="radio" class="custom-control-input" name="compact" value="condensed"
									 id="condensed-check"/>
						<label class="custom-control-label" for="condensed-check">Condensed</label>
					</div>

					<button class="btn btn-primary btn-block mt-4" id="resetBtn">Reset to Default</button>

					<a href="https://themes.getbootstrap.com/product/hyper-responsive-admin-dashboard-template/"
						 class="btn btn-danger btn-block mt-3" target="_blank"><i class="mdi mdi-basket mr-1"></i> Purchase
						Now</a>
				</div> <!-- end padding-->

			</div>
		</div>

		<div class="rightbar-overlay"></div>
		<!-- /Right-bar -->

		<!-- bundle -->
		<script src="<?=$page->getAssetPath('~/admin/assets/js/vendor.min.js')?>"></script>
		<script src="<?=$page->getAssetPath('~/admin/assets/js/app.min.js')?>"></script>

		<!-- third party js -->
		<script src="<?=$page->getAssetPath('~/admin/assets/js/vendor/apexcharts.min.js')?>"></script>
		<script src="<?=$page->getAssetPath('~/admin/assets/js/vendor/jquery-jvectormap-1.2.2.min.js')?>"></script>
		<script src="<?=$page->getAssetPath('~/admin/assets/js/vendor/jquery-jvectormap-world-mill-en.js')?>"></script>
		<!-- third party js ends -->

		<!-- page variables -->
		<script type="text/javascript">
			const apiBaseUrl = "<?=$page->getAssetPath('~/api/1/')?>";
			const authToken  = "<?=base64_encode("{$_SESSION[UserEvents::STR_SESSION_USERID]}:{$_SESSION[UserEvents::STR_SESSION_TOKEN]}")?>";
		</script>
		<!-- page variables end -->

		<!-- utility js -->
		<script src="<?=$page->getAssetPath('~/assets/js/utils.js')?>"></script>
		<script type="text/javascript">
			const utils = new Utilities(apiBaseUrl, authToken);
		</script>
		<!-- utility js ends -->

<?=$this->section('scripts')?>
	</body>
</html>