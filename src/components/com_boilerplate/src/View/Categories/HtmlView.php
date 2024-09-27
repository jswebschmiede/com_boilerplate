<?php

/**
 * @package     com_boilerplate
 * @version     1.0.0
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     MIT License (MIT) see LICENSE.txt
 * @author      Jörg Schöneburg <info@joerg-schoeneburg.de> - https://joerg-schoeneburg.de
 */

namespace Joomla\Component\Boilerplate\Site\View\Categories;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;


defined('_JEXEC') or die;

/**
 * Boilerplates list view
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * @var    string  The name of the extension for the category
	 * @since  3.2
	 */
	protected $extension = 'com_boilerplate';

	/**
	 * An array of items
	 *
	 * @var    array
	 * @since  1.6
	 */
	protected $items = [];

	/**
	 * The component params
	 *
	 * @var    \Joomla\Registry\Registry
	 * @since  1.6
	 */
	protected $params;

	/**
	 * The ID of the item
	 *
	 * @var    int
	 * @since  1.6
	 */
	protected $item_id;

	/**
	 * The pagination object
	 *
	 * @var    \Joomla\CMS\Pagination\Pagination
	 * @since  1.6
	 */
	protected $pagination;

	/**
	 * The state object
	 *
	 * @var    \Joomla\CMS\Object\CMSObject
	 * @since  1.6
	 */
	protected $state;


	public function display($tpl = null): void
	{
		$model = $this->getModel();
		$this->items = $model->getItems();
		$this->state = $model->getState();
		$this->params = $this->state->get('params');

		foreach ($this->items as &$item) {
			$item->link = Route::_("index.php?option=com_boilerplate&view=boilerplate&id={$item->id}");
			$item->category_link = Route::_("index.php?option=com_boilerplate&view=category&id={$item->catid}");
		}

		parent::display($tpl);
	}
}
