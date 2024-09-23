<?php

/**
 * @package     com_boilerplate
 * @version     1.0.0
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     MIT License (MIT) see LICENSE.txt
 * @author      Jörg Schöneburg <info@joerg-schoeneburg.de> - https://joerg-schoeneburg.de
 */

namespace Joomla\Component\Boilerplate\Administrator\Controller;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\Controller\AdminController;

defined('_JEXEC') or die;

/**
 * Boilerplates list controller class.
 *
 * @since  1.0.0
 */
class BoilerplatesController extends AdminController
{
	/**
	 * Proxy for getModel
	 * @since    1.6
	 *
	 * @param string $name
	 * @param string $prefix
	 * @param array $config
	 *
	 * @return BaseDatabaseModel|bool
	 */
	public function getModel($name = 'Boilerplate', $prefix = 'Administrator', $config = []): BaseDatabaseModel|bool
	{
		return parent::getModel($name, $prefix, ['ignore_request' => true]);
	}
}