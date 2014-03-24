<?php
defined('_JEXEC') or die;

class modJoomGoogleLoginHelper
{

	public static function initLinkedInSdk($appId, $secretAppId) 
    {
        
    }

    public static function initLinkedInUser($linkedIn, $accessToken = null) 
    {
    	
	}

	public static function loginLinkedInUser($user, $linkedInUser, $accessToken, $referer)
	{
		
	}

    public static function generateLinkedInButton($params, $appId)
    {
		$linkedInButton = modJoomHelper::getParamName($params, 'linkedInButton');
		$linkedInButtonText = modJoomHelper::getParamName($params, 'linkedInButtonText');

		$linkedInButton  = '';

        return $linkedInButton;
    }

    public static function loadLinkedInJavascriptSdk($appId)
    {
    	$script = '

		';

		return $script;
    }

    public static function generateJsLoginScript()
    {
    	$script = '
        
		';

		return $script;
    } 

    public static function generateCssScript($moduleName)
    {
    	$style = '
		'; 

		return $style;
    }
}
?>