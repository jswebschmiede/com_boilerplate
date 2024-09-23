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
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\Component\Content\Administrator\Helper\ContentHelper;

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
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 */
	public function display($tpl = null)
	{
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		if (count($errors = $this->get('Errors'))) {
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		$this->addToolbar();

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
		ToolbarHelper::title(Text::_('COM_BOILERPLATE_TITLE_BOILERPLATES'));

		$canDo = ContentHelper::getActions('com_boilerplate');

		if ($canDo->get('core.create')) {
			ToolbarHelper::addNew('boilerplate.add');
		}

		if ($canDo->get('core.edit')) {
			ToolbarHelper::editList('boilerplate.edit');
		}

		if ($canDo->get('core.delete')) {
			ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'boilerplates.delete');
		}
	}
}
