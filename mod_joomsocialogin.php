<?php
/**
 * Joomla Social Login!
 * 
 * @package        Joomla
 * @subpackage     Modules
 * @link           http://joomla.zavrakas.me/
 * @license        GNU/GPL, see http://www.gnu.org/licenses/gpl.html
 * @author         Stavros Zavrakas <stavros@zavrakas.me>
 * mod_joomsociallogin is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');
// Include the syndicate functions only once
require_once(dirname(__FILE__).'/helper.php');

$socialEnabled = array();
$socialEnabled['facebook'] = modJoomHelper::getParamName($params, 'fb_is_enabled');
$socialEnabled['google'] = modJoomHelper::getParamName($params, 'google_is_enabled');
$socialEnabled['linkedIn'] = modJoomHelper::getParamName($params, 'linkedIn_is_enabled');

// If we have at least on of the social logins enabled, we need the logic in the if statement.
if(in_array("1", $socialEnabled, true)) {
	$socialData = array();

	$jInput = JFactory::getApplication()->input;
	$loginType = $jInput->get('type', null, 'STRING');
	$accessToken = $jInput->get('accessToken', null, 'STRING');

	$user = JFactory::getUser();
	$referer = modJoomHelper::getReferer();

	if(isset($socialEnabled['facebook'])) {
		modJoomHelper::initFacebookLibrary();
		$socialData['facebook'] = array();
		$socialData['facebook']['appId'] = modJoomHelper::getParamName($params, 'fb_app_id');
		$socialData['facebook']['appSecret'] = modJoomHelper::getParamName($params, 'fb_app_secret');
		if($loginType == "facebook") {
			$facebook = modJoomFacebookLoginHelper::initFacebookSdk($socialData['facebook']['appId'], $socialData['facebook']['appSecret']);
			$fbuser = modJoomFacebookLoginHelper::initFacebookUser($facebook, $accessToken);
			modJoomFacebookLoginHelper::loginFacebookUser($fbuser, $user, $facebook, $accessToken, $referer);
		}
		else 
		{
			$permissions = modJoomFacebookLoginHelper::getFacebookPermissions($params);
			
			$socialData['facebook']['jsSdk'] = modJoomFacebookLoginHelper::loadFacebookJavascriptSdk();
			$socialData['facebook']['jsLoginScript'] = modJoomFacebookLoginHelper::generateJsLoginScript($socialData['facebook']['appId']);
			$socialData['facebook']['button'] = modJoomFacebookLoginHelper::generateFacebookButton($params, $permissions);
		}
	}

	if(isset($socialEnabled['google'])) {
		modJoomHelper::initGoogleLibrary();
		$socialData['google']['appId'] = modJoomHelper::getParamName($params, 'google_app_id');
		$socialData['google']['appSecret'] = modJoomHelper::getParamName($params, 'google_app_secret');
		if($loginType == "google") {
			$google = modJoomGoogleLoginHelper::initGoogleSdk($socialData['google']['appId'], $socialData['google']['appSecret']);
			$googleUser = modJoomGoogleLoginHelper::initGoogleUser($google, $accessToken);
			modJoomGoogleLoginHelper::loginGoogleUser($user, $googleUser, $accessToken, $referer);
		}
		else
		{
			$socialData['google']['jsSdk'] = modJoomGoogleLoginHelper::loadGoogleJavascriptSdk($socialData['google']['appId']);
			$socialData['google']['jsLoginScript'] = modJoomGoogleLoginHelper::generateJsLoginScript();
			$socialData['google']['cssScript'] = modJoomGoogleLoginHelper::generateCssScript($module->module);
			$socialData['google']['button'] = modJoomGoogleLoginHelper::generateGoogleButton($params, $socialData['google']['appId']);
		}
	}

	if(isset($socialEnabled['linkedIn'])) {

	}

	require(JModuleHelper::getLayoutPath('mod_joomsocialogin'));
}
?>