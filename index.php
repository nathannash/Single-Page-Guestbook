<!DOCTYPE html>
<html lang="en">
    <head>
	    <title>Exercise 2: Guestbook</title>
        <link rel="stylesheet" href="lib/css/bootstrap.css" type="text/css" media="screen" charset="utf-8">
        <link rel="stylesheet" href="lib/css/style.css" type="text/css" media="screen" charset="utf-8">  
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>		
		<script type="text/javascript" src="lib/js/bootstrap.js"></script>
		<script type="text/javascript">
		$(document).ready(function(){
			$(".comment:odd").addClass("alternateBG");
		});
		</script>
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
					$FormAttachment = "picture_file";
					$Name = $Email = $Message = $Attachment = "";
				?>
				<form id="comment-form" action="<?php htmlspecialchars($_SERVER['PHP_SELF'])?>" enctype="multipart/form-data" method="post">
					<!--name-->
					<label for="<?php echo $FormName; ?>"><strong><span class="required">*</span>Guest Name:</strong></label>
					<input id="post-name" class="noerror" type="text" name="<?php echo $FormName; ?>" value="<?php if(isset($_POST['guest_name'])) echo $_POST['guest_name']; ?>">
					<!--email-->
					<label for="<?php echo $FormEmail; ?>"><strong><span class="required">*</span>Email:</strong></label>
					<input id="post-email" class="noerror" type="text" name="<?php echo $FormEmail; ?>" value="<?php if(isset($_POST['email_address'])) echo $_POST['email_address']; ?>">	
					<!--message-->
					<label for="<?php echo $FormMessage; ?>"><strong><span class="required">*</span>Message:</strong></label>
					<textarea id="post-message" class="noerror" name="<?php echo $FormMessage; ?>" ><?php if(isset($_POST["post_message"])) echo $_POST["post_message"]?></textarea>	
					<!--attachment-->
					<label for="<?php echo $FormAttachment; ?>"><strong>Upload Picture:</strong></label>
					<input id="post-image-size" name="size_constraint" type ="hidden">
					<input id="post-image" class="well" name="<?php echo $FormAttachment; ?>" type="file">			
					<!--submit:: hidden fields enforce file size and check if the form has been submitted-->
				    <input type="hidden" name="MAX_FILE_SIZE" value="2097152">
				    <input type="hidden" name="submitted" value="1">  
					<input class="submit" type="submit" name="submit"  value="Let 'er rip" />
				</form>
			</div>
			<div class="span8 wall">
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
							echo 
							'<script type="text/javascript">
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
							echo 
							'<script type="text/javascript">
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
							echo 
							'<script type="text/javascript">
								$(document).ready(function(){
									$("#post-message").removeClass("noerror").addClass("error");
								});
							</script>';							
						}
						
						//Attachment Validation
						$FileType = $_FILES['picture_file']['type'];
						$FileSize = $_FILES['picture_file']['size'];
						$FileName = $_FILES['picture_file']['name'];
						$FileTmp  = $_FILES['picture_file']['tmp_name'];
						$Dir = "uploads/";
						
						//If user uploads file...
						if($_FILES){
							global $Attachment;
							$Attachment = $FileName;
							
							if (($FileType == "image/gif") 
						    || ($FileType == "image/jpg") 
							|| ($FileType == "image/jpeg") 
							|| ($FileType == "image/png") 
							&& ($FileSize < 2097152)) {
								//Make public directory 
								if(move_uploaded_file($FileTmp, $Dir . $FileName === FALSE)){
									echo "<div class='alert alert-danger'> Could not move uploaded file to \"uploads" . htmlentities($FileName) . "\"</div>\n";
								} else {
									mkdir($Dir, 0777);
									move_uploaded_file($FileTmp, $Dir . $FileName);
									echo "<div class='alert alert-success'>Successfully uploaded \"uploads/" . htmlentities($FileName) . "\"</div>\n";
								}
							} else {
								echo "<div class='alert alert-danger'>File must be a JPG, JPEG, GIF or PNG and weigh <2MB. <strong><a class='alert-link' href='/nnash_ex2/'>Start over?</a></strong></div>";							
							}								
						} 
							
						//If all required fields are set publish comment
						if(isset($Name) && isset($Email) && isset($Message)){
							publishComment();
						} else {
							echo "<div class='alert alert-danger'>Please complete all required fields. Or <strong><a class='alert-link' href='/nnash_ex2/'>start over</a></strong></div>";							
						}	
					}
					
					//Load comments into page 
					displayComments();					
					
					function publishComment(){
						//Capture form data passed by GLOBAL vars
						global $Name;
						global $Email;
						global $Message;
						global $Attachment;
						$Date = date('o F d, g:i A'); //YYYY MM DD, 00:00 AM/PM
						
						//Format comment 
						if($Attachment){
						    $Comment = "<div class='comment'><p>" . $Message . "</p>" . "<hr />" . "<p>By <a href=mailto:" . $Email . ">" . $Name . "</a> on "  . $Date . " (View Attachment:<a href='uploads/'" . $Attachment .">" . $Attachment . "</a>)</p></div>\n";
						} else {
						    $Comment = "<div class='comment'><p>" . $Message . "</p>" . "<hr />" . "<p>By <a href=mailto:" . $Email . ">" . $Name . "</a> on "  . $Date . "</p></div>\n";
						}
						//VARs to write the file 
						$Dir = "comments/";
						$File = 'comments/comments.txt';
						//Open file to get existing content
						$Current = file_get_contents($File);
						//Append new comment to file
						$Current .= $Comment;
						
						//Create comments directory if it doesn't exist already
						if(!is_dir($Dir)){
							mkdir($Dir, 0777);
							file_put_contents($File, $Current);
						} else {
							file_put_contents($File, $Current);
						}							    						
					}
					
					function displayComments(){
						if(is_dir("comments/")){
							$Comments = file("comments/comments.txt");
							$CommentLines = count($Comments);
							//Display comments with loop
							for($i = 0; $i < $CommentLines; ++$i){
								echo $Comments[$i];
							}
						} else {
							echo "<div class='alert alert-info'>You're the <strong>first one</strong> here! Leave a comment!</div>";
						}
					}					
			 ?>
			</div>
		</div>
	</section>
</body>
</html>