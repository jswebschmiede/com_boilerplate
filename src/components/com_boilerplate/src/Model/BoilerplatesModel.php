<?php

/**
 * @package     com_boilerplate
 * @version     1.0.0
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     MIT License (MIT) see LICENSE.txt
 * @author      Jörg Schöneburg <info@joerg-schoeneburg.de> - https://joerg-schoeneburg.de
 */

namespace Joomla\Component\Boilerplate\Site\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormHelper;
use Joomla\Database\DatabaseQuery;
use Joomla\Database\ParameterType;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\Component\Content\Administrator\Extension\ContentComponent;

defined('_JEXEC') or die;

class BoilerplatesModel extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     \Joomla\CMS\MVC\Model\BaseDatabaseModel
	 * @since   1.0.0
	 */
	public function __construct($config = [])
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = [
				'id',
				'a.id',
				'name',
				'a.name',
				'alias',
				'a.alias',
				'state',
				'a.state',
				'ordering',
				'a.ordering',
				'language',
				'a.language',
				'published',
				'created',
				'a.created',
				'catid',
				'a.catid',
				'category_title',
				'level',
				'c.level',
			];
		}

		parent::__construct($config);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  DatabaseQuery
	 *
	 * @since   1.0.0
	 */
	protected function getListQuery(): DatabaseQuery
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				[
					$db->quoteName('a.id'),
					$db->quoteName('a.name'),
					$db->quoteName('a.alias'),
					$db->quoteName('a.catid'),
					$db->quoteName('a.state'),
					$db->quoteName('a.ordering'),
					$db->quoteName('a.language'),
					$db->quoteName('a.metakey'),
					$db->quoteName('a.created_by'),
					$db->quoteName('a.created'),
					$db->quoteName('a.modified'),
					$db->quoteName('a.modified_by'),
					$db->quoteName('a.description'),
				]
			)
		)
			->select(
				[
					$db->quoteName('l.title', 'language_title'),
					$db->quoteName('l.image', 'language_image'),
					$db->quoteName('c.title', 'category_title'),
					$db->quoteName('c.path', 'category_route'),
					$db->quoteName('c.alias', 'category_alias'),
					$db->quoteName('c.language', 'category_language'),
					$db->quoteName('c.published', 'category_published'),
					$db->quoteName('u.name', 'author'),
				]
			)
			->from($db->quoteName('#__boilerplate_boilerplate', 'a'))
			->join('LEFT', $db->quoteName('#__languages', 'l'), $db->quoteName('l.lang_code') . ' = ' . $db->quoteName('a.language'))
			->join('LEFT', $db->quoteName('#__categories', 'c'), $db->quoteName('c.id') . ' = ' . $db->quoteName('a.catid'))
			->join('LEFT', $db->quoteName('#__users', 'u'), $db->quoteName('u.id') . ' = ' . $db->quoteName('a.created_by'));

		// Add the list ordering clause.
		$orderCol = $this->getState('list.ordering', 'a.name');
		$orderDirn = $this->getState('list.direction', 'ASC');

		if ($orderCol === 'a.ordering' || $orderCol === 'category_title') {
			$ordering = [
				$db->quoteName('c.title') . ' ' . $db->escape($orderDirn),
				$db->quoteName('a.ordering') . ' ' . $db->escape($orderDirn),
			];
		} else {
			$ordering = $db->escape($orderCol) . ' ' . $db->escape($orderDirn);
		}

		// Filter by published state
		$condition = (int) $this->getState('filter.published');

		// Category has to be published and article has to be published.
		$query->where($db->quoteName('c.published') . ' = 1')
			->where($db->quoteName('a.state') . ' = :condition')
			->bind(':condition', $condition, ParameterType::INTEGER);

		$query->order($ordering);

		return $query;
	}

	protected function populateState($ordering = 'ordering', $direction = 'ASC'): void
	{
		$app = Factory::getApplication();
		$input = $app->input;

		// List state information
		$value = $input->get('limit', $app->get('list_limit', 0), 'uint');
		$this->setState('list.limit', $value);

		$value = $input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $value);

		$value = $input->get('filter_tag', 0, 'uint');
		$this->setState('filter.tag', $value);

		$orderCol = $input->get('filter_order', 'a.ordering');

		if (!\in_array($orderCol, $this->filter_fields)) {
			$orderCol = 'a.ordering';
		}

		$this->setState('list.ordering', $orderCol);

		$listOrder = $input->get('filter_order_Dir', 'ASC');

		if (!\in_array(strtoupper($listOrder), ['ASC', 'DESC', ''])) {
			$listOrder = 'ASC';
		}

		$this->setState('list.direction', $listOrder);

		$user = $this->getCurrentUser();

		if ((!$user->authorise('core.edit.state', 'com_content')) && (!$user->authorise('core.edit', 'com_content'))) {
			// Filter on published for those who do not have edit or edit.state rights.
			$this->setState('filter.published', ContentComponent::CONDITION_PUBLISHED);
		}
		$params = $app->getParams();
		$this->setState('params', $params);
		$user = $this->getCurrentUser();

		if ((!$user->authorise('core.edit.state', 'com_content')) && (!$user->authorise('core.edit', 'com_content'))) {
			// Filter on published for those who do not have edit or edit.state rights.
			$this->setState('filter.published', ContentComponent::CONDITION_PUBLISHED);
		}

		$this->setState('filter.language', Multilanguage::isEnabled());

		// Process show_noauth parameter
		if ((!$params->get('show_noauth')) || (!ComponentHelper::getParams('com_content')->get('show_noauth'))) {
			$this->setState('filter.access', true);
		} else {
			$this->setState('filter.access', false);
		}

		$this->setState('layout', $input->getString('layout'));
		$this->setState('filter.language', Multilanguage::isEnabled());

		// Process show_noauth parameter
		if ((!$params->get('show_noauth')) || (!ComponentHelper::getParams('com_boilerplate')->get('show_noauth'))) {
			$this->setState('filter.access', true);
		} else {
			$this->setState('filter.access', false);
		}

		$this->setState('layout', $input->getString('layout'));
	}

	/**
	 * Summary of getFormFactory
	 * @return FormFactoryInterface
	 */
	public function getFormFactory(): FormFactoryInterface
	{
		return parent::getFormFactory();
	}
}
