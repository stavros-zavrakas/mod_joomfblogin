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

		$linkedInUser = $linkedIn->get('/people/~:(id,first-name,last-name,headline,picture-url,email-address)', array());

		return $linkedInUser;
	}

	public static function loginLinkedInUser($user, $linkedInUser, $accessToken, $referer)
	{
		if ($linkedInUser && $user->guest)
        {
            try {
                $isJoomlaUser = modJoomHelper::getUserIdByParam('email', $linkedInUser->emailAddress);

                if(empty($isJoomlaUser)) 
                {
                    // Store the user object in the DB (register)
                    jimport('joomla.user.helper');
                    $password = JUserHelper::genRandomPassword(5);
                    $name = $linkedInUser->firstName . " " . $linkedInUser->lastName;
                    $joomlaUser = modJoomHelper::registerUser($name, $name, $password, $linkedInUser->emailAddress);
                }
                else 
                {
                    // Retrieve the user object from DB
                    $joomlaUser = JFactory::getUser($isJoomlaUser);
                }
                // Login the User
                modJoomHelper::login($joomlaUser, $referer, 'linkedIn', $linkedInUser->id, $accessToken);
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
        
        // LinkedIn button sizes
        // https://developer.linkedin.com/thread/3290
        // You can use data-size as small, medium, and large to resize the Button
        // Ex. <script type="IN/Login" data-size="large" data-onAuth="onLinkedInAuth">
		$linkedInButton  = '
			<script type="in/Login" data-size="large" title="Sign in" data-onAuth="onLinkedInAuth">
			</script>
		';

        $linkedInButton  = '
            <input type="button" onclick="onLinkedInLoad()" value="Sign in LinkedIn" />
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

            function onLinkedInLoad() {
                IN.UI.Authorize().place();      
                IN.Event.on(IN, "auth", function () { onLinkedInAuth(); });
            }
		';

		return $script;
    } 

    public static function generateCssScript($moduleName)
    {
    	// Implement all the logic to override the default linkedIn button.
    	$style = '
            .linkedIn-x-large {
                font-size: 24px !important;
                width: 93px;
                height: 39px !important;
            }
		'; 

		return $style;
    }
}
?>