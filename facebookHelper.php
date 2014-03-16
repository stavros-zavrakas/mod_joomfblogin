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