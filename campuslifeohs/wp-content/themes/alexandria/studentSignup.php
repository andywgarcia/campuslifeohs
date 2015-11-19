<?php
/*
Template Name: Student Signup
*/
?>
<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package alexandria
 */

get_header(); ?>

	<div id="primary" class="full-page-content-area">
		<div id="content" class="site-content" role="main">
              <style type="text/css">
					.error{
						padding: 5px 9px;
						border: 1px solid red;
						color: red;
						border-radius: 3px;
					} 

					.success{
						padding: 5px 9px;
						border: 1px solid green;
						color: green;
						border-radius: 3px;
					}

					form span{
						color: red;
						white-space:nowrap;
					}
					table {
						margin: 10px;
						width: auto;
					}
					td {
						vertical-align: middle;
						/*border: 1px solid black;*/
					}
					#required {
						margin: 10px;
						color: red;
					}
					#details {
						text-align: right;
					}
					#inputs {
						text-align: left;
					}
				
				</style>
				<div id="respond">
					<?php echo $response; ?>
					<form enctype="multipart/form-data" id="signUp" name="signUp">
						<h3><div id="TK"></div></h3>
						<table>
							<tbody><tr><td id="details"><p id="required"><b>*</b> Required</p></td></tr><tr>				
							</tr><tr><td align="right" id="details"><span><b>*</b></span> First Name:</td> <td id="inputs"><input type="text" name="firstName" id="firstName" value=""></td></tr>
							<tr><td id="details"><span><b>*</b></span> Last name: </td><td id="inputs"><input type="text" name="lastName" id="lastName" value=""></td></tr>
							<tr><td id="details">Preferred Name: </td><td id="inputs"><input type="text" name="nickName" id="nickName" value=""></td></tr>
							<tr><td id="details"><span><b>*</b></span> Phone Number: </td><td id="inputs"><input type="text" id="number" name="number" class="phone_us" value=""></td></tr>
							<tr><td id="details"><span><b>*</b></span> Text OK: </td><td id="inputs"><label><input type="radio" name="text" id="text" value="Yes" checked="">Yes</label>
									 <label><input type="radio" name="textok" value="No">No</label></td></tr>
							<tr><td id="details"><span><b>*</b></span> Email: </td><td id="inputs"><input type="email" id="email" name="primaryEmail" value=""></td></tr>
							<tr><td id="details"><span><b>*</b></span> Grade: </td><td id="inputs">
											<select id="grade" name="grade">
												<option value="Freshman" selected="">Freshman</option>
												<option value="Sophomore">Sophomore</option>
												<option value="Junior">Junior</option>
												<option value="Senior">Senior</option>
											</select>
							</td></tr>
							<tr><td id="details"><span><b>*</b></span> Birthday: </td><td id="inputs"><input type="date" id="birthday" name="birthday" value=""></td></tr>
							<tr><td id="details">Upload a Photo of Yourself:</td><td align="right"><input type="file" name="uploadedFile" id="uploadedFile" accept="image/*" capture="camera"></td></tr>
							
						</tbody></table>
					</form>
				<button  style="text-align: center" id="submit" onclick="signup()"> Sign Up</button>
				
				</div>
			
				<?php
					// If comments are open or we have at least one comment, load up the comment template
					if ( comments_open() || '0' != get_comments_number() )
						comments_template();
				?>


		</div><!-- #content -->
	</div><!-- #primary -->


	<script type="text/javascript"> 
	 
function signup()
{
  var firstName  = document.getElementById("firstName").value;
  var lastName = document.getElementById("lastName").value;
  var nickName = document.getElementById("nickName").value;
  var number = document.getElementById("number").value;
  var text = document.getElementById("text").value;
  var email = document.getElementById("email").value;
  var grade = document.getElementById("grade").value;
  var birthday = document.getElementById("birthday").value;

  xhr = new XMLHttpRequest();
  var url = "http://campuslifeohs.com/my-includes/ajax/signup.php?firstName=" + firstName + "&lastName=" + lastName + "&nickName=" + nickName + "&email=" + email + "&mobilePhone=" + number + "&textok=" + text + "&grade=" + grade + "&birthday=" + birthday;
  xhr.onreadystatechange = setStates;
  xhr.open("GET", url, true);
  xhr.send(null);

function setStates()
{
 // only handle requests in "loaded" state
  if (xhr.readyState == 4)
    {
     if (xhr.status == 200)
     {
      var res = xhr.responseXML;
      if (res = 1){
      	document.getElementById('TK').innerHTML = xhr.responseText;
      } else {
      	document.getElementById('TK').innerHTML = xhr.responseText;
      }
      
      } else {
       alert("Error with Ajax call!");
   	  }
    }
}



}


</script>

<?php get_footer(); ?>