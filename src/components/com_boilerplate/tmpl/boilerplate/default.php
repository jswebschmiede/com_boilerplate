<?php

/**
 * @package     com_boilerplate
 * @version     1.0.0
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     MIT License (MIT) see LICENSE.txt
 * @author      Jörg Schöneburg <info@joerg-schoeneburg.de> - https://joerg-schoeneburg.de
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/** @var \Joomla\Component\Boilerplate\Site\View\Boilerplate\HtmlView $this */

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useScript('component.boilerplate.site');
$wa->useStyle('component.boilerplate.site');
?>

<?php if ($this->params->get('show_page_heading')): ?>
	<div class="page-header">
		<h1>
			<?php if ($this->escape($this->params->get('page_heading'))): ?>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			<?php else: ?>
				<?php echo $this->escape($this->params->get('page_title')); ?>
			<?php endif; ?>
		</h1>
	</div>
<?php endif; ?>

<div class="com_boilerplate boilerplate-<?php echo $this->item->id; ?> boilerplate-item ">
	<div class="row">
		<div class="col-12 mb-4">
			<h2><?php echo $this->item->name; ?></h2>
			<div class="details small mb-3">
				<time datetime="<?php echo $this->item->created; ?>">
					<?php echo Text::_('JDATE'); ?>:
					<?php echo JDate::getInstance($this->item->created)->format('d.m.Y'); ?>
				</time>
				<div><?php echo Text::_('JAUTHOR'); ?>: <?php echo $this->item->author; ?></div>
				<div><?php echo Text::_('JCATEGORY'); ?>:
					<a href="<?php echo $this->item->category_link; ?>"><?php echo $this->item->category_title; ?></a>
				</div>
			</div>
			<?php echo $this->item->description; ?>
		</div>
	</div>
</div>