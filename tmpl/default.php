<?php // no direct access
	defined( '_JEXEC' ) or die( 'Restricted access' ); 
?>

<div class="<?php echo $params->get('moduleclass_sfx'); ?>">
	<!-- The fields that exists will be displayed only if are not empty -->
	<div> <?php echo $params->get('pretext'); ?> </div>

	<?php 
		if(!empty($fb_app_id)) {
			echo "'" . $fb_app_id . "'"; 
			$document = JFactory::getDocument();

			$document->addScript(JURI::root() . 'media/mod_joomfblogin/js/functions.js');
		} else {
			echo "Critical Error!";
		}
	?>

	<div> <?php echo $params->get('posttext'); ?> </div>
</div>
