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
        if (isset($accessToken)) {
            $facebook->setAccessToken($accessToken);
        }

        $fbuser = $facebook->getUser();

        return $fbuser;
    }

    public static function loginFacebookUser($fbuser, $user, $facebook)
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

                modJoomHelper::login($joomlaUser, $referer, $fbuser, $accessToken);
            }
            catch (FacebookApiException $e) {
                // error_log($e);
                $fbuser = null;
            }
        }
    }

    public static function getDimensionByImageName($imageName) 
    {
        $fbDimenions = array(
            '01FacebookButtonXLarge.png' => array('width' => '300px', 'height' => '66px'),
            '02FacebookButtonLarge.png' => array('width' => '260px', 'height' => '57px'),
            '03FacebookButtonMedium.png' => array('width' => '220px', 'height' => '48px'),
            '04FacebookButtonSmall.png' => array('width' => '180px', 'height' => '39px')
        );
        
        return $fbDimenions[$imageName]; 
    }

    public static function generateFacebookButton($params)
    {
        $fbButtonText = modJoomHelper::getParamName($params, 'fbButtonText');
        $fbButtonName = modJoomHelper::getParamName($params, 'fbButton');
        $fbButtonArgs = modJoomHelper::getDimensionByImageName($fbButtonName);
        $fbButtonUrl = JURI::root() . 'media/mod_joomfblogin/img/' . $fbButtonName;
        $fbButtonStyle = 'style="width: ' . $fbButtonArgs['width'] . '; height: ' . $fbButtonArgs['height'] . '; background-image:url(' . $fbButtonUrl . ');"';
        $fbButton = '<div class="login facebook-login" ' . $fbButtonStyle . '><div class="facebook-text">' . $fbButtonText . '</div></div>';

        return $fbButton;
    }
}
?>