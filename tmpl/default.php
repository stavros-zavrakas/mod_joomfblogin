<?php // no direct access
	defined('_JEXEC') or die('Restricted access'); 
  JHTML::_('behavior.keepalive');
  JHTML::_('behavior.modal');
?>

<div class="<?php echo $params->get('moduleclass_sfx'); ?>">
	<?php 
		if (empty($fbAppId) || empty($fbAppSecret)) 
		{
			echo "Critical Error!";
		} 
		else 
		{
			if (!$user->guest) 
			{
				echo htmlspecialchars($user->get('name'));
        
				?>
				<form action="" method="post" id="login-form">
					<div class="logout-button">
						<input type="submit" name="Submit" class="btn btn-primary" value="<?php echo JText::_('JLOGOUT'); ?>" />
						<input type="hidden" name="option" value="com_users" />
						<input type="hidden" name="task" value="user.logout" />
						<input type="hidden" name="return" value="/" />
						<?php echo JHtml::_('form.token'); ?>
					</div>
				</form>
				<?php
			}
			else 
			{
				$document = JFactory::getDocument();

				$document->addScriptDeclaration('var fbAppId = ' . $fbAppId);
				$document->addScript(JURI::root() . 'media/mod_joomfblogin/js/mod_joomfblogin_functions.js');
				$document->addScript(JURI::root() . 'media/mod_joomfblogin/js/mod_joomfblogin_facebook.js');
				echo $fbButton;
			}
		}
	?>
	<div id="fb-root"></div>
</div>
