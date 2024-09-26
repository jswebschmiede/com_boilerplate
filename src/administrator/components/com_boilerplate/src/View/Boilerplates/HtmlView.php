<?php

/**
 * @package     com_boilerplate
 * @version     1.0.0
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     MIT License (MIT) see LICENSE.txt
 * @author      Jörg Schöneburg <info@joerg-schoeneburg.de> - https://joerg-schoeneburg.de
 */

namespace Joomla\Component\Boilerplate\Administrator\View\Boilerplates;

defined('_JEXEC') or die;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\Toolbar\Button\DropdownButton;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\Component\Content\Administrator\Helper\ContentHelper;
use Joomla\Component\Boilerplate\Administrator\Model\BoilerplatesModel;

/**
 * View class for a list of boilerplates.
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{


	/**
	 * An array of items
	 *
	 * @var    array
	 * @since  1.6
	 */
	protected $items = [];

	/**
	 * The pagination object
	 *
	 * @var    Pagination
	 * @since  1.6
	 */
	protected $pagination;

	/**
	 * The model state
	 *
	 * @var    Registry
	 * @since  1.6
	 */
	protected $state;

	/**
	 * Filter form
	 *
	 * @var    Form
	 * @since  1.6
	 */
	public $filterForm;

	/**
	 * The active search filters
	 *
	 * @var    array
	 * @since  1.6
	 */
	public $activeFilters = [];

	/**
	 * Is this view an Empty State
	 *
	 * @var  boolean
	 * @since 4.0.0
	 */
	private $isEmptyState = false;

	/**
	 * Get the state
	 *
	 * @return Registry
	 */
	public function getState(): Registry
	{
		return $this->state;
	}

	/**
	 * Get the items
	 *
	 * @return array
	 */
	public function getItems(): array
	{
		return $this->items;
	}

	/**
	 * Get the pagination
	 *
	 * @return Pagination
	 */
	public function getPagination(): Pagination
	{
		return $this->pagination;
	}


	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  A template file to load. [optional]
	 *
	 * @return  void
	 *
	 * @since   1.6
	 * @throws  \Exception
	 */
	public function display($tpl = null): void
	{
		/** @var BoilerplatesModel $model */
		$model = $this->getModel();
		$this->items = $model->getItems();
		$this->pagination = $model->getPagination();
		$this->state = $model->getState();
		$this->filterForm = $model->getFilterForm();
		$this->activeFilters = $model->getActiveFilters();

		if (!\count($this->items) && $this->isEmptyState = $this->get('IsEmptyState')) {
			$this->setLayout('emptystate');
		}

		if (count($errors = $this->get('Errors'))) {
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		$this->addToolbar();

		// We do not need to filter by language when multilingual is disabled
		if (!Multilanguage::isEnabled()) {
			unset($this->activeFilters['language']);
			$this->filterForm->removeField('language', 'filter');
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since  1.6
	 */
	protected function addToolbar(): void
	{
		$canDo = ContentHelper::getActions('com_boilerplate');
		$user = $this->getCurrentUser();
		$toolbar = Toolbar::getInstance();

		ToolbarHelper::title(Text::_('COM_BOILERPLATE_MANAGER_BOILERPLATES'), 'bookmark boilerplates');

		if ($canDo->get('core.create') || \count($user->getAuthorisedCategories('com_boilerplate', 'core.create')) > 0) {
			$toolbar->addNew('boilerplate.add');
		}

		if (!$this->isEmptyState && ($canDo->get('core.edit.state') || ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')))) {
			/** @var  DropdownButton $dropdown */
			$dropdown = $toolbar->dropdownButton('status-group', 'JTOOLBAR_CHANGE_STATUS')
				->toggleSplit(false)
				->icon('icon-ellipsis-h')
				->buttonClass('btn btn-action')
				->listCheck(true);

			$childBar = $dropdown->getChildToolbar();

			if ($canDo->get('core.edit.state')) {
				if ($this->state->get('filter.published') != 2) {
					$childBar->publish('boilerplates.publish')->listCheck(true);

					$childBar->unpublish('boilerplates.unpublish')->listCheck(true);
				}

				if ($this->state->get('filter.published') != -1) {
					if ($this->state->get('filter.published') != 2) {
						$childBar->archive('boilerplates.archive')->listCheck(true);
					} elseif ($this->state->get('filter.published') == 2) {
						$childBar->publish('publish')->task('boilerplates.publish')->listCheck(true);
					}
				}

				$childBar->checkin('boilerplates.checkin');

				if ($this->state->get('filter.published') != -2) {
					$childBar->trash('boilerplates.trash')->listCheck(true);
				}
			}

			if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
				$toolbar->delete('boilerplates.delete', 'JTOOLBAR_EMPTY_TRASH')
					->message('JGLOBAL_CONFIRM_DELETE')
					->listCheck(true);
			}

			// Add a batch button
			if (
				$user->authorise('core.create', 'com_banners')
				&& $user->authorise('core.edit', 'com_boilerplate')
				&& $user->authorise('core.edit.state', 'com_boilerplate')
			) {
				$childBar->popupButton('batch', 'JTOOLBAR_BATCH')
					->popupType('inline')
					->textHeader(Text::_('COM_BOILERPLATE_BATCH_OPTIONS'))
					->url('#joomla-dialog-batch')
					->modalWidth('800px')
					->modalHeight('fit-content')
					->listCheck(true);
			}
		}

		if ($user->authorise('core.admin', 'com_boilerplate') || $user->authorise('core.options', 'com_boilerplate')) {
			$toolbar->preferences('com_boilerplate');
		}
	}
}
