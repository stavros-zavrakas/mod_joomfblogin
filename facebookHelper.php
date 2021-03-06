<?php
/**
 * Helper class for Hello World! module
 * 
 * @package    Joomla.Tutorials
 * @subpackage Modules
 * @link http://dev.joomla.org/component/option,com_jd-wiki/Itemid,31/id,tutorials:modules/
 * @license        GNU/GPL, see LICENSE.php
 * mod_helloworld is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
defined('_JEXEC') or die;

class modJoomFacebookLoginHelper
{
    public static function initFacebookSdk($appId, $secretAppId) 
    {
        $facebook = new Facebook(array(
            'appId'  => $appId,
            'secret' => $secretAppId,
            'allowSignedRequest' => false
        ));

        return $facebook;
    }

    public static function initFacebookUser($facebook, $accessToken = null) 
    {
        if (isset($accessToken)) 
        {
            $facebook->setAccessToken($accessToken);
        }

        $fbuser = $facebook->getUser();

        return $fbuser;
    }

    public static function loginFacebookUser($fbuser, $user, $facebook, $accessToken, $referer)
    {
        if ($fbuser && $user->guest)
        {
            try {
                $fbuser = $facebook->api('/me');

                $isJoomlaUser = modJoomHelper::getUserIdByParam('email', $fbuser['email']);

                if(empty($isJoomlaUser)) 
                {
                    // Store the user object in the DB (register)
                    jimport('joomla.user.helper');
                    $password = JUserHelper::genRandomPassword(5);
                    $joomlaUser = modJoomHelper::registerUser($fbuser['name'], $fbuser['username'], $password, $fbuser['email']);
                }
                else 
                {
                    // Retrieve the user object from DB
                    $joomlaUser = JFactory::getUser($isJoomlaUser);
                }
                // Login the User

                modJoomHelper::login($joomlaUser, $referer, 'fb', $fbuser['id'], $accessToken);
            }
            catch (FacebookApiException $e) {
                // error_log($e);
                $fbuser = null;
            }
        }
    }

    public static function generateFacebookButton($params, $permissions)
    {
        $permissionsStr = implode(",", $permissions);
        error_log("permissionsStr: " . $permissionsStr);

        $fbButtonText = modJoomHelper::getParamName($params, 'fbButtonText');
        $fbButtonSize = modJoomHelper::getParamName($params, 'fbButton');
        $fbButton = '<fb:login-button  onlogin="facebookLogin();"  size="' . $fbButtonSize . '" scope="' . $permissionsStr . '">' . $fbButtonText . '</fb:login-button><br>';

        return $fbButton;
    }

    public static function getFacebookPermissions($params)
    {
        $permissions = array();
        $permissions[] = "email";
        $permissions[] = "user_birthday";
        $permissions[] = "user_about_me";
        $permissions[] = "user_likes";

        $publish_actions = modJoomHelper::getParamName($params, 'fb_publish_actions');
        if($publish_actions) 
        { 
            $permissions[] = "publish_actions"; 
        }

        return $permissions;
    }

    public static function loadFacebookJavascriptSdk()
    {
        $script = '
        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/all.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, \'script\', \'facebook-jssdk\'));
        ';

        return $script;
    }

    public static function generateJsLoginScript($facebookAppId)
    {
        $script = '
        if (jQuery("#fb-root").length == 0)
        {
            var joomFbSdk = \'<div id="fb-root"></div>\';
            jQuery("body").append(joomFbSdk);
        }

        function facebookLogin()
        {
            FB.getLoginStatus(function(response) {
                if (response.status === \'connected\') {
                    var accessToken = response.authResponse.accessToken;
                    fetchFbUserData(accessToken);
                } 
                else 
                {
                    initiateFbLogin();
                }
            });
        }

        function initiateFbLogin()
        {
            FB.login(function(response) {
                if (response.authResponse) 
                {
                    var accessToken = response.authResponse.accessToken;
                    fetchFbUserData(accessToken);
                } 
                else
                {
                    //User cancelled login or did not fully authorize.
                }
            }, {
                scope: "email, user_birthday, user_about_me, user_likes, publish_actions"
            });
        }

        function fetchFbUserData(accessToken)
        {
            FB.api("/me", function(response) {
                window.location.href=document.URL + "?mod=log&accessToken=" + accessToken + "&type=facebook";
            });
        }

        window.fbAsyncInit = function() {
            FB.init({
                appId      : ' . $facebookAppId . ',
                status     : true,
                cookie     : true,
                xfbml      : true
            });

            // jQuery(".facebook-login").click(function(event) {
            //    event.preventDefault();
            function facebookLogin() {
                FB.login(function(response) {
                    if (response.authResponse) 
                    {
                        var accessToken = response.authResponse.accessToken;
                        FB.api("/me", function(response) {
                            window.location.href=document.URL + "?accessToken=" + accessToken + "&type=facebook";
                        });
                    } 
                    else
                    {
                        //User cancelled login or did not fully authorize.
                    }
                }, {
                    scope: "email, user_birthday, user_about_me, user_likes, publish_actions"
                });
            }
            // });
        };
        ';

        return $script;
    }
}
?>