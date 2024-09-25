<?php

/**
 * @package     com_boilerplate
 * @version     1.0.0
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     MIT License (MIT) see LICENSE.txt
 * @author      Jörg Schöneburg <info@joerg-schoeneburg.de> - https://joerg-schoeneburg.de
 */

namespace Joomla\Component\Boilerplate\Administrator\Controller;

use Joomla\CMS\Router\Route;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Versioning\VersionableControllerTrait;

defined('_JEXEC') or die;

/**
 * Boilerplates list controller class.
 *
 * @since  1.0.0
 */
class BoilerplateController extends FormController
{
	use VersionableControllerTrait;

	/**
	 * The view list string
	 * @var string
	 */
	protected $view_list = 'boilerplates';

	/**
	 * Method to run batch operations.
	 *
	 * @param   string  $model  The model
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 */
	public function batch($model = null)
	{
		$this->checkToken();

		// Set the model
		$model = $this->getModel('Boilerplate', '', []);

		// Preset the redirect
		$this->setRedirect(Route::_('index.php?option=com_boilerplate&view=boilerplates' . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}
}
