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
use Joomla\Database\DatabaseQuery;
use Joomla\Database\ParameterType;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\Component\Content\Administrator\Extension\ContentComponent;

defined('_JEXEC') or die;

class CategoryModel extends ListModel
{
	/**
	 * Model context string.
	 *
	 * @var     string
	 */
	public $_context = 'com_boilerplate.category';

	/**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var     string
	 */
	protected $_extension = 'com_boilerplate';

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
	 * Populates the state for the model.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 * @return  void
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null): void
	{
		$app = Factory::getApplication();
		$input = $app->input;

		$pk = $this->getId();

		$this->setState('category.id', $pk);

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

		// List state information
		$value = $input->get('limit', $app->get('list_limit', 0), 'uint');
		$this->setState('list.limit', $value);

		$value = $input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $value);

		// Load the parameters. Merge Global and Menu Item params into new object
		$params = $app->getParams();
		$this->setState('params', $params);

		$user = $this->getCurrentUser();

		if ((!$user->authorise('core.edit.state', 'com_boilerplate')) && (!$user->authorise('core.edit', 'com_boilerplate'))) {
			// Filter on published for those who do not have edit or edit.state rights.
			$this->setState('filter.published', ContentComponent::CONDITION_PUBLISHED);
		}

	}

	/**
	 * Get the ID of the category.
	 * 
	 * @return int
	 * @throws \Exception
	 */
	private function getId(): int
	{
		$app = Factory::getApplication();

		$id = $app->input->getInt('id');
		$params = $app->getParams();

		$paramId = $params->get('id');
		if ($paramId && $id === null) {
			return (int) $paramId;
		}

		return (int) $id;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.extension');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.parentId');

		return parent::getStoreId($id);
	}

	/**
	 * Summary of getListQuery
	 * @return \Joomla\Database\DatabaseQuery|false
	 */
	protected function getListQuery(): DatabaseQuery|false
	{
		$pk = (int) $this->getState('category.id');

		// Load the item
		$db = $this->getDatabase();
		$query = $db->getQuery(true);

		$query->select($this->getState('list.select', [
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
			$db->quoteName('l.title', 'language_title'),
			$db->quoteName('l.image', 'language_image'),
			$db->quoteName('c.title', 'category_title'),
			$db->quoteName('c.path', 'category_route'),
			$db->quoteName('c.alias', 'category_alias'),
			$db->quoteName('c.language', 'category_language'),
			$db->quoteName('u.name', 'author'),
			$db->quoteName('parent.title', 'parent_title'),
			$db->quoteName('parent.id', 'parent_id'),
			$db->quoteName('parent.path', 'parent_route'),
			$db->quoteName('parent.alias', 'parent_alias'),
			$db->quoteName('parent.language', 'parent_language')
		]));

		$query->from($db->quoteName('#__boilerplate_boilerplate', 'a'))
			->join('INNER', $db->quoteName('#__categories', 'c'), $db->quoteName('c.id') . ' = ' . $db->quoteName('a.catid'))
			->join('LEFT', $db->quoteName('#__users', 'u'), $db->quoteName('u.id') . ' = ' . $db->quoteName('a.created_by'))
			->join('LEFT', $db->quoteName('#__categories', 'parent'), $db->quoteName('parent.id') . ' = ' . $db->quoteName('c.parent_id'))
			->join('LEFT', $db->quoteName('#__languages', 'l') . ' ON ' . $db->quoteName('l.lang_code') . ' = ' . $db->quoteName('a.language'));

		$query->where($db->quoteName('a.catid') . ' = :catid')
			->bind(':catid', $pk, ParameterType::INTEGER);

		// Filter by published state
		$condition = (int) $this->getState('filter.published');

		// Category has to be published and article has to be published.
		$query->where($db->quoteName('a.state') . ' = :condition')
			->bind(':condition', $condition, ParameterType::INTEGER);

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

		$query->order($ordering);

		return $query;
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