<?php

/**
 * @package     com_boilerplate
 * @version     1.0.0
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     MIT License (MIT) see LICENSE.txt
 * @author      Jörg Schöneburg <info@joerg-schoeneburg.de> - https://joerg-schoeneburg.de
 */

namespace Joomla\Component\Boilerplate\Administrator\Controller;

use Joomla\CMS\MVC\Controller\FormController;

defined('_JEXEC') or die;

/**
 * Boilerplates list controller class.
 *
 * @since  1.0.0
 */
class BoilerplateController extends FormController
{
	/**
	 * The view list string
	 * @var string
	 */
	protected $view_list = 'boilerplates';
}
