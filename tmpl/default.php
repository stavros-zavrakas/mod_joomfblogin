<?php // no direct access
	defined('_JEXEC') or die('Restricted access'); 
?>

<div class="<?php echo $params->get('moduleclass_sfx'); ?>">
	<?php 
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
			foreach ($socialData as $key => $socialNetwork) {
				$document->addScriptDeclaration('var ' . $key . 'AppId = "' . $socialNetwork['appId'] . '";');

				echo $socialNetwork['button'] . '<br><br>';
				$document->addScriptDeclaration($socialNetwork['jsSdk']);
				$document->addScriptDeclaration($socialNetwork['jsLoginScript']);
				if(isset($socialNetwork['cssScript']))
				{
					$document->addStyleDeclaration($socialNetwork['cssScript']);
				}
			}

			$jquery = modJoomHelper::getJquery();
			$document->addScriptDeclaration($jquery);
		}
	?>
	<div id="fb-root"></div>
</div>
