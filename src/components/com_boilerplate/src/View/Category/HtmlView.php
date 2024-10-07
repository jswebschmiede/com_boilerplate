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
use Joomla\CMS\Categories\Categories;
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

	/**
	 * The category object
	 *
	 * @var    \Joomla\CMS\Categories\CategoryNode
	 * @since  1.0.0
	 */
	protected $category;

	/**
	 * The active menu item
	 *
	 * @var    \Joomla\CMS\Menu\MenuItem
	 * @since  1.0.0
	 */
	protected $menu;

	public function display($tpl = null): void
	{
		$app = Factory::getApplication();
		$this->menu = $app->getMenu()->getActive();

		$this->items = $this->get('Items');
		$this->state = $this->get('State');
		$this->params = $this->state->get('params');
		$this->pagination = $this->get('Pagination');

		// Get the category
		$this->category = Categories::getInstance('Boilerplate')->get($this->state->get('category.id'));

		foreach ($this->items as &$item) {
			$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;

			// No link for ROOT category
			if ($item->parent_alias === 'root') {
				$item->parent_id = null;
			}

			$item->link = Route::_(RouteHelper::getBoilerplateRoute($item->id, $item->catid, $item->language));
			$item->category_link = Route::_(RouteHelper::getCategoryRoute($item->catid, $item->language));
		}

		$this->prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return  void
	 */
	protected function prepareDocument(): void
	{
		$app = Factory::getApplication();

		// Get ID of the category from active menu item
		$menu = $this->menu;

		if (
			$menu && $menu->component == 'com_boilerplate' && isset($menu->query['view'])
			&& \in_array($menu->query['view'], ['categories', 'category'])
		) {
			$id = $menu->query['id'];
		} else {
			$id = 0;
		}

		$path = [['title' => $this->category->title, 'link' => '']];
		$category = $this->category->getParent();

		/** @var \Joomla\CMS\Categories\CategoryNode $category */
		while ($category !== null && $category->id !== 'root' && $category->id != $id) {
			$path[] = ['title' => $category->title, 'link' => RouteHelper::getCategoryRoute($category->id, $category->language)];
			$category = $category->getParent();
		}

		$path = array_reverse($path);

		foreach ($path as $item) {
			$app->getPathway()->addItem($item['title'], $item['link']);
		}
	}
}
