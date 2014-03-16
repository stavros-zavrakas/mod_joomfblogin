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

class modJoomHelper
{
    /**
     * Retrieves the a param, depending on the name that the function accepts as name.
     *
     * @param array $params An object containing the module parameters
     * @param string $name The name of the parameter that we want to retrieve
     * @access public
     */    
    public static function getParamName($params, $name)
    {
        return $params->get($name);
    }

    public static function getUserIdByParam($param, $email) {
        $db     = JFactory::getDbo();
        $query = "SELECT id FROM #__users WHERE " . $param . "='" . $email . "';";
        $db->setQuery($query);
        $jUser = $db->loadResult();
        
        return $jUser;
    }

    public static function getReferer() {
        $url = JRequest::getURI();
        $url = preg_replace('/([?&])fbAccessToken=[^&]+(&|$)/','',$url);

        return $url[0];
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
        $fbButtonText = modJoomFacebookLoginHelper::getParamName($params, 'fbButtonText');
        $fbButtonName = modJoomFacebookLoginHelper::getParamName($params, 'fbButton');
        $fbButtonArgs = modJoomFacebookLoginHelper::getDimensionByImageName($fbButtonName);
        $fbButtonUrl = JURI::root() . 'media/mod_joomfblogin/img/' . $fbButtonName;
        $fbButtonStyle = 'style="width: ' . $fbButtonArgs['width'] . '; height: ' . $fbButtonArgs['height'] . '; background-image:url(' . $fbButtonUrl . ');"';
        $fbButton = '<div class="login facebook-login" ' . $fbButtonStyle . '><div class="facebook-text">' . $fbButtonText . '</div></div>';

        return $fbButton;
    }

    public static function registerUser($name, $username, $password, $email) {
        jimport('joomla.application.component.helper');
        $config = JComponentHelper::getParams('com_users');
        // Default to Registered.
        $defaultUserGroup = $config->get('new_usertype', 2);
        
        $data = array(
            "name" => $name, 
            "username" => $username, 
            "password" => $password,
            "password2" => $password,
            "groups" => array($defaultUserGroup),
            "email" => $email
        );

        $user = clone(JFactory::getUser());

        //Write to database
        if(!$user->bind($data)) {
            throw new Exception("Could not bind data. Error: " . $user->getError());
        }
        if (!$user->save()) {
            throw new Exception("Could not save user. Error: " . $user->getError());
        }

        return $user;
    }

    public static function login($joomlaUser, $redirect, $fbuser, $fbAccessToken) {
        $db     = JFactory::getDbo();
        $query = "SELECT password FROM #__users WHERE id='" . $joomlaUser->id . "';";
        $db->setQuery($query);
        $oldpass = $db->loadResult();

        jimport('joomla.user.helper');
        $password = JUserHelper::genRandomPassword(5);
        $query = "UPDATE #__users SET password='" . md5($password) . "' WHERE id='" . $joomlaUser->id . "';";
        $db->setQuery($query);
        $db->query();
        
        $app = JFactory::getApplication();

        $credentials = array();
        $credentials['username'] = $joomlaUser->username;
        $credentials['password'] = $password;

        $options = array();
        $options['remember']    = true;
        $options['silent']      = true;

        $app->login($credentials, $options);
        
        $query = "UPDATE #__users SET password='" . $oldpass . "' WHERE id='" . $joomlaUser->id . "';";
        $db->setQuery($query);
        $db->query();
        
        $user = JFactory::getUser();
        $user->setParam('fb_uid', $fbuser['id']);
        $user->setParam('fb_access_token', $fbAccessToken);
        $user->save();

        $app->redirect($redirect);
    }
}
?>