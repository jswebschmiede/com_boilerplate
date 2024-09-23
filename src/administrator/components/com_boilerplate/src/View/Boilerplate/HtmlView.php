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
use Joomla\CMS\Toolbar\ToolbarFactoryInterface;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\Component\Content\Administrator\Helper\ContentHelper;

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
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @throws \Exception
	 * @since   1.6
	 */
	public function display($tpl = null): void
	{
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');

		if (count($errors = $this->get('Errors'))) {
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);
		$isNew = $this->item->id == 0;

		$toolbar = Factory::getContainer()->get(ToolbarFactoryInterface::class)->createToolbar('toolbar');

		ToolbarHelper::title(
			Text::_('COM_BOILERPLATE_TITLE_BOILERPLATE' . ($isNew ? 'ADD_BOILERPLATE' : 'EDIT_BOILERPLATE'))
		);

		$canDo = ContentHelper::getActions('com_boilerplate');
		if ($canDo->get('core.create')) {
			$toolbar->apply('boilerplate.apply');
			$toolbar->save('boilerplate.save');
		}
		if ($isNew) {
			$toolbar->cancel('boilerplate.cancel', 'JTOOLBAR_CANCEL');
		} else {
			$toolbar->cancel('boilerplate.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
