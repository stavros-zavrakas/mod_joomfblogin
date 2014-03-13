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
defined( '_JEXEC' ) or die( 'Restricted access' );
// Include the syndicate functions only once
require_once( dirname(__FILE__).'/helper.php' );
require_once dirname(__FILE__).'/src/facebook.php';
 
$user = JFactory::getUser();
$fb_app_id = modHelloWorldHelper::getFbAppId( $params );
$fb_app_secret = modHelloWorldHelper::getFbAppSecret( $params );

$facebook = new Facebook(array(
	'appId'  => $fb_app_id,
	'secret' => $fb_app_secret,
));
$fbuser = $facebook->getUser();
echo $fbuser . " ; " . $user->guest;

// if ($fbuser && $user->guest ) {
// 	try {
// 		$user_profile = $facebook->api('/me');
// 		$isjoomlauser = modJLVFacebookLoginHelper::getJoomlaId($user_profile['email']);
// 		if((int)$isjoomlauser==0) {
// 			jimport( 'joomla.user.helper' );
// 			$password = JUserHelper::genRandomPassword(5);
// 			$joomlauser = modJLVFacebookLoginHelper::addJoomlaUser($user_profile['name'], $user_profile['username'], $password, $user_profile['email']);
// 		}
// 		else {
// 			$joomlauser = JFactory::getUser($isjoomlauser);
// 		}
// 		modJLVFacebookLoginHelper::loginFb($joomlauser,$return);
// 	}
// 	catch (FacebookApiException $e) {
// 		error_log($e);
// 		$fbuser = null;
// 	}
// }

require( JModuleHelper::getLayoutPath( 'mod_joomfblogin' ) );
?>