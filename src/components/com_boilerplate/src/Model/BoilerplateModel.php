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
use Joomla\CMS\Language\Text;
use Joomla\Database\ParameterType;
use Joomla\CMS\MVC\Model\ItemModel;

defined('_JEXEC') or die;

/**
 * Methods supporting a single boilerplate record.
 *
 * @since  1.0.0
 */
class BoilerplateModel extends ItemModel
{
	/**
	 * Model context string.
	 *
	 * @var string
	 */
	protected $_context = 'com_boilerplate.boilerplate';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function populateState(): void
	{
		$app = Factory::getApplication();

		// Load state from the request.
		$pk = $app->input->getInt('id') ?? $this->getId();
		$this->setState('boilerplate.id', $pk);
	}


	/**
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
	 * @param int|null $pk
	 * @return object|bool
	 * @since 1.0.0
	 */
	public function getItem($pk = null): object|bool
	{
		$pk = (int) ($pk ?: $this->getState('boilerplate.id'));


		if ($this->_item === null) {
			$this->_item = [];
		}


		if (!isset($this->_item[$pk])) {

			try {
				$db = $this->getDatabase();
				$query = $db->getQuery(true);

				$query->select($this->getState(
					'item.select',
					[
						$db->quoteName('a.id'),
						$db->quoteName('a.state'),
						$db->quoteName('a.ordering'),
						$db->quoteName('a.name'),
						$db->quoteName('a.alias'),
						$db->quoteName('a.catid'),
						$db->quoteName('a.description'),
						$db->quoteName('a.created'),
						$db->quoteName('a.created_by'),
						$db->quoteName('a.modified'),
						$db->quoteName('a.modified_by'),
						$db->quoteName('a.language'),
						$db->quoteName('l.title', 'language_title'),
						$db->quoteName('l.image', 'language_image'),
						$db->quoteName('c.title', 'category_title'),
						$db->quoteName('c.alias', 'category_alias'),
						$db->quoteName('c.access', 'category_access'),
						$db->quoteName('c.language', 'category_language'),
						$db->quoteName('u.name', 'author'),
						$db->quoteName('parent.title', 'parent_title'),
						$db->quoteName('parent.id', 'parent_id'),
						$db->quoteName('parent.path', 'parent_route'),
						$db->quoteName('parent.alias', 'parent_alias'),
						$db->quoteName('parent.language', 'parent_language')
					]
				))
					->from($db->quoteName('#__boilerplate_boilerplate', 'a'))
					->join('LEFT', $db->quoteName('#__languages', 'l'), $db->quoteName('l.lang_code') . ' = ' . $db->quoteName('a.language'))
					->join('INNER', $db->quoteName('#__categories', 'c'), $db->quoteName('c.id') . ' = ' . $db->quoteName('a.catid'))
					->join('LEFT', $db->quoteName('#__users', 'u'), $db->quoteName('u.id') . ' = ' . $db->quoteName('a.created_by'))
					->join('LEFT', $db->quoteName('#__categories', 'parent'), $db->quoteName('parent.id') . ' = ' . $db->quoteName('c.parent_id'))
					->where($db->quoteName('a.id') . ' = :id')
					->bind(':id', $pk, ParameterType::INTEGER);

				$query->where($db->quoteName('c.published') . ' > 0 AND ' . $db->quoteName('a.state') . ' = 1');
				$query->order($db->quoteName('a.ordering') . ' ASC');

				$db->setQuery($query);
				$data = $db->loadObject();

				if (empty($data)) {
					throw new \Exception(Text::_('COM_BOILERPLATE_ERROR_BOILERPLATE_NOT_FOUND'), 404);
				}

				$this->_item[$pk] = $data;

			} catch (\Exception $e) {
				if ($e->getCode() == 404) {
					// Need to go through the error handler to allow Redirect to work.
					throw $e;
				}

				$this->setError($e);
				$this->_item[$pk] = false;
			}
		}

		return $this->_item[$pk];
	}
}
