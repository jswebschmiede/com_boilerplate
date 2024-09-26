<?php

/**
 * @package     com_boilerplate
 * @version     1.0.0
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     MIT License (MIT) see LICENSE.txt
 * @author      Jörg Schöneburg <info@joerg-schoeneburg.de> - https://joerg-schoeneburg.de
 */

namespace Joomla\Component\Boilerplate\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseQuery;
use Joomla\Database\ParameterType;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\FormFactoryInterface;

/**
 * Methods supporting a list of boilerplate records.
 *
 * @since  1.0.0
 */
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
			];
		}

		parent::__construct($config);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  \Joomla\Database\DatabaseQuery
	 *
	 * @since   1.0.0
	 */
	protected function getListQuery(): DatabaseQuery
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState('list.select', 'a.*')
		);

		$query->from($db->quoteName('#__boilerplate_boilerplate', 'a'));

		$query->select('b.name AS `created_by`');

		$query->leftJoin($db->quoteName('#__users', 'b') . ' ON b.id = a.created_by');

		// Filter by published state
		$published = $this->getState('filter.published');

		if (is_numeric($published)) {
			$query->where($db->quoteName('a.state') . ' = :published')
				->bind(':published', $published, ParameterType::INTEGER);
		} elseif ($published === '') {
			$query->where($db->quoteName('a.state') . ' IN (0, 1)');
		}

		// Filter by search in title
		if ($search = $this->getState('filter.search')) {
			if (stripos($search, 'id:') === 0) {
				$search = (int) substr($search, 3);
				$query->where($db->quoteName('a.id') . ' = :search')
					->bind(':search', $search, ParameterType::INTEGER);
			} else {
				$search = '%' . str_replace(' ', '%', trim($search)) . '%';
				$query->where('(' . $db->quoteName('a.name') . ' LIKE :search1 OR ' . $db->quoteName('a.alias') . ' LIKE :search2)')
					->bind([':search1', ':search2'], $search);
			}
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where($db->quoteName('a.language') . ' = :language')
				->bind(':language', $language);
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.name');
		$orderDirn = $this->state->get('list.direction', 'ASC');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		if ($orderCol === 'name') {
			$ordering = [
				$db->quoteName('a.name') . ' ' . $db->escape($orderDirn),
			];
		} else {
			$ordering = $db->escape($orderCol) . ' ' . $db->escape($orderDirn);
		}

		$query->order($ordering);

		return $query;
	}

	/**
	 * Summary of getStoreId
	 * @param string $id
	 * @return string
	 */
	protected function getStoreId($id = ''): string
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.language');

		return parent::getStoreId($id);
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  Table  A Table object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'Boilerplate', $prefix = 'Administrator', $config = []): Table
	{
		return parent::getTable($type, $prefix, $config);
	}


	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = 'a.name', $direction = 'asc')
	{
		// Load the parameters.
		$this->setState('params', ComponentHelper::getParams('com_boilerplate'));

		// List state information.
		parent::populateState($ordering, $direction);
	}

	/**
	 * Summary of getItems
	 * @return array
	 */
	public function getItems(): array
	{
		return parent::getItems();
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