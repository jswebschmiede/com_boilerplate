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
use Joomla\CMS\Router\Route;
use Joomla\Component\Boilerplate\Site\Helper\RouteHelper;

/** @var \Joomla\Component\Boilerplate\Site\View\Categories\HtmlView $this */

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useScript('com_boilerplate.main');
$wa->useStyle('com_boilerplate.style');

$items = $this->getItems();
?>

<div class="com_boilerplate categories-list">
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

	<ul>
		<?php foreach ($items[$this->parent->id] as $id => $item): ?>
			<li>
				<a href="<?php echo Route::_(RouteHelper::getCategoryRoute($item->id, $item->language)); ?>">
					<?php echo $item->title; ?>
				</a>
			</li>

			<?php if (count($item->getChildren()) > 0 && $this->maxLevelcat > 1): ?>
				<ul class="com-content-categories__children" id="category-<?php echo $item->id; ?>">
					<?php
					$items[$item->id] = $item->getChildren();
					$this->parent = $item;
					$this->maxLevelcat--;
					?>

					<?php foreach ($items[$this->parent->id] as $id => $item): ?>
						<li>
							<a href="<?php echo Route::_(RouteHelper::getCategoryRoute($item->id, $item->language)); ?>">
								<?php echo $item->title; ?>
							</a>
						</li>
					<?php endforeach; ?>

					<?php $this->parent = $item->getParent(); ?>
					<?php $this->maxLevelcat++; ?>
				</ul>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>
</div>