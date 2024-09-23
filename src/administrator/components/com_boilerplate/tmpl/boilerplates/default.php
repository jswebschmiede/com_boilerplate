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

/** @var \Joomla\Component\Boilerplate\Administrator\View\Boilerplates\HtmlView $this */

$state = $this->getState();
$items = $this->getItems();
$pagination = $this->getPagination();

$listOrder = $this->escape($state->get('list.ordering'));
$listDirn = $this->escape($state->get('list.direction'));

$states = array(
	'0' => Text::_('JUNPUBLISHED'),
	'1' => Text::_('JPUBLISHED'),
	'2' => Text::_('JARCHIVED'),
	'-2' => Text::_('JTRASHED')
);

$editIcon = '<span class="fa fa-pen-square mr-2" aria-hidden="true"></span>';

?>

<form action="<?php echo Route::_('index.php?option=com_boilerplate&view=boilerplates'); ?>" method="post"
	name="adminForm" id="adminForm">
	<?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

	<?php if (empty($items)): ?>
		<div class="alert alert-info">
			<span class="fa fa-info-circle" aria-hidden="true"></span>
			<span class="sr-only"><?php echo Text::_('INFO'); ?></span>
			<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
	<?php else: ?>
		<table class="table itemList" id="boilerplateTable">
			<thead>
				<tr>
					<td class="w-1 text-center">
						<?php echo HTMLHelper::_('grid.checkall'); ?>
					</td>

					<th scope="col" class="w-1 text-center d-none d-md-table-cell">
						<?php echo HTMLHelper::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
					</th>

					<th scope="col" class="w-5 text-center">
						<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
					</th>

					<th scope="col" class="w-20">
						<?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
					</th>

					<th scope="col" class="w-25">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_BOILERPLATE_BOILERPLATES_LABEL_DESCRIPTION', 'a.description', $listDirn, $listOrder); ?>
					</th>

					<th scope="col" class="w-10">
						<?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_CREATED_DATE', 'a.created', $listDirn, $listOrder); ?>
					</th>

					<th scope="col" class="w-5 d-none d-md-table-cell">
						<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($items as $i => $item): ?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="text-center">
							<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
						</td>

						<td class="text-center d-none d-md-table-cell">
							<?php /* TODO: Add ordering logic */ ?>
							<span class="sortable-handler">
								<span class="icon-ellipsis-v"></span>
							</span>
						</td>

						<td class="article-status text-center">
							<?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'boilerplates.', $canChange, 'cb'); ?>
						</td>

						<th scope="row" class="has-context">
							<a class="hasTooltip"
								href="<?php echo Route::_('index.php?option=com_boilerplate&task=boilerplate.edit&id=' . $item->id); ?>">
								<?php echo $editIcon; ?>
								<?php echo $this->escape($item->title); ?>
							</a>
						</th>

						<td class="description">
							<?php echo $this->escape($item->description); ?>
						</td>

						<td class="created small">
							<?php echo HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC2')); ?>
						</td>

						<td class="id d-none d-md-table-cell">
							<?php echo $item->id; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<div class="pagination center">
			<?php echo $pagination->getListFooter(); ?>
		</div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />

		<?php echo HTMLHelper::_('form.token'); ?>
	<?php endif; ?>
</form>