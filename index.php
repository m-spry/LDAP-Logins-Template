<?php
/*
LDAP Logins template v1
Author: Madison Spry (https://www.mspry.net/ || @m_spry)
If you found this useful and want to show your appreciation leave this message in your source

WHAT THIS IS:
This file contains a barebones template login page that connects to a LDAP server to authorise a login
It has some basic logic for catching the number of login attempts.
You may want to expand upon this yourself but its outside the scope of this file
You can also expand upon on this using some XHR and CSS

HOW TO USE / IMPORTANT TO KNOW:
You must enable ldap in your php.ini as well as configured ldap.conf to use TLS_CACERT.
If you don't want to setup the TLS certificate enter "TLS_REQCERT never" in your ldap.conf
If using a Windows Server you must install OpenLDAP and create the directory C:\OpenLDAP\sysconf\ to place your ldap.conf file
Edit $ldap to use your server, this template uses ldaps over port 636.
You can modify it to use ldap 389 if you wish however its advised you use start tls with that.
Edit $bind "YOUR_DOMAIN" to be that of your AD name else logins will fail
*/
session_start();
if(isset($_POST['loginUser']) && isset($_POST['loginPassword'])){
	$ldap = ldap_connect("ldaps://xxx.xxx.xxx.xxx");
	$username  = filter_var($_POST['loginUser'], FILTER_SANITIZE_STRING);
	$password = filter_var($_POST['loginPassword'], FILTER_SANITIZE_STRING);
	if(!ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3)){
		print "Could not set LDAPv3\r\n";
	} else {
		if ($_SESSION['loginAttempts'] <= 5) {
			if (!empty($password)) {
				// now we need to bind to the ldap server
				$bind = ldap_bind($ldap, "YOUR_DOMAIN\\".$username, $password);
			    if ($bind) {
				    $_SESSION['Username'] = $username;
			        @ldap_close($ldap);
			        $_SESSION['loginAttempts'] = 0;
					session_regenerate_id(TRUE); // Prevent stagnation and hijacking. Regenerate the session ID after accepting the login credentials.
					// Execute any other code, IE: DB queries or Security checks then refresh.
					header('Location: '.$_SERVER['PHP_SELF']);
			    } else {
					$_SESSION['loginAttempts']++;
			        unset($_POST['loginUser']);
			        unset($_POST['loginPassword']);
					header('Location: '.$_SERVER['PHP_SELF']);
			    }
		    } else {
		    	$_SESSION['loginAttempts']++;
			    unset($_POST['loginUser']);
			    unset($_POST['loginPassword']);
				header('Location: '.$_SERVER['PHP_SELF']);
		    }
	    } else {
		    echo 'Too many attempts, try again later.';
	    }
	}
}
if (!isset($_SESSION['Username'])) { ?>
	<h1>You're not logged in!</h1>
	<?php // LDAPS based login.
	if (!isset($_SESSION['loginAttempts'])) {
		$_SESSION['loginAttempts'] = 0;
	}
	if(!isset($_POST['loginUser']) && !isset($_POST['loginPassword'])){
		if ($_SESSION['loginAttempts'] < 5) { ?>
			<form id="loginForm" action="#" method="POST">
			    <?php if ($_SESSION['loginAttempts'] >= 1) { ?>
			    	<p>Login failed. Too many incorrect login attempts will result in a temporary lock out.</p>
					<?php if ($_SESSION['loginAttempts'] == 4) { ?><p>This is your last attempt to login, one more failed attempt will result in a temporary lock out.</p><?php } ?>
			    <?php } ?>
				<label for="username">Username: </label><input id="username" type="text" name="loginUser" />
				<label for="password">Password: </label><input id="password" type="password" name="loginPassword" />
				<input type="submit" name="submit" value="Login" />
			</form>
		<?php } else {
			echo 'Too many attempts, try again later.';
		}
	}
} else {
	echo '<h1>Welcome '.$_SESSION['Username'].'!</h1>';
} ?>
