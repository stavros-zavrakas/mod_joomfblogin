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
require_once(dirname(__FILE__).'/facebookHelper.php');
require_once(dirname(__FILE__).'/src/facebook.php');
 
$socialEnabled = array();
$socialEnabled['facebook'] = modJoomHelper::getParamName($params, 'fb_is_enabled');
$socialEnabled['google'] = modJoomHelper::getParamName($params, 'google_is_enabled');

// If we have at least on of the social logins enabled, we need the logic in the if statement.
if(in_array("1", $socialEnabled, true)) {
	$jInput = JFactory::getApplication()->input;
	$loginType = $jInput->get('type', null, 'STRING');
	$accessToken = $jInput->get('accessToken', null, 'STRING');

	$user = JFactory::getUser();
	$referer = modJoomHelper::getReferer();

	if(isset($socialEnabled['facebook'])) {
		$fbAppId = modJoomHelper::getParamName($params, 'fb_app_id');
		$fbAppSecret = modJoomHelper::getParamName($params, 'fb_app_secret');
		if($loginType == "facebook") {
			$facebook = modJoomFacebookLoginHelper::initFacebookSdk($fbAppId, $fbAppSecret);
			$fbuser = modJoomFacebookLoginHelper::initFacebookUser($facebook, $accessToken);
			modJoomFacebookLoginHelper::loginFacebookUser($fbuser, $user, $facebook);
		}
		else 
		{
			$fbButton = modJoomFacebookLoginHelper::generateFacebookButton($params);
		}
		require(JModuleHelper::getLayoutPath('mod_joomfblogin'));
	}

	if(isset($socialEnabled['google'])) {
		$googleAppId = modJoomHelper::getParamName($params, 'google_app_id');
		$googleAppSecret = modJoomHelper::getParamName($params, 'google_app_secret');
		// @todo: this is exactly what we need:
		// https://developers.google.com/+/web/signin/redirect-uri-flow
		
	}
}
?>