<?php
/**
 * Hello World! Module Entry Point
 * 
 * @package    Joomla.Tutorials
 * @subpackage Modules
 * @link http://dev.joomla.org/component/option,com_jd-wiki/Itemid,31/id,tutorials:modules/
 * @license        GNU/GPL, see LICENSE.php
 * mod_joomfblogin is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');
// Include the syndicate functions only once
require_once(dirname(__FILE__).'/helper.php');
require_once(dirname(__FILE__).'/src/facebook.php');
 
$jInput = JFactory::getApplication()->input;
$fbAccessToken = $jInput->get('fbAccessToken', null, 'STRING');

$user = JFactory::getUser();
$fbAppId = modJoomFacebookLoginHelper::getParamName($params, 'fb_app_id');
$fbAppSecret = modJoomFacebookLoginHelper::getParamName($params, 'fb_app_secret');
$fbButton = '<div class="login facebook-login"> Facebook Login </div>';
$referer = modJoomFacebookLoginHelper::getReferer();

$facebook = new Facebook(array(
	'appId'  => $fbAppId,
	'secret' => $fbAppSecret,
	'allowSignedRequest' => false
));

if($fbAccessToken) {
	$facebook->setAccessToken($fbAccessToken);
}
$fbuser = $facebook->getUser();
if ($fbuser && $fbAccessToken && $user->guest)
{
	
	try {
		$fbuser = $facebook->api('/me');

		$isJoomlaUser = modJoomFacebookLoginHelper::getUserIdByParam('email', $fbuser['email']);

		if(empty($isJoomlaUser)) 
		{
			// Store the user object in the DB (register)
			jimport('joomla.user.helper');
			$password = JUserHelper::genRandomPassword(5);
			$joomlaUser = modJoomFacebookLoginHelper::registerUser($fbuser['name'], $fbuser['username'], $password, $fbuser['email']);
		}
		else 
		{
			// Retrieve the user object from DB
			$joomlaUser = JFactory::getUser($isJoomlaUser);
		}
		// Login the User

		modJoomFacebookLoginHelper::login($joomlaUser, $referer);
	}
	catch (FacebookApiException $e) {
		// error_log($e);
		$fbuser = null;
	}
}
require(JModuleHelper::getLayoutPath( 'mod_joomfblogin'));
?>