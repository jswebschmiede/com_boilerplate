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
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;
use Joomla\Database\ParameterType;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Language\Multilanguage;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Categories\CategoryNode;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\Component\Content\Administrator\Extension\ContentComponent;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * This models supports retrieving lists of article categories.
 *
 * @since  1.6
 */
class CategoriesModel extends ListModel
{
    /**
     * Model context string.
     *
     * @var     string
     */
    public $_context = 'com_boilerplate.categories';

    /**
     * The category context (allows other extensions to derived from this model).
     *
     * @var     string
     */
    protected $_extension = 'com_boilerplate';

    /**
     * Parent category of the current one
     *
     * @var    CategoryNode|null
     */
    private $_parent = null;

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   string  $ordering   The field to order on.
     * @param   string  $direction  The direction to order on.
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function populateState($ordering = null, $direction = null)
    {
        $app = Factory::getApplication();
        $this->setState('filter.extension', $this->_extension);

        // Get the parent id if defined.
        $parentId = $app->getInput()->getInt('id');
        $this->setState('filter.parentId', $parentId);

        $params = $app->getParams();
        $this->setState('params', $params);

        $this->setState('filter.published', 1);
        $this->setState('filter.access', true);
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
    protected function getStoreId($id = ''): string
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.extension');
        $id .= ':' . $this->getState('filter.published');
        $id .= ':' . $this->getState('filter.access');
        $id .= ':' . $this->getState('filter.parentId');

        return parent::getStoreId($id);
    }

    /**
     * Redefine the function and add some properties to make the styling easier
     *
     * @param   bool  $recursive  True if you want to return children recursively.
     *
     * @return  mixed  An array of data items on success, false on failure.
     *
     * @since   1.6
     */
    public function getItems(bool $recursive = false): mixed
    {
        $store = $this->getStoreId();

        $app = Factory::getApplication();
        $menu = $app->getMenu();
        $active = $menu->getActive();

        if ($active) {
            $params = $active->getParams();
        } else {
            $params = new Registry();
        }

        $options = [];
        $categories = Categories::getInstance('Boilerplate', $options);
        $this->_parent = $categories->get($this->getState('filter.parentId', 'root'));

        if (\is_object($this->_parent)) {
            return $this->_parent->getChildren($recursive);
        }

        return false;
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
     * Get the parent.
     *
     * @return  object|false  An array of data items on success, false on failure.
     *
     * @since   1.6
     */
    public function getParent(): object|bool
    {
        if (!\is_object($this->_parent)) {
            $this->getItems();
        }

        return $this->_parent;
    }
}