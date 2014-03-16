<?php
defined('_JEXEC') or die;

class modJoomGoogleLoginHelper
{
    public static function getDimensionByImageName($imageName) 
    {
        $googleDimenions = array(
            '01FacebookButtonXLarge.png' => array('width' => '300px', 'height' => '66px'),
            '02FacebookButtonLarge.png' => array('width' => '260px', 'height' => '57px'),
            '03FacebookButtonMedium.png' => array('width' => '220px', 'height' => '48px'),
            '04FacebookButtonSmall.png' => array('width' => '180px', 'height' => '39px')
        );
        
        return $googleDimenions[$imageName]; 
    }

    public static function generateGoogleButton($params)
    {
        $googleButtonText = modJoomHelper::getParamName($params, 'googleButtonText');
        $googleButtonName = modJoomHelper::getParamName($params, 'googleButton');
        $googleButtonArgs = modJoomHelper::getDimensionByImageName($googleButtonName);
        $googleButtonUrl = JURI::root() . 'media/mod_joomfblogin/img/' . $googleButtonName;
        $googleButtonStyle = 'style="width: ' . $googleButtonArgs['width'] . '; height: ' . $googleButtonArgs['height'] . '; background-image:url(' . $googleButtonUrl . ');"';
        $googleButton = '<div class="login" ' . $googleButtonStyle . '><div class="facebook-text">' . $googleButtonText . '</div></div>';

        return $googleButton;
    }
}
?>