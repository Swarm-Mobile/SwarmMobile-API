<html>
<body>
<!-- <form method="POST" action="/expmembers/login" id="form"></form>-->
<form method="POST" action="" id="form">
	<input type="text" name="username"  id="username"/>
	<input type="password" name="password" id="password"/>
	<!--<input type="password" name="password_confirm"  id="password_confirm"/>
	<input type="text" name="email" id="email"/>
	<input type="text" name="screen_name" value="lalan's"/>-->
	<input type="submit" name="submit" class="button" id="submit_btn" value="Send" />
</form>

<div id="message"></div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script>
$(function() {
  $("#form").on('submit', function(e) {
  	e.preventDefault();
    var username = $("#username").val();
    var password = $("#password").val();
    var password_confirm = $("#password_confirm").val();
    var email = $("#email").val();
    var screen_name = $("#screen_name").val();
    var uuid = '5355ae34700be';
    //dataString = 'username='+ username + '&password=' + password + '&email=' + email + '&screen_name=' + screen_name + '&password_confirm=' + password_confirm;
    dataString = 'username='+ username + '&password=' + password;
 
    $.ajax({
	  type: "GET",
	  //url: "/api/member/register",
	  url: "/login",
	  data: dataString,
	  contentType:'application/x-www-form-urlencoded',
	  success: function(dataStr) {
		$('#message').html("<h2>Contact Form Submitted!</h2>");
	  }
});
  });
});

</script>
</body>
</html>