<?php

$ls = "log";
if($global_user != null){
$ls .= "out";
$u = json_encode($global_user->getPropArray());
}else{
$ls .= "in";
 $u = "null";
}


?>

<div id="login" style="position:fixed;bottom:10px;right:10px;text-align:right;">
<a class="<?php echo $ls; ?>" style="font-size:20px;padding:5px;display:block;" href="#"><?php echo $ls; ?></a>
<script src="https://login.persona.org/include.js"></script>
<script src="/Core/User/public/jstz.min.js" type="text/javascript" ></script>
<script type="text/javascript">

	var global_user = <?php echo $u ?>;

	jQuery("a.login").click(function(e){e.preventDefault(); navigator.id.request(); });
	jQuery("a.logout").click(function(e){e.preventDefault(); navigator.id.logout(); });

	navigator.id.watch({
		loggedInUser: (global_user!=null)?global_user["email"]:null,
		onlogin: function(assertion) {
			var tz = jstz.determine();
			$.ajax({
				type: 'POST',
				url: '/Core/User/public/verify.php',
				data: {assertion: assertion, timezone: tz.name()}
			}).done(function(res, status, xhr) { 
				console.log(res);
				var j = JSON.parse(res);
				if(j.status == 1){ 
					ap.login();
					global_user = j.user;
					
					var mes = jQuery("<h1>Welcome "+j.user.nickname+"</h1>");
					
					jQuery("#login").prepend(mes);
					mes.fadeTo(1000,0,function(){
						mes.remove();
					});
					jQuery("#login .login")
					.removeClass("login")
					.addClass("logout")
					.html("logout");
					jQuery("a.logout").unbind("click");
					jQuery("a.logout").click(function(e){e.preventDefault(); navigator.id.logout(); });
				}
				else{
					console.log("Login failure: " + j.reason);
					navigator.id.logout();
				}
			}).fail(function(xhr, status, err) {
				console.log("Login failure: " + err);
				navigator.id.logout();
			}
			);
		},
		onlogout: function() {
			// A user has logged out! Here you need to:
			// Tear down the user's session by redirecting the user or making a call to your backend.
			// Also, make sure loggedInUser will get set to null on the next page load.
			// (That's a literal JavaScript null. Not false, 0, or undefined. null.)
			$.ajax({
				type: 'POST',
				url: '/Core/User/public/logout.php', // This is a URL on your website.
				success: function(res, status, xhr) { 
//					var j = JSON.parse(res);
					ap.logout(global_user);
					var mes = jQuery("<h1>Goodbye "+global_user.nickname+"</h1>");
					global_user = null;

					jQuery("#login").prepend(mes);
					mes.fadeTo(1000,0,function(){
						mes.remove();
					});

					jQuery("#login .logout")
					.removeClass("logout")
					.addClass("login")
					.html("login");
					jQuery("a.logout").unbind("click");
					jQuery("a.login").click(function(e){e.preventDefault(); navigator.id.request(); });
				},
				error: function(xhr, status, err) { alert("Logout failure: " + err); }
			});
		}
	});
	

</script>
</div>

