<?php
/*
Plugin Name: Contact Form by ContactMetrics
Plugin URI: http://contactmetrics.com
Description:  Easily add a contact form to your WordPress site.
Author:  contactmetrics.com
Author URI: http://contactmetrics.com
Version: 1.2
*/
////////////////////////////////////////////////////////////The admin page///////////////////////////////////////////////////////////
$email = get_option('contactmetrics_email');
if (empty($email)){update_option('contactmetrics_email', get_bloginfo('admin_email'));}
$css = get_option('contactmetrics_css');
$starting_css='.control-label, p, .btn{font-family: "Lucida Sans Unicode", "Lucida Grande", sans-serif; font-size: 12px; line-height: 18px;}';
if (empty($css)){update_option('contactmetrics_css', $starting_css);}

if($_POST) {
$email_form=$_POST["Email"];
$css_form=$_POST["Css"];
update_option('contactmetrics_email', $email_form);
update_option('contactmetrics_css', $css_form);
}

add_action('admin_menu', 'my_plugin_menu' );


function my_plugin_menu() {
	add_menu_page('Contact Form by Contactmetrics.com ', 'Contact Form', 'manage_options', 'custompage', 'cm_options_page', plugins_url("assets/images/favicon.gif", __FILE__));
}

function cm_options_page() {
	$url=plugins_url();
	?>
	<h1>Contact Form by <a href="http://contactmetrics.com"><img style="vertical-align: middle;"height="40" src="<?php echo $url ?>/contact-form-contactmetrics/assets/images/logo.png" /></a></h2>
	<h3>Overview</h3>	
	<p>This plugin will insert a simple, elegant, contact form into your website.  It will look like this:</p>
	<img src="<?php echo $url ?>/contact-form-contactmetrics/assets/images/example.png" />
	<h3>Installation</h3>
	<p>To install the plugin, paste the shortcode <span style="font-family: Courier; color: red">[contactmetrics]</span> into your post or page.</p>
	<br /><br />
	<h3>Plugin Options</h3>
	<form method="post">
	<label for="cm_email">Email (messages sent here):</label>
	<input id="cm_email" name="Email" type="email" style="width: 200px" value="<?php echo get_option('contactmetrics_email'); ?>" /> 
	<br /><br />
	<label for="cm_css">CSS (add styles to the form here, for advanced users only)</label><br />
	<textarea class="textarea" cols="130" id="cm_css" name="Css" rows="2"><?php $out=get_option('contactmetrics_css'); $out=str_replace('\\','',$out); echo $out ?></textarea>		
	<br /><br />
	<input type="submit" name="submit" value="Save Plugin Options"/>
	</form>
	<br /><br />
	<h3>Support</h3>
	<p>If you need help, feel free to <a href="http://contactmetrics.com/contact/">contact us.</p>

	<?php
}

////////////////////////////////////////////////////////////The shortcode replacement////////////////////////////////////////////////////////////

function replace_shortcode(){
	$url=home_url();
	return 	'<iframe frameborder="0" width="900" height="550" seamless="seamless" scrolling="no"  src="'.$url.'/?contactmetrics=true"></iframe>';
}

add_shortcode('contactmetrics', 'replace_shortcode' );


////////////////////////////////////////////////////////////The form page////////////////////////////////////////////////////////////
add_action('init', 'process');

function process(){
	if(isset($_GET['contactmetrics'])){
		form_html();
		exit();
	}
}

function form_html(){
		$url=plugins_url();
		?>
		<!DOCTYPE html>
		<html>
			<head>
				<script type="text/javascript" src="<?php echo includes_url();?>/js/jquery/jquery.js"></script>				
				<script type="text/javascript" src="<?php echo $url ?>/contact-form-contactmetrics/assets/js/bootstrap.min.js"></script>			
				<link href="<?php echo $url ?>/contact-form-contactmetrics/assets/css/bootstrap.css" media="all" rel="stylesheet" type="text/css">		
				<link href="<?php echo $url ?>/contact-form-contactmetrics/assets/css/extra.css" media="all" rel="stylesheet" type="text/css">		
			
				<script type="text/javascript" >
				 function validateEmail($email) {
					if ($email==''){return false}
					var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
					if( !emailReg.test( $email ) ) {return false;} else {return true;}
				}
				
				jQuery(document).ready(function(){
					jQuery('#cm_spam').remove();
					var button=jQuery('#submit-id-submit');
					button.removeClass();
					button.addClass("btn btn-primary");
					
					jQuery("#cm_form").submit(function(evt) {
							var email=jQuery("#cm_email_input").val();
							if (validateEmail(email)){return}
							else{
								var email_label=jQuery('#cm_email');
								email_label.css('color','red');
								evt.preventDefault(); // prevent default behavior of the event, in this case form submission
								return false; // return false, just in case, but in theory is not necessary}
							}
						});
				
				});
				</script>

				<style>
				<?php 
					$css=get_option('contactmetrics_css');
					echo $css;
				?>
				</style>
			</head>

			<body>

				<div class="container" name="container">
		<?php 
		if($_POST) {
			if (filter_var($_POST["Email"], FILTER_VALIDATE_EMAIL)){
				$spam=$_POST["Spam"];
				echo $spam;
				if (!$spam){
					$email=$_POST["Email"];
					$br="\n";
					$to=get_option('contactmetrics_email');
					$name=$_POST["Name"]?$_POST["Name"]:'Name Blank';
					$subject=$_POST["Subject"]?$_POST["Subject"]:'Subject Blank';
					$message=$_POST["Message"]?$_POST["Message"]:'Message Blank';
					$headers[] = 'From: " '.$name.'"<'.$email.'>';
					$sent=wp_mail($to, $subject, $message, $headers);			
				}
			}
			else{
				echo 'The message was not sent because you had an invalid email address.';
			}
			
			if ($sent){echo 'Thanks for your message!';}
			else{echo 'Unfortunately, there was an error and your message did not send.';}
		}
		else{

		?>			
					<form id="cm_form" method="post" >
						<!-- spam -->
						<div id="cm_spam" class="control-group"><label for="spam" class="control-label "><b style="color: red">Please enable JavaScript and refresh the page before using this form.</b></label>
							<div class="controls">
								<input class="textinput textInput" id="cm_spam_input" name="Spam" type="text" />
							</div>
						</div>
						<!-- name -->
						<div id="cm_name" class="control-group">
							<label for="name" class="control-label ">Name</label>
							<div class="controls">
								<input class="textinput textInput" id="cm_name_input" name="Name" type="text" /> 
							</div>
						</div>
						<!-- email -->
						<div id="cm_email" class="control-group">
							<label for="name" class="control-label ">Email<span style="color: red">*</span></label>
							<div class="controls">
								<input class="textinput textInput" id="cm_email_input" name="Email" type="text" /> 
							</div>
						</div>
						<!-- subject -->
						<div id="cm_subject" class="control-group">
							<label for="name" class="control-label ">Subject</label>
							<div class="controls">
								<input class="textinput textInput" id="cm_subject_input" name="Subject" type="text" /> 
							</div>
						</div>
						<!-- message -->
						<div id="cm_message" class="control-group">
							<label for="name" class="control-label ">Message</label>
							<div class="controls">
								<textarea class="textarea" cols="40" id="cm_message_input" name="Message" rows="10"></textarea>					
							</div>
						</div>		
						<!-- submit button -->				
						<div class="form-actions">
							<input type="submit" name="submit" value="Submit" class="btn btn-primary" id="cm_submit"/>
						 </div>
					 </form>
		<?php
		}
		?>			 
				 </div>
			</body>
		</html>
		<?php
}


?>