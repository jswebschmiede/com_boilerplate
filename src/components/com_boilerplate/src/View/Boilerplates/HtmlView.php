<?php

/**
 * @package     com_boilerplate
 * @version     1.0.0
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     MIT License (MIT) see LICENSE.txt
 * @author      Jörg Schöneburg <info@joerg-schoeneburg.de> - https://joerg-schoeneburg.de
 */

namespace Joomla\Component\Boilerplate\Site\View\Boilerplates;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die;

/**
 * Boilerplates list view
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
	 * @param $tpl
	 *
	 * @throws \Exception
	 */
	public function display($tpl = null): void
	{
		$app = Factory::getApplication();
		$menus = $app->getMenu();
		$menu = $menus->getActive();
		$model = $this->getModel();

		$this->item_id = $app->input->getInt('Itemid');
		$this->state = $model->getState();
		$this->params = $this->state->get('params');
		$this->items = $model->getItems();
		$this->pagination = $model->getPagination();

		foreach ($this->items as &$item) {
			$item->link = Route::_("index.php?option=com_boilerplate&view=boilerplate&id={$item->id}");
		}

		// Set the page heading
		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', Text::_('JGLOBAL_ARTICLES'));
		}

		$title = $this->params->get('page_title', '');

		// Set the page title according to the site settings
		if (empty($title)) {
			$title = $app->get('sitename');
		} elseif ((int) $app->get('sitename_pagetitles', 0) === 1) {
			$title = Text::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		} elseif ((int) $app->get('sitename_pagetitles', 0) === 2) {
			$title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description')) {
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('robots')) {
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}

		parent::display($tpl);
	}
}