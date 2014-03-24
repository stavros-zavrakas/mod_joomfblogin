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

    public static function initFacebookLibrary()
    {
        require_once(dirname(__FILE__).'/facebookHelper.php');
        require_once(dirname(__FILE__).'/fbSrc/facebook.php');
    }

    public static function initGoogleLibrary()
    {
        require_once(dirname(__FILE__).'/googleHelper.php');
        require_once(dirname(__FILE__).'/googleSrc/Google_Client.php');
        require_once(dirname(__FILE__).'/googleSrc/contrib/Google_PlusService.php');
    }

    public static function initLinkedInLibrary()
    {
        require_once(dirname(__FILE__).'/linkedInHelper.php');
        require_once(dirname(__FILE__).'/linkedinSrc/linkedin.php');
    }

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

    public static function getUserIdByParam($param, $email) 
    {
        $db     = JFactory::getDbo();
        $query = "SELECT id FROM #__users WHERE " . $param . "='" . $email . "';";
        $db->setQuery($query);
        $jUser = $db->loadResult();
        
        return $jUser;
    }

    public static function getReferer() 
    {
        $url = JRequest::getURI();
        $url = explode("?mod=log", $url);

        return $url[0];
    }

    public static function getJquery() 
    {
        $jquery  = "if (typeof jQuery == 'undefined')";
        $jquery .= "{";
        $jquery .= "    var head = document.getElementsByTagName(\"head\")[0]; ";
        $jquery .= "    script = document.createElement('script'); ";
        $jquery .= "    script.id = 'jQuery'; ";
        $jquery .= "    script.type = 'text/javascript'; ";
        $jquery .= "    script.src = '//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js'; ";
        $jquery .= "    head.appendChild(script); ";
        $jquery .= "}";

        return $jquery;
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

    public static function login($joomlaUser, $redirect, $socialPrefix, $socialUid, $accessToken) {
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
        $user->setParam($socialPrefix . '_uid', $socialUid);
        $user->setParam($socialPrefix . '_access_token', $accessToken);
        $user->save();

        $app->redirect($redirect);
    }
}
?>