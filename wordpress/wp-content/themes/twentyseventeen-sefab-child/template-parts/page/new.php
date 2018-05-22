<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

?>
<img class="banner	" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/logo_light_1_800x200.png" />

<div id="login-button-container" class="container-fluid login-container <?php echo ($_GET['login'] || $_GET['register'] ||$_GET['verify'] ||$_GET['registration'] ) ? 'hidden' : ''; ?>">
	<div class="row">
		<div class="col-md-12">
			<button class="color-primary" onclick="showSignIn()">Sign In</button>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<button class="color-white" onclick="showRegistration()">Register</button>
		</div>
	</div>
</div>



<div id="sign-in-container" class="container-fluid login-container <?php echo (!$_GET['login']) ? 'hidden' : ''; ?>">
	<form name="login-form" id="login-form" action="<?php get_site_url(); ?>/wordpress/wp-admin/admin-post.php" method="POST">
		<input type="hidden" value="validate_user" name="action">

		<div class="row">
			<div class="col-md-12">
				<p class="center <?php echo ($_GET['login'] === 'failed') ? 'error' : 'hidden'; ?>">Invalid Phone Number or Password</p>

				<p class="center <?php echo ($_GET['login'] === 'empty') ? 'error' : 'hidden'; ?>">Enter your credentials</p>
			</div>	
		</div>

		<div class="row">
			<div class="col-md-12">

			<label class="center color-white" >User Name</label>

			<input id="user_login" type="text" size="20" value="" name="log" placeholder="07xx-xxx-xxx"  class="bfh-phone" data-format="ddd-ddd-dddd"/>
			
				<!--input id="user_login" type="text" size="20" value="" name="log" placeholder="Phone Number"/-->
				
			
				<input id="user_pass" type="password" size="20" value="" name="pwd" placeholder="Password"/>
			</div>
		</div>


		<div class="row">
			<div class="col-md-12 form-check">
				<input type="checkbox" class="form-check-input" id="rememberme" value="forever" name="rememberme">
				<label class="form-check-label color-white" for="rememberme">Remember Me</label>
			</div>
		</div>
	</form>
	<div class="row">
		<div class="col-md-6">
			<button class="color-white" onclick="showWelcome()">Back</button>
		</div>
		<div class="col-md-6">
			<button class="color-primary" form="login-form" id="wp-submit" type="submit" name="wp-submit">Sign In</button>
		</div>
	</div>
</div>

<div id="register-container" class="container-fluid login-container <?php echo (!$_GET['register']) ? 'hidden' : ''; ?>">
	<form id="register-form"  action="<?php get_site_url(); ?>/wordpress/wp-admin/admin-post.php" method="POST">
		<input type="hidden" name="action" value="send_code" />
		<div class="row">
			<div class="col-md-12">
				<p class="error center <?php echo ($_GET['register'] === 'false') ? '' : 'hidden'; ?>">Phone number not found</p>
				<p class="error center <?php echo ($_GET['register'] === 'returnee') ? '' : 'hidden'; ?>">This account is already set.</p>				
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
			<label class="center color-white" >Phone Number</label>
			<input id="user_phone_number" type="text" size="20" value="" name="phoneNumber" placeholder="07xx-xxx-xxx" class="bfh-phone" data-format="dddd-ddd-ddd"/>
				
			</div>
		</div>
	</form>
	<div class="row">
		<div class="col-md-6">
			<button class="color-white" onclick="showWelcome()">Back</button>
		</div>
		<div class="col-md-6">
			<button type="submit" form="register-form" class="color-primary">Send Verification Code</button>
		</div>
	</div>
</div>

<div id="verify-container" class="container-fluid login-container <?php echo ($_GET['verify']) ? '' : 'hidden'; ?>">

<form id="verify-form"  action="<?php get_site_url(); ?>/wordpress/wp-admin/admin-post.php" method="POST">
		<input type="hidden" name="action" value="verify_code" />
		
	
	<div class="row">
		<div class="col-md-12">
			<!--p class="center color-white"-->
			<p class="center color-white <?php echo ($_GET['verify'] === 'pending') ? '' : 'hidden'; ?>">Verification Code Sent</p>
		</div>
	</div>	
	

	<div class="row">
			<div class="col-md-12">
				<p class="error center <?php echo ($_GET['verify'] === 'false') ? '' : 'hidden'; ?>">Verification Code Incorrect</p>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<input id="user_verification_code" type="text" value="" name="verificationCode" placeholder="Verification Code" />
			</div>
		</div>
	</form>
<form id="resend-form"  action="<?php get_site_url(); ?>/wordpress/wp-admin/admin-post.php" method="POST">
		<input type="hidden" name="action" value="resend_code" />
		
	<div class="row">
		<div class="col-md-12">
		
			<button class="color-primary" form="resend-form">Resend Verification Code</button>			
		</div>
	</div>
	
	</form>
	
	
	<div class="row">
		<div class="col-md-12">
			<button class="color-primary" type="submit" form="verify-form">Confirm</button>
		</div>
	</div>
</div>
<div id="registration-container" class="container-fluid login-container <?php echo ($_GET['registration']) ? '' : 'hidden'; ?>">

<form id="registration-form"  action="<?php //get_site_url(); ?>/wordpress/wp-admin/admin-post.php" method="POST">	
		<input type="hidden" value="<?php echo esc_attr( '/' ); ?>" name="redirect_to">
		<input type="hidden" name="action" value="new_registration" />
		
	<div class="row">
		<div class="col-md-12">
		<p class="center color-white <?php echo ($_GET['registration'] === 'start') ? '' : 'hidden'; ?>">Set your Password</p>
		</div>
	</div>	
	
	
	<div class="row">
			<div class="col-md-12">
				<p class="error center <?php echo ($_GET['registration'] === 'incomplete') ? '' : 'hidden'; ?>">Incomplete Information</p>
			</div>
		</div>

			<div class="row">
			<div class="col-md-12">	
				
				<input id="user_pass" type="hidden" size="20" value="<?php echo $userName ?>" name="log" placeholder="Password"/>
				
				<input id="user_pass" type="password" size="20" value="" name="pwd" placeholder="Password"/>
			</div>
		</div>
		</form>
		
		<div class="row">
		<div class="col-md-12">
		<button class="color-primary"  form="registration-form" id="wp-submit" type="submit" name="wp-submit">Save</button>
		</div>
	</div>	
</div>

