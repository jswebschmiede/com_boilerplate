<?php

/**
 * @package     com_boilerplate
 * @version     1.0.0
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     MIT License (MIT) see LICENSE.txt
 * @author      Jörg Schöneburg <info@joerg-schoeneburg.de> - https://joerg-schoeneburg.de
 */

namespace Joomla\Component\Boilerplate\Site\View\Categories;

use Joomla\CMS\MVC\View\CategoriesView;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Content categories view.
 *
 * @since  1.5
 */
class HtmlView extends CategoriesView
{

	public $maxLevelcat = 0;

	/**
	 * Language key for default page heading
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $pageHeading = 'JGLOBAL_ARTICLES';

	/**
	 * @var    string  The name of the extension for the category
	 * @since  3.2
	 */
	protected $extension = 'com_boilerplate';

	/**
	 * Get the items for the categories view.
	 * @return mixed
	 */
	public function getItems(): mixed
	{
		$items = [
			$this->parent->id => $this->get('Items')
		];

		return $items;
	}
}
