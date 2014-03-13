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
 
$user = JFactory::getUser();
$isGuest = modJoomFacebookLoginHelper::getParamName($user, 'guest');
$fbAppId = modJoomFacebookLoginHelper::getParamName($params, 'fb_app_id');
$fbAppSecret = modJoomFacebookLoginHelper::getParamName($params, 'fb_app_secret');

$facebook = new Facebook(array(
	'appId'  => $fbAppId,
	'secret' => $fbAppSecret,
));
$fbuser = $facebook->getUser();

if ($fbuser && $isGuest) 
{
	$fbButton = '<div class="login facebook-login"> Facebook Login </div>';
}
else
{

}

require(JModuleHelper::getLayoutPath( 'mod_joomfblogin'));
?>