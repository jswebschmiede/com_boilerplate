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

/** @var \Joomla\Component\Boilerplate\Site\View\Category\HtmlView $this */

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useScript('component.boilerplate.site');
$wa->useStyle('component.boilerplate.site');

$items = $this->get('Items');
?>

<div class="com_boilerplate categories">
	<?php if ($this->params->get('show_page_heading')): ?>
		<div class="row">
			<div class="page-header mb-4">
				<h1>
					<?php if ($this->escape($this->params->get('page_heading'))): ?>
						<?php echo $this->escape($this->params->get('page_heading')); ?>
					<?php else: ?>
						<?php echo $this->escape($this->params->get('page_title')); ?>
					<?php endif; ?>
				</h1>
			</div>
		</div>
	<?php endif; ?>

	<?php foreach ($items as $item): ?>
		<div class="row">
			<div class="col-12 mb-4">
				<h2><?php echo $item->name; ?></h2>
				<div class="details small mb-3">
					<time datetime="<?php echo $item->created; ?>">
						<?php echo Text::_('JDATE'); ?>:
						<?php echo JDate::getInstance($item->created)->format('d.m.Y'); ?>
					</time>
					<div><?php echo Text::_('JAUTHOR'); ?>: <?php echo $item->author; ?></div>
					<div>
						<?php echo Text::_('JCATEGORY'); ?>:
						<a href="<?php echo $item->category_link; ?>"><?php echo $item->category_title; ?></a>
					</div>
				</div>
				<?php echo $item->description; ?>

				<a href="<?php echo $item->link; ?>" class="btn btn-primary">
					More
				</a>
			</div>
		</div>
	<?php endforeach; ?>

	<?php if ($this->pagination->pagesTotal > 1): ?>
		<div class="com-boilerplate-boilerplates__pagination">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php endif; ?>
</div>