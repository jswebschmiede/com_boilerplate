<?php

/**
 * @package     com_boilerplate
 * @version     1.0.0
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     MIT License (MIT) see LICENSE.txt
 * @author      Jörg Schöneburg <info@joerg-schoeneburg.de> - https://joerg-schoeneburg.de
 */

namespace Joomla\Component\Boilerplate\Administrator\View\Boilerplate;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\Component\Content\Administrator\Helper\ContentHelper;
use Joomla\Component\Boilerplate\Administrator\Model\BoilerplateModel;

/**
 * View class for a list of boilerplates.
 *
 * @since   1.0.0
 */
class HtmlView extends BaseHtmlView
{


	/**
	 * An object of item
	 *
	 * @var    object
	 * @since  1.6
	 */
	protected $item;

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
	 * @var    \JForm
	 * @since  1.6
	 */
	protected $form;

	/**
	 * The actions the user is authorised to perform
	 *
	 * @var  \JObject
	 */
	protected $canDo;

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
	 * Get the form
	 *
	 * @return Form
	 */
	public function getForm(): Form
	{
		return $this->form;
	}

	/**
	 * Get the item
	 *
	 * @return object
	 */
	public function getItem(): object
	{
		return $this->item;
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
		/** @var BoilerplateModel $model */
		$model = $this->getModel();
		$this->form = $model->getForm();
		$this->item = $model->getItem();
		$this->state = $model->getState();

		if (count($errors = $this->get('Errors'))) {
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 * @since   1.6
	 */
	protected function addToolbar(): void
	{
		Factory::getApplication()->input->set('hidemainmenu', true);
		$isNew = ($this->item->id == 0);

		ToolbarHelper::title(
			Text::_('COM_BOILERPLATE_TITLE_BOILERPLATE_' . ($isNew ? 'ADD_BOILERPLATE' : 'EDIT_BOILERPLATE'))
		);

		// Check if the user has permission to edit
		$canDo = ContentHelper::getActions('com_boilerplate');

		if ($canDo->get('core.create') || $canDo->get('core.edit')) {
			ToolbarHelper::apply('boilerplate.apply');
			ToolbarHelper::save('boilerplate.save');
			ToolbarHelper::save2new('boilerplate.save2new');
		}

		if ($isNew) {
			ToolbarHelper::cancel('boilerplate.cancel', 'JTOOLBAR_CANCEL');
		} else {
			ToolbarHelper::cancel('boilerplate.cancel', 'JTOOLBAR_CLOSE');
		}

		// Add a "Save & Close" button
		if ($canDo->get('core.edit')) {
			ToolbarHelper::save2copy('boilerplate.save2copy');
		}
	}
}
