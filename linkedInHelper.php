<?php
defined('_JEXEC') or die;

class modJoomGoogleLoginHelper
{

	public static function initLinkedInSdk($appId, $secretAppId) 
    {
        
    }

    public static function initLinkedInUser($linkedIn, $accessToken = null) 
    {
    	
	}

	public static function loginLinkedInUser($user, $linkedInUser, $accessToken, $referer)
	{
		
	}

    public static function generateLinkedInButton($params, $appId)
    {
		$linkedInButton = modJoomHelper::getParamName($params, 'linkedInButton');
		$linkedInButtonText = modJoomHelper::getParamName($params, 'linkedInButtonText');

		// @todo: somehow we must include the src on the return vars. Right now is not a valid JS code with
    	// the HTML tags in the string.
		$linkedInButton  = '
			<script type="in/Login" data-onAuth="onLinkedInAuth">
				Hello, <?js= firstName ?> <?js= lastName ?>. </br>
			</script>
		';

        return $linkedInButton;
    }

    public static function loadLinkedInJavascriptSdk($appId)
    {
    	// @todo: somehow we must include the src on the return vars. Right now is not a valid JS code with
    	// the HTML tags in the string.
    	$script = '
			<script type="text/javascript" src="http://platform.linkedin.com/in.js">
				api_key: ' . $appId . '
				credentials_cookie: true
			</script>
		';

		return $script;
    }

    public static function generateJsLoginScript()
    {
    	$script = '
        	function onLinkedInAuth() {
				IN.API.Profile("me")
					.fields("id", "first-name", "last-name", "picture-url", "email-address")
					.result(displayProfiles)
					.error(displayProfilesErrors);
			}

			function displayProfiles(profiles) {
				member = profiles.values[0];
				data.user = member;
				
				// @todo: Here we have to redirect if we have all the data that we need.
			}

			function displayProfilesErrors(profiles) {
				alert("ERRORS");
			}
		';

		return $script;
    } 

    public static function generateCssScript($moduleName)
    {
    	// Implement all the logic to override the default linkedIn button.
    	$style = '
		'; 

		return $style;
    }
}
?>