<!DOCTYPE html>
<html lang="en">
    <head>
	    <title>Exercise 2: Guestbook</title>
        <link rel="stylesheet" href="lib/css/bootstrap.css" type="text/css" media="screen" charset="utf-8">
		<link href="lib/css/lightbox.css" rel="stylesheet" />
        <link rel="stylesheet" href="lib/css/style.css" type="text/css" media="screen" charset="utf-8">  
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>		
		<script src="lib/js/lightbox-2.6.min.js"></script>
		<script type="text/javascript">
		$(document).ready(function(){
			$(".comment:odd").addClass("well");
		});
		</script>
    </head>
<body>
	<section id="wrapper">
		<div class="row show-grid ">
			<div class="span4">
				<?php
					//Initialize global form variables
					session_start();
					$FormName = "post_name";					
					$FormEmail = "post_email";
					$FormMessage = "post_message";	
					$FormAttachment = "post_image";
					$Name = $Email = $Message = $Attachment = "";
					//<?php if(isset($_POST['submit'])){ $Name = $_SESSION['post_name'] echo $Name;} end delimiter
				?>
				<form id="comment-form" action="<?php htmlspecialchars($_SERVER['PHP_SELF'])?>" enctype="multipart/form-data" method="post">
					<!--name-->
					<label for="<?php echo $FormName; ?>"><strong><span class="required">*</span>Guest Name:</strong></label>
					<input id="post-name" class="noerror" type="text" name="<?php echo $FormName; ?>" value="<?php if(isset($_POST['submit'])){ $Name = $_POST['post_name']; echo $Name;} ?>">
					<!--email-->
					<label for="<?php echo $FormEmail; ?>"><strong><span class="required">*</span>Email:</strong></label>
					<input id="post-email" class="noerror" type="text" name="<?php echo $FormEmail; ?>" value="<?php if(isset($_POST['submit'])){ $Email = $_POST['post_email']; echo $Email;} ?>">	
					<!--message-->
					<label for="<?php echo $FormMessage; ?>"><strong><span class="required">*</span>Message:</strong></label>
					<textarea id="post-message" class="noerror" name="<?php echo $FormMessage; ?>" ><?php if(isset($_POST['submit'])){ $Message = $_POST['post_message']; echo $Message;} ?></textarea>	
					<!--attachment-->
					<label for="<?php echo $FormAttachment; ?>"><strong>Upload Picture:</strong></label>
					<input id="post-image-size" name="size_constraint" type ="hidden">
					<input id="post-image" class="well" name="<?php echo $FormAttachment; ?>" type="file">			
					<!--submit:: hidden fields enforce file size and check if the form has been submitted-->
				    <input type="hidden" name="MAX_FILE_SIZE" value="2097152">
				    <input type="hidden" name="submitted" value="1">  
					<input class="submit" type="submit" name="submit" value="Let 'er rip" />
				</form>
			</div>
			<div class="span8 wall">
				<h2>Guestbook</h2>
				<?php
					
					//Validate form only if its been submitted
					if($_POST['submitted'] == 1){
						validateForm();
					}
					
					//Retrieves the Mime-type of the uploaded file
				    function getMimeType(){
		                $finfo = new finfo(FILEINFO_MIME);
		                $type = $finfo->file($_FILES['post_image']['tmp_name']);//change the field_name
		                $mime = substr($type, 0, strpos($type, ';'));
		                return $mime;
			        }
					
					//If the Mime-type is invalid cancel upload
			        function isValidImage(){
			            $mime = getMimeType();
			            if(stristr($mime,'image')){
			                return TRUE;
		                } else {
			                return FALSE;
						}							
			        }					 
					
					function validateForm(){
						//Name Validation
						if(strlen($_POST["post_name"]) != 0){
							
						    global $Name;
							$Name = $_POST["post_name"];
						} else {
							echo 
							'<script type="text/javascript">
								$(document).ready(function(){
									$("#post-name").removeClass("noerror").addClass("error");
								});
							</script>';
						}
						//Email Validation
						if(strlen($_POST["post_email"]) != 0){
							global $Email;
							$Email = $_POST["post_email"];
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
						$FileType = $_FILES['post_image']['type'];
						$FileSize = $_FILES['post_image']['size'];
						$FileName = $_FILES['post_image']['name'];
						$FileTmp  = $_FILES['post_image']['tmp_name'];
				        $Res = isValidImage();
						$Dir = "uploads/";
				
						//If user uploads file...
						if(strlen($FileName) > 0){
							global $Attachment;
					
							if ($Res == TRUE) {
								//Make public directory 
								if(move_uploaded_file($FileTmp, $Dir . $FileName === FALSE)){
									$Attachment = $FileName;
									echo "<div class='alert alert-danger'> Could not move uploaded file to \"uploads" . htmlentities($FileName) . "\"</div>\n";
								} else {
									//If directory doesn't exist
									$Attachment = $FileName;
									mkdir($Dir, 0777);
									move_uploaded_file($FileTmp, $Dir . $FileName);
									echo "<div class='alert alert-success'>Successfully uploaded \"uploads/" . htmlentities($FileName) . "\"</div>\n";
								}
							} 
							
							if ($Res == FALSE) {
								echo "<div class='alert alert-danger'>The file must be a JPG, JPEG, GIF or PNG and smaller than 2MB.<strong><a class='alert-link' href='/nnash_ex2/'> Start over?</a></strong></div>";
							}					
						} 
						
						//If all required fields are set publish comment
						if(isset($Name) && isset($Email) && isset($Message)){
							publishComment();
						} else {
							echo "<div class='alert alert-danger'>Please complete all required fields.<strong><a class='alert-link' href='/nnash_ex2/'> Start over?</a></strong></div>";							
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
						
						//Format comment and include attachment if it exists
						if($Attachment){
						    $Comment = "<div class='comment'><p>" . $Message . "</p>" . "<hr />" . "<p>By <a href=mailto:" . $Email . ">" . $Name . "</a> on "  . $Date . " | <a href='uploads/" . $Attachment ."' data-lightbox='".$Attachment."'>Attachments</a></p></div>\n";
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
						session_unset();
						session_destroy();							    						
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