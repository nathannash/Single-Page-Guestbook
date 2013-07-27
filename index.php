<!DOCTYPE html>
<html lang="en">
    <head>
	    <title>Exercise 2: Guestbook</title>
        <link rel="stylesheet" href="lib/css/bootstrap.css" type="text/css" media="screen" charset="utf-8"> 
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>		
		<script type="text/javascript" src="lib/js/bootstrap.js"></script>
		<!-- any custom styling-->
		<style type="text/css">
			#wrapper {
				margin:0 auto;
				padding:100px 0 0 0;
				width:960px;
			}
			
			.errorText, .required {
				color:#F00;
			}
		</style>
    </head>
<body>
	<section id="wrapper">
		<div class="row show-grid ">
			<div class="span4">
				<?php
					//Initialize global form variables
					$FormName = "guest_name";					
					$FormEmail = "email_address";
					$FormMessage = "post_message";	
					$FormImage = "post_image";
					$Name = $Email = $Message = "";						
				?>
				<form id="comment-form" action="<?php htmlspecialchars($_SERVER['PHP_SELF'])?>" enctype="multipart/form-data" method="post">
					<!--name-->
					<label for="<?php echo $FormName; ?>"><strong><span class="required">*</span>Guest Name:</strong></label>
					<input id="post-name" class="noerror" type="text" name="<?php echo $FormName; ?>" value="<?php if(isset($_POST['guest_name'])) echo $_POST['guest_name']; ?>">
					<!--email-->
					<label for="<?php echo $FormEmail; ?>"><strong><span class="required">*</span>Email:</strong></label>
					<input id="post-email" class="noerror" type="text" name="<?php echo $FormEmail; ?>" value="<?php if(isset($_POST['email_address'])) echo $_POST['email_address']; ?>">	
					<!--website-->
					<!--message-->
					<label for="<?php echo $FormMessage; ?>"><strong><span class="required">*</span>Message:</strong></label>
					<textarea id="post-message" class="noerror" name="<?php echo $FormMessage; ?>" ><?php if(isset($_POST["post_message"])) echo $_POST["post_message"]?></textarea>	
					<!--attachment-->
					<label for="<?php echo $FormImage; ?>"><strong>Upload Picture:</strong></label>
					<input id="post-image" class="noerror" name="<?php echo $FormImage; ?>" type="file">			
					<!--submit-->
				    <input type="hidden" name="submitted" value="1">  
					<input type="submit" class="noerror" name="submit" value="Let 'er rip" />
				</form>
			</div>
			<div class="span8">
				<h2>Guestbook</h2>
				<?php
				
					//Validate form only if its been submitted
					if($_POST['submitted'] == 1){
						validateForm();
					} 
					
					function validateForm(){
						//Name Validation
						if(strlen($_POST["guest_name"]) != 0){
						    global $Name;
							$Name = $_POST["guest_name"];
						} else {
							echo '<script type="text/javascript">
								$(document).ready(function(){
									$("#post-name").removeClass("noerror").addClass("error");
								});
							</script>';
						}
						//Email Validation
						if(strlen($_POST["email_address"]) != 0){
							global $Email;
							$Email = $_POST["email_address"];
						} else {
							echo '<script type="text/javascript">
								$(document).ready(function(){
									$("#post-email").removeClass("noerror").addClass("error");
								});
							</script>';							
						}
						//Message Validation
						if(strlen($_POST["post_message"]) != 0){
							global $Message;
							$Message = $_POST["post_message"];
						} else {
							echo '<script type="text/javascript">
								$(document).ready(function(){
									$("#post-message").removeClass("noerror").addClass("error");
								});
							</script>';							
						}
						
						//If all required fields are set publish comment
						if(isset($Name) && isset($Email) && isset($Message)){
							publishComment();
						} else {
							echo "<strong class='errorText'>Please complete all required fields.</strong>";							
						}	
					}
					
					function publishComment(){
						//Capture form data passed by GLOBAL vars
						global $Name;
						global $Email;
						global $Message;
						$Date = date('o F d');	
						
						//Format comment 
						$Comment = "<p>" . $Message . "</p>" . "<hr />" . "<p>By " . $Name . " on " . $Date . "</p>";
						
						//Create comments directory
						$Dir = "/nnash_ex2/comments/";
						if (is_dir($Dir)){
							$SaveString = $Comment;
							$SaveFileName = "$Dir/comments.txt";
							if(file_put_contents($SaveFileName, $SaveString) > 0){
								//parse comments.txt
								echo "a lovely comment";
							} else {
								echo "error :(";
							}
						}
						    						
					}
					
				?>
			</div>
		</div>
	</section>
</body>
</html> 