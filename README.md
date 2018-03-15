# LDAP-Logins-Template
Just a barebones LDAP login page

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
