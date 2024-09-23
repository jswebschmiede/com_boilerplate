<?php

/**
 * @package     com_boilerplate
 * @version     1.0.0
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     MIT License (MIT) see LICENSE.txt
 * @author      Jörg Schöneburg <info@joerg-schoeneburg.de> - https://joerg-schoeneburg.de
 */

use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

defined('_JEXEC') or die;

/** @var \Joomla\Component\Boilerplate\Administrator\View\Boilerplate\HtmlView $this */

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_contenthistory');
$wa->useScript('keepalive')
	->useScript('form.validate')
	->useScript('com_contenthistory.admin-history-versions');

$state = $this->getState();
$item = $this->getItem();
$form = $this->getForm();
?>

<form
	action="<?php echo Route::_('index.php?option=com_boilerplate&view=boilerplate&layout=edit&id=' . (int) $item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="adminForm" class="form-validate">

	<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="row-fluid">
		<div class="span10 form-horizontal">
			<?php
			$fieldset = $form->getFieldset('details');
			foreach ($fieldset as $field) {
				echo $field->renderField();
			}
			?>
		</div>
		<input type="hidden" name="task" value="" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
	<div id="validation-form-failed" data-backend-detail="boilerplate"
		data-message="<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>">
	</div>

	<input type="hidden" name="task" value="" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>