<?php
defined('_JEXEC') or die;

class modJoomLinkedInLoginHelper
{

	public static function initLinkedInSdk($appId, $secretAppId) 
    {
		$linkedin = new LinkedIn(array(
			'apiKey' => $appId,
			'apiSecret' => $secretAppId,
			'callbackUrl' => '/',
		));

		return $linkedin;
    }

    public static function initLinkedInUser($linkedIn, $accessToken = null) 
    {
    	if (isset($accessToken)) 
        {
            $linkedIn->setAccessToken($accessToken);
        }

		$options = ":(first-name,last-name,picture-url)";
		$linkedInUser = $linkedIn->get('/people/~', $options);
echo $linkedInUser;
die;
		return $linkedInUser;
	}

	public static function loginLinkedInUser($user, $linkedInUser, $accessToken, $referer)
	{
		if ($linkedInUser && $user->guest)
        {
            try {

            	// @todo: Verify that is the correcte params that we have to send to retrieve the mail
                $isJoomlaUser = modJoomHelper::getUserIdByParam('emailAddress', $linkedInUser['values'][0]);

                if(empty($isJoomlaUser)) 
                {
                    // Store the user object in the DB (register)
                    jimport('joomla.user.helper');
                    $password = JUserHelper::genRandomPassword(5);
                    $joomlaUser = modJoomHelper::registerUser($linkedInUser['displayName'], $linkedInUser['displayName'], $password, $linkedInUser['values'][0]);
                }
                else 
                {
                    // Retrieve the user object from DB
                    $joomlaUser = JFactory::getUser($isJoomlaUser);
                }
                // Login the User

                modJoomHelper::login($joomlaUser, $referer, 'linkedIn', $linkedInUser['id'], $accessToken);
            }
            catch (FacebookApiException $e) {
                // error_log($e);
                $linkedInUser = null;
            }
        }
	}

    public static function generateLinkedInButton($params, $appId)
    {
		$linkedInButton = modJoomHelper::getParamName($params, 'linkedInButton');
		$linkedInButtonText = modJoomHelper::getParamName($params, 'linkedInButtonText');

		// @todo: modify the text: http://forums.asp.net/t/1931746.aspx?Customizing+Linkedin+Login+Button
    // Last response
		$linkedInButton  = '
			<script type="in/Login" data-size="large" title="Sign in" data-onAuth="onLinkedInAuth">
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
				
                window.location.href=document.URL + "?mod=log&accessToken=" + IN.ENV.auth.oauth_token + "&type=linkedIn";
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