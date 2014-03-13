<?php // no direct access
	defined( '_JEXEC' ) or die( 'Restricted access' ); 
?>

<div class="<?php echo $params->get('moduleclass_sfx'); ?>">
	<!-- The fields that exists will be displayed only if are not empty -->
	<div> <?php echo $params->get('pretext'); ?> </div>

	<?php 
		if( empty($fbAppId) || empty($fbAppSecret) ) {
			echo "Critical Error!";
		} else {
			$document = JFactory::getDocument();

			$document->addScriptDeclaration('var fbAppId = ' . $fbAppId);
			$document->addScript(JURI::root() . 'media/mod_joomfblogin/js/mod_joomfblogin_functions.js');
			$document->addScript(JURI::root() . 'media/mod_joomfblogin/js/mod_joomfblogin_facebook.js');
			?>
			<div class="login facebook-login">
				Facebook Login
			</div>
			<?php
		}
	?>
	<div id="fb-root"></div>
	<div> <?php echo $params->get('posttext'); ?> </div>
</div>
