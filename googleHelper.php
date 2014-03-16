<?php
defined('_JEXEC') or die;

class modJoomGoogleLoginHelper
{
    public static function getDimensionByImageName($imageName) 
    {
        $googleDimenions = array(
            '01FacebookButtonXLarge.png' => array('width' => '300px', 'height' => '66px'),
            '02FacebookButtonLarge.png' => array('width' => '260px', 'height' => '57px'),
            '03FacebookButtonMedium.png' => array('width' => '220px', 'height' => '48px'),
            '04FacebookButtonSmall.png' => array('width' => '180px', 'height' => '39px')
        );
        
        return $googleDimenions[$imageName]; 
    }

    public static function generateGoogleButton($params, $appId)
    {
    	$googleButton  = '<button class="g-signin"';
	    $googleButton .= 'data-scope="https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/plus.profile.emails.read"';
	    $googleButton .= 'data-requestvisibleactions="http://schemas.google.com/AddActivity"';
	    $googleButton .= 'data-clientId="' . $appId . '"';
	    $googleButton .= 'data-accesstype="offline"';
	    $googleButton .= 'data-callback="onSignInCallback"';
	    $googleButton .= 'data-theme="dark"';
	    $googleButton .= 'data-cookiepolicy="single_host_origin">';
    	$googleButton .= '</button>';

        return $googleButton;
    }

    public static function loadGoogleJavascriptSdk()
    {
    	$script = '
    	(function() {
			var po = document.createElement(\'script\');
			po.type = \'text/javascript\'; po.async = true;
			po.src = \'https://plus.google.com/js/client:plusone.js\';
			var s = document.getElementsByTagName(\'script\')[0];
			s.parentNode.insertBefore(po, s);
		})();
		';

		return $script;
    }

    public static function generateJsLoginScript()
    {
    	$script = '
    	var helper = (function() {
			var authResult = undefined;

			return {
				onSignInCallback: function(authResult) {
					if (authResult["access_token"]) {
						// The user is signed in
						this.authResult = authResult;
						// After we load the Google+ API, render the profile data from Google+.
						gapi.client.load("plus","v1",this.renderProfile);
					} else if (authResult["error"]) {
						// There was an error, which means the user is not signed in.
						// As an example, you can troubleshoot by writing to the console:
						console.log("There was an error: " + authResult["error"]);
					}
					console.log("authResult", authResult);
				},

				renderProfile: function() {
					var request = gapi.client.plus.people.get( {"userId" : "me"} );
					request.execute( function(profile) {
						if (profile.error) {
							alert(profile.error);
							return;
						}

						console.log("profile", profile);
					});
				},
			};
		})();
		function onSignInCallback(authResult) {
			helper.onSignInCallback(authResult);
		}
		';

		return $script;
    } 
}
?>