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
			}

$style = '
    #customBtn {
      display: inline-block;
      background: #dd4b39;
      color: white;
      width: 165px;
      border-radius: 5px;
      white-space: nowrap;
    }
    #customBtn:hover {
      background: #e74b37;
      cursor: pointer;
    }
    span.label {
      font-weight: bold;
    }
    span.icon {
      display: inline-block;
      vertical-align: middle;
      width: 35px;
      height: 35px;
      border-right: #bb3f30 1px solid;
    }
    span.buttonText {
      display: inline-block;
      vertical-align: middle;
      padding-left: 35px;
      padding-right: 35px;
      font-size: 14px;
      font-weight: bold;
      /* Use the Roboto font that is loaded in the <head> */
      font-family: \'Roboto\',arial,sans-serif;
    }
'; 
$document->addStyleDeclaration($style);

			$jquery = modJoomHelper::getJquery();
			$document->addScriptDeclaration($jquery);
		}
	?>
	<div id="fb-root"></div>
</div>
