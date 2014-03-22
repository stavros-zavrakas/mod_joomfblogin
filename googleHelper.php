<?php
defined('_JEXEC') or die;

class modJoomGoogleLoginHelper
{

	public static function initGoogleSdk($appId, $secretAppId) 
    {
        $google = new Google_Client();
		$google->setApplicationName('Google+ server-side flow');
		$google->setClientId($appId);
		$google->setClientSecret($secretAppId);
		$google->setRedirectUri('/');
		// $google->setDeveloperKey('YOUR_SIMPLE_API_KEY');

        return $google;
    }

    public static function initGoogleUser($google, $accessToken = null) 
    {
    	if (isset($accessToken)) {
            $accessToken = array('access_token' => $accessToken, 'created' => time(), 'expires_in' => 3000);
            $accessToken = json_encode($accessToken);
            $google->setAccessToken($accessToken);
        }
    	
				$plus = new Google_PlusService($google);
				$googleUser = $plus->people->get('me');
        
        return $googleUser;
	}

	public static function loginGoogleUser($user, $googleUser, $accessToken, $referer)
	{
		if ($googleUser && $user->guest)
        {
            try {

                $isJoomlaUser = modJoomHelper::getUserIdByParam('email', $googleUser['emails'][0]['value']);

                if(empty($isJoomlaUser)) 
                {
                    // Store the user object in the DB (register)
                    jimport('joomla.user.helper');
                    $password = JUserHelper::genRandomPassword(5);
                    $joomlaUser = modJoomHelper::registerUser($googleUser['displayName'], $googleUser['displayName'], $password, $googleUser['emails'][0]['value']);
                }
                else 
                {
                    // Retrieve the user object from DB
                    $joomlaUser = JFactory::getUser($isJoomlaUser);
                }
                // Login the User

                modJoomHelper::login($joomlaUser, $referer, 'google', $googleUser['id'], $accessToken);
            }
            catch (FacebookApiException $e) {
                // error_log($e);
                $fbuser = null;
            }
        }
	}

    public static function generateGoogleButton($params, $appId)
    {
        // @todo: maybe helpful to customize the button
    	// $googleButton  = '<button class="g-signin"';
	    // $googleButton .= 'data-scope="https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/plus.profile.emails.read"';
	    // $googleButton .= 'data-requestvisibleactions="http://schemas.google.com/AddActivity"';
	    // $googleButton .= 'data-clientId="' . $appId . '"';
	    // $googleButton .= 'data-accesstype="offline"';
	    // $googleButton .= 'data-callback="onSignInCallback"';
	    // $googleButton .= 'data-theme="dark"';
	    // $googleButton .= 'data-cookiepolicy="single_host_origin">';
    	// $googleButton .= '</button>';

		$googleButton  = '
			<div id="gSignInWrapper">
				<div id="customBtn" class="customGPlusSignIn">
					<span class="icon"></span>
					<span class="buttonText">Google</span>
				</div>
			</div>';

        return $googleButton;
    }

    public static function loadGoogleJavascriptSdk($appId)
    {
    	$script = '
    	(function() {
			var po = document.createElement(\'script\');
			po.type = \'text/javascript\'; po.async = true;
			po.src = \'https://plus.google.com/js/client:plusone.js?onload=render\';
			var s = document.getElementsByTagName(\'script\')[0];
			s.parentNode.insertBefore(po, s);
		})();

		/* Executed when the APIs finish loading */
		function render() {
			var additionalParams = {
				// \'callback\': \'onSignInCallback\',
				\'clientid\': "' . $appId . '",
				\'cookiepolicy\': \'single_host_origin\',
				\'accesstype\' : \'"offline"\',
				\'requestvisibleactions\': \'http://schemas.google.com/AddActivity\',
				\'scope\': \'https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/plus.profile.emails.read\'
			};

			gapi.signin.render(\'googleButton\', additionalParams);

			additionalParams.callback = \'onSignInCallback\';

			var signinButton = document.getElementById(\'customBtn\');
			signinButton.addEventListener(\'click\', function() {
				gapi.auth.signIn(additionalParams); // Will use page level configuration
			});
		}
		';

		return $script;
    }

    public static function generateJsLoginScript()
    {
    	$script = '
        var first_run = true;
    	var helper = (function() {
			var authResult = undefined;

			return {
				onSignInCallback: function(authResultResp) {
					if (authResultResp["access_token"]) {
						// The user is signed in
						authResult = authResultResp;
						// After we load the Google+ API, render the profile data from Google+.
						gapi.client.load("plus","v1",this.renderProfile);
					} else if (authResultResp["error"]) {
						// There was an error, which means the user is not signed in.
					}
				},

				renderProfile: function() {
					var request = gapi.client.plus.people.get( {"userId" : "me"} );
					request.execute( function(profile) {
						if (profile.error) {
							alert(profile.error);
							return;
						}

						window.location.href=document.URL + "?mod=log&accessToken=" + authResult.access_token + "&type=google";
						console.log("profile", profile);
					});
				},
			};
		})();
		function onSignInCallback(authResult) {
            if(!first_run) {
			    // helper.onSignInCallback(authResult);
            }
            first_run = false;
		}
		';

		return $script;
    } 

    public static function generateCssScript($moduleName)
    {
    	$style = '
		    #customBtn {
				display: inline-block;
				background: #dd4b39;
				color: white;
				width: 165px;
				border-radius: 5px;
				white-space: nowrap;
		    }
		    #customBtn:hover {
				background: #e74b37;
				cursor: pointer;
		    }
		    span.label {
				font-weight: bold;
		    }
		    span.icon {
		    	background: url(\'' . JURI::base().'modules/' . $moduleName . '/tmpl/img/btn_red_32.png\') transparent 5px 50% no-repeat;
				display: inline-block;
				vertical-align: middle;
				width: 35px;
				height: 35px;
				border-right: #bb3f30 1px solid;
		    }
		    span.buttonText {
				display: inline-block;
				vertical-align: middle;
				padding-left: 35px;
				padding-right: 35px;
				font-size: 14px;
				font-weight: bold;
				/* Use the Roboto font that is loaded in the <head> */
				font-family: \'Roboto\',arial,sans-serif;
		    }
		'; 

		return $style;
    }
}
?>