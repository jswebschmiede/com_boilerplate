<?php

/**
 * @package     com_boilerplate
 * @version     1.0.0
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     MIT License (MIT) see LICENSE.txt
 * @author      Jörg Schöneburg <info@joerg-schoeneburg.de> - https://joerg-schoeneburg.de
 */

namespace Joomla\Component\Boilerplate\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\String\StringHelper;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\Versioning\VersionableModelTrait;
use Joomla\Component\Categories\Administrator\Helper\CategoriesHelper;

defined('_JEXEC') or die;

/**
 * Methods supporting a single boilerplate record.
 *
 * @since  1.0.0
 */
class BoilerplateModel extends AdminModel
{
	use VersionableModelTrait;

	/**
	 * The type alias for this content type.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $typeAlias = 'com_boilerplate.boilerplate';

	/**
	 * Batch copy/move command. If set to false, the batch copy/move command is not supported
	 *
	 * @var  string
	 */
	protected $batch_copymove = 'category_id';

	/**
	 * Allowed batch commands
	 *
	 * @var  array
	 */
	protected $batch_commands = [
		'client_id' => 'batchClient',
		'language_id' => 'batchLanguage',
	];

	/**
	 * Method to get the row form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  Form|boolean  A Form object on success, false on failure
	 *
	 * @since   1.0.0
	 */
	public function getForm($data = [], $loadData = true): bool|Form
	{
		// Get the form.
		$form = $this->loadForm(
			'com_boilerplate.boilerplate',
			'boilerplate',
			array(
				'control' => 'jform',
				'load_data' => $loadData
			)
		);

		if (empty($form)) {
			return false;
		}

		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data)) {
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('state', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('state', 'filter', 'unset');
		}

		// Don't allow to change the created_by user if not allowed to access com_users.
		if (!$this->getCurrentUser()->authorise('core.manage', 'com_users')) {
			$form->setFieldAttribute('created_by', 'filter', 'unset');
		}

		if ($this->canCreateCategory()) {
			$form->setFieldAttribute('catid', 'allowAdd', 'true');

			// Add a prefix for categories created on the fly.
			$form->setFieldAttribute('catid', 'customPrefix', '#new#');
		}

		return $form;
	}

	/**
	 * Summary of getTable
	 * @param mixed $name
	 * @param mixed $prefix
	 * @param mixed $options
	 * @throws \Exception
	 * @return bool|Table
	 */
	public function getTable($name = '', $prefix = '', $options = []): bool|Table
	{
		$name = 'boilerplate';
		$prefix = 'Table';

		if ($table = $this->_createTable($name, $prefix, $options)) {
			return $table;
		}

		throw new \Exception(Text::sprintf('JLIB_APPLICATION_ERROR_TABLE_NAME_NOT_SUPPORTED', $name), 0);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.0.0
	 */
	protected function loadFormData(): mixed
	{
		$app = Factory::getApplication();

		// Check the session for previously entered form data.
		$data = $app->getUserState(
			'com_boilerplate.edit.boilerplate.data',
			[]
		);

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   Table  $table  A Table object.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function prepareTable($table): void
	{
		$date = Factory::getDate();
		$user = $this->getCurrentUser();

		if (empty($table->id)) {
			// Set the values
			$table->created = $date->toSql();
			$table->created_by = $user->id;

			// Set ordering to the last item if not set
			if (empty($table->ordering)) {
				$db = $this->getDatabase();
				$query = $db->getQuery(true)
					->select('MAX(' . $db->quoteName('ordering') . ')')
					->from($db->quoteName('#__boilerplate_boilerplate'));

				$db->setQuery($query);
				$max = $db->loadResult();

				$table->ordering = $max + 1;
			}
		} else {
			// Set the values
			$table->modified = $date->toSql();
			$table->modified_by = $user->id;
		}

		// Increment the content version number.
		$table->version++;
	}

	protected function generateNewTitleAndAlias(string $alias, string $title): array
	{
		// Alter the title & alias
		$table = $this->getTable();
		$aliasField = $table->getColumnAlias('alias');
		$titleField = $table->getColumnAlias('name');

		while ($table->load([$aliasField => $alias])) {
			if ($title === $table->$titleField) {
				$title = StringHelper::increment($title, 'dash');
			}

			$alias = StringHelper::increment($alias, 'dash');
		}

		return [$title, $alias];
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 * @since   1.0.0
	 */
	public function save($data): bool
	{
		$input = Factory::getApplication()->getInput();

		// Alter the name for save as copy
		if ($input->get('task') == 'save2copy') {
			/** @var \Joomla\Component\Boilerplate\Administrator\Table\BoilerplateTable $origTable */
			$origTable = clone $this->getTable();
			$origTable->load($input->getInt('id'));

			if ($data['name'] == $origTable->name) {
				list($name, $alias) = $this->generateNewTitleAndAlias($data['alias'], $data['name']);
				$data['name'] = $name;
				$data['alias'] = $alias;
			} else {
				if ($data['alias'] == $origTable->alias) {
					$data['alias'] = '';
				}
			}

			$data['state'] = 0;
		}

		// Create new category, if needed.
		$createCategory = true;

		// If category ID is provided, check if it's valid.
		if (is_numeric($data['catid']) && $data['catid']) {
			$createCategory = !CategoriesHelper::validateCategoryId($data['catid'], 'com_boilerplate');
		}

		// Save New Category
		if ($createCategory && $this->canCreateCategory()) {
			$category = [
				// Remove #new# prefix, if exists.
				'title' => strpos($data['catid'], '#new#') === 0 ? substr($data['catid'], 5) : $data['catid'],
				'parent_id' => 1,
				'extension' => 'com_boilerplate',
				'language' => $data['language'],
				'published' => 1,
			];

			/** @var \Joomla\Component\Categories\Administrator\Model\CategoryModel $categoryModel */
			$categoryModel = Factory::getApplication()->bootComponent('com_categories')
				->getMVCFactory()->createModel('Category', 'Administrator', ['ignore_request' => true]);

			// Create new category.
			if (!$categoryModel->save($category)) {
				$this->setError($categoryModel->getError());

				return false;
			}

			// Get the new category ID.
			$data['catid'] = $categoryModel->getState('category.id');
		}

		return parent::save($data);
	}

	/**
	 * Summary of getFormFactory
	 * @return FormFactoryInterface
	 */
	public function getFormFactory(): FormFactoryInterface
	{
		return parent::getFormFactory();
	}

	/**
	 * Is the user allowed to create an on the fly category?
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 */
	private function canCreateCategory(): bool
	{
		return $this->getCurrentUser()->authorise('core.create', 'com_boilerplate');
	}
}