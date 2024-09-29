<?php

/**
 * @package     com_boilerplate
 * @version     1.0.0
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     MIT License (MIT) see LICENSE.txt
 * @author      Jörg Schöneburg <info@joerg-schoeneburg.de> - https://joerg-schoeneburg.de
 */

namespace Joomla\Component\Boilerplate\Site\View\Category;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\Component\Boilerplate\Site\Helper\RouteHelper;

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
		$this->items = $model->getItem();
		$this->state = $model->getState();
		$this->params = $this->state->get('params');

		foreach ($this->items as &$item) {
			$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;

			// No link for ROOT category
			if ($item->parent_alias === 'root') {
				$item->parent_id = null;
			}

			$item->link = Route::_(RouteHelper::getBoilerplateRoute($item->id, $item->catid, $item->language));
			$item->category_link = Route::_(RouteHelper::getCategoryRoute($item->catid, $item->language));
		}

		parent::display($tpl);
	}
}
