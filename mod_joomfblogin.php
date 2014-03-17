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
require_once(dirname(__FILE__).'/googleHelper.php');
require_once(dirname(__FILE__).'/fbSrc/facebook.php');
// require_once(dirname(__FILE__).'/google-api-php-client/src/Google/Client.php');
 
$socialEnabled = array();
$socialEnabled['facebook'] = modJoomHelper::getParamName($params, 'fb_is_enabled');
$socialEnabled['google'] = modJoomHelper::getParamName($params, 'google_is_enabled');

// If we have at least on of the social logins enabled, we need the logic in the if statement.
if(in_array("1", $socialEnabled, true)) {
	$socialData = array();

	$jInput = JFactory::getApplication()->input;
	$loginType = $jInput->get('type', null, 'STRING');
	$accessToken = $jInput->get('accessToken', null, 'STRING');

	$user = JFactory::getUser();
	$referer = modJoomHelper::getReferer();

	if(isset($socialEnabled['facebook'])) {
		$socialData['facebook'] = array();
		$socialData['facebook']['appId'] = modJoomHelper::getParamName($params, 'fb_app_id');
		$socialData['facebook']['appSecret'] = modJoomHelper::getParamName($params, 'fb_app_secret');
		if($loginType == "facebook") {
			$facebook = modJoomFacebookLoginHelper::initFacebookSdk($socialData['facebook']['appId'], $socialData['facebook']['appSecret']);
			$fbuser = modJoomFacebookLoginHelper::initFacebookUser($facebook, $accessToken);
			modJoomFacebookLoginHelper::loginFacebookUser($fbuser, $user, $facebook);
		}
		else 
		{
			$socialData['facebook']['jsSdk'] = modJoomFacebookLoginHelper::loadFacebookJavascriptSdk();
			$socialData['facebook']['jsLoginScript'] = modJoomFacebookLoginHelper::generateJsLoginScript($socialData['facebook']['appId']);
			$socialData['facebook']['button'] = modJoomFacebookLoginHelper::generateFacebookButton($params);
		}
	}

	if(isset($socialEnabled['google'])) {
		$socialData['google']['appId'] = modJoomHelper::getParamName($params, 'google_app_id');
		$socialData['google']['appSecret'] = modJoomHelper::getParamName($params, 'google_app_secret');
		if($loginType == "google") {
			// @todo: this is exactly what we need:
			// https://developers.google.com/+/web/signin/redirect-uri-flow
			// 1) Init GooglePhpSDK.
			// 2) GetUser Data.
			// 3) Login/Register the user.

			// $google = modJoomGoogleLoginHelper::initGoogleSdk($socialData['google']['appId'], $socialData['google']['appSecret']);
		}
		else
		{
			$socialData['google']['jsSdk'] = modJoomGoogleLoginHelper::loadGoogleJavascriptSdk();
			$socialData['google']['jsLoginScript'] = modJoomGoogleLoginHelper::generateJsLoginScript();
			$socialData['google']['button'] = modJoomGoogleLoginHelper::generateGoogleButton($params, $socialData['google']['appId']);
		}
	}
	require(JModuleHelper::getLayoutPath('mod_joomfblogin'));
}
?>