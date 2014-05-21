<?php $user = Sentry::getUser(); ?>

		<!-- Necessary markup, do not remove -->
		<div id="mws-sidebar-stitch"></div>
		<div id="mws-sidebar-bg"></div>

		<!-- Sidebar Wrapper -->
		<div id="mws-sidebar">

			<!-- Hidden Nav Collapse Button -->
			<div id="mws-nav-collapse">
				<span></span>
				<span></span>
				<span></span>
			</div>

			<!-- Main Navigation -->
			<div id="mws-navigation">
				<ul>
					<li class=""><a href="<?php echo URL::to('dashboard') ?>"><i class="icon-home"></i> Dashboard</a></li>
					<li class=""><a href="<?php echo URL::action('UsersController@getIndex') ?>"><i class="icon-user"></i> User</a></li>

					<?php if ($user->hasAccess('role.read')) : ?>
					<li class=""><a href="<?php echo URL::action('GroupsController@getIndex') ?>"><i class="icon-users"></i> User Role</a></li>
					<?php endif; ?>
					<li class=""><a href="<?php echo URL::to('apps') ?>"><i class="icon-database"></i> App</a></li>
					<li class=""><a href="<?php echo URL::to('brands') ?>"><i class="icon-television"></i> Brand</a></li>
					<li class="">
						<a href="#"><i class="icon-archive"></i> Collection</a>
						<ul class="<?php echo (Request::segment(1)==='collections') ? '' : 'closed' ?>">
							<li><a href="<?php echo URL::to('collections') ?>">Manage Collection</a></li>
						</ul>
					</li>
					<li class="">
						<a href="#"><i class="icon-mobile-phone"></i> Product</a>
						<ul class="<?php echo Request::segment(1)==='products' || Request::segment(1)==='variants' ? '' : 'closed' ?>">
							<!-- <li><a href="<?php echo URL::to('#'); ?>">New Lot (2)</a></li> -->
							<li><a href="<?php echo URL::to('products/search'); ?>">Search</a></li>
							<li><a href="<?php echo URL::to('products'); ?>">Product List</a></li>
							<li><a href="<?php echo URL::to('products/set-content'); ?>">Set Content</a></li>
							<li><a href="<?php echo URL::to('products/set-price'); ?>">Set Price</a></li>
							<li><a href="<?php echo URL::to('products/set-shipping'); ?>">Set Shipping Data</a></li>
							<li><a href="<?php echo URL::to('products/set-tag'); ?>">Set Tags</a></li>
							<li><a href="<?php echo URL::to('products/collection') ?>">Set Product Collection</a></li>
							<li><a href="<?php echo URL::to('products/approve'); ?>">Approve Product</a></li>
							<li><a href="<?php echo URL::to('products/approve/wait-for-publish'); ?>">Publish Content</a></li>
							<!-- <li><a href="<?php echo URL::to('variants'); ?>">Set Variants</a></li> -->
							<li><a href="<?php echo URL::to('products/trash'); ?>">Trash</a></li>
						</ul>
					</li>
					<li class="">
						<a href="#"><i class="icon-magic"></i> Promotion</a>
						<ul class="<?php echo ( in_array(Request::segment(1), array('campaigns', 'discount-campaigns')) ) ? '' : 'closed' ?>">
							<li><a href="<?php echo URL::to('campaigns') ?>">Campaigns</a></li>
							<li><a href="<?php echo URL::to('discount-campaigns') ?>">Flashsale/iTruemart TV</a></li>
						</ul>
					</li>
					<li class="">
						<a href="#"><i class="icon-file"></i> Policy</a>
						<ul class="<?php echo Request::segment(1)==='policies' ? '' : 'closed' ?>">
							<li style="display:none;"><a href="<?php echo URL::to('policies/vendors') ?>">Set Policy</a></li>
                            <li><a href="<?php echo URL::to('policies') ?>">Policy Template</a></li>
                            <li><a href="<?php echo URL::to('policies/assigns') ?>">Assign Policy</a></li>
						</ul>
					</li>
					<li class=""><a href="#"><i class="icon-list"></i> Order</a>
						<ul class="<?php echo Request::segment(1)==='orders' ? '' : 'closed' ?>">
							<li><a href="<?php echo URL::to('orders') ?>"> Track Orders</a></li>
							<li><a href="<?php echo URL::to('orders/discount-tracker') ?>"> Discount Tracker</a></li>
						</ul>
					</li>
					<li class=""><a href="#"><i class="icon-rocket"></i> Shipping</a>
						<ul class="<?php echo Request::segment(1)==='shipping' ? '' : 'closed' ?>">
							<li><a href="<?php echo URL::to('shipping/method') ?>">Manage Shipping Method</a></li>
							<li><a href="<?php echo URL::to('shipping/boxes') ?>">Manage Shipping Box</a></li>
							<li><a href="<?php echo URL::to('shipping/payment-methods') ?>"> Manage Payment Method</a></li>
							<li><a href="<?php echo URL::to('shipping/delivery-area') ?>"> Delivery Area</a></li>
							<li><a href="<?php echo URL::to('shipping/set-method/stock') ?>"> Set by Stock</a></li>
							<li><a href="<?php echo URL::to('shipping/set-method/vendor') ?>"> Set by Vendor</a></li>
							<li><a href="<?php echo URL::to('shipping/set-method/product') ?>"> Set by Product</a></li>
						</ul>
					</li>
					<li class=""><a href="<?php echo URL::to('banners/groups') ?>"><i class="icon-rocket"></i> Banner Management</a></li>
					<li class=""><a href="<?php echo URL::to('shops') ?>"><i class="icon-archive"></i> Shop Management </a></li>
					<li class=""><a href="<?php echo URL::to('holidays') ?>"><i class="icon-calendar"></i> Holidays Management </a></li>
<?php /*
					<li><a href="dashboard.html"><i class="icon-home"></i> Dashboard</a></li>
					<li><a href="charts.html"><i class="icon-graph"></i> Charts</a></li>
					<li><a href="calendar.html"><i class="icon-calendar"></i> Calendar</a></li>
					<li><a href="files.html"><i class="icon-folder-closed"></i> File Manager</a></li>
					<li class="active"><a href="table.html"><i class="icon-table"></i> Table</a></li>
					<li>
						<a href="#"><i class="icon-list"></i> Forms</a>
						<ul>
							<li><a href="form_layouts.html">Layouts</a></li>
							<li><a href="form_elements.html">Elements</a></li>
							<li><a href="form_wizard.html">Wizard</a></li>
						</ul>
					</li>
					<li><a href="widgets.html"><i class="icon-cogs"></i> Widgets</a></li>
					<li><a href="typography.html"><i class="icon-font"></i> Typography</a></li>
					<li><a href="grids.html"><i class="icon-th"></i> Grids &amp; Panels</a></li>
					<li><a href="gallery.html"><i class="icon-pictures"></i> Gallery</a></li>
					<li><a href="error.html"><i class="icon-warning-sign"></i> Error Page</a></li>
					<li>
						<a href="icons.html">
							<i class="icon-pacman"></i> Icons <span class="mws-nav-tooltip">2000+</span>
						</a>
					</li>
*/ ?>
				</ul>
			</div>

		</div>
