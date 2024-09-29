<?php

/**
 * @package     Joomla.Site
 * @package     com_boilerplate
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Boilerplate\Site\Service;

use Joomla\CMS\Factory;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\Database\ParameterType;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Categories\CategoryInterface;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Component\Router\RouterViewConfiguration;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Routing class of com_content
 *
 * @since  3.3
 */
class Router extends RouterView
{
    /**
     * Flag to remove IDs
     *
     * @var    boolean
     */
    protected bool $noIDs = false;

    /**
     * The category factory
     *
     * @var CategoryFactoryInterface
     *
     * @since   4.0.0
     */
    private CategoryFactoryInterface $categoryFactory;

    /**
     * The category cache
     *
     * @var  array
     *
     * @since  4.0.0
     */
    private array $categoryCache = [];

    /**
     * The db
     *
     * @var DatabaseInterface
     *
     * @since  4.0.0
     */
    private DatabaseInterface $db;

    /**
     * Content Component router constructor
     *
     * @param   SiteApplication           $app              The application object
     * @param   AbstractMenu              $menu             The menu object to work with
     * @param   CategoryFactoryInterface  $categoryFactory  The category object
     * @param   DatabaseInterface         $db               The database object
     */
    public function __construct(SiteApplication $app, AbstractMenu $menu, CategoryFactoryInterface $categoryFactory, DatabaseInterface $db)
    {
        $this->categoryFactory = $categoryFactory;
        $this->db = $db;

        $params = ComponentHelper::getParams('com_boilerplate');
        $this->noIDs = (bool) $params->get('sef_ids');

        $categories = new RouterViewConfiguration('categories');
        $categories->setKey('id');
        $this->registerView($categories);

        $category = new RouterViewConfiguration('category');
        $category->setKey('id')->setParent($categories, 'catid')->setNestable();
        $this->registerView($category);

        $boilerplate = new RouterViewConfiguration('boilerplate');
        $boilerplate->setKey('id')->setParent($category, 'catid');
        $this->registerView($boilerplate);

        $boilerplates = new RouterViewConfiguration('boilerplates');
        $boilerplates->setKey('id');
        $this->registerView($boilerplates);

        parent::__construct($app, $menu);

        $this->attachRule(new MenuRules($this));
        $this->attachRule(new StandardRules($this));
        $this->attachRule(new NomenuRules($this));
    }

    /**
     * Method to get the segment(s) for a category
     *
     * @param   string  $id     ID of the category to retrieve the segments for
     * @param   array   $query  The request that is built right now
     *
     * @return  array|string  The segments of this item
     */
    public function getCategorySegment($id, $query): array|string
    {
        $category = $this->getCategories(['access' => true])->get($id);

        if ($category) {
            $path = array_reverse($category->getPath(), true);
            $path[0] = '1:root';

            if ($this->noIDs) {
                foreach ($path as &$segment) {
                    list($id, $segment) = explode(':', $segment, 2);
                }
            }
            return $path;
        }

        return [];
    }

    /**
     * Method to get the segment(s) for a category
     *
     * @param   string  $id     ID of the category to retrieve the segments for
     * @param   array   $query  The request that is built right now
     *
     * @return  array|string  The segments of this item
     */
    public function getCategoriesSegment($id, $query): array|string
    {
        return $this->getCategorySegment($id, $query);
    }

    /**
     * Method to get the segment(s) for an article
     *
     * @param   string  $id     ID of the article to retrieve the segments for
     * @param   array   $query  The request that is built right now
     *
     * @return  array|string  The segments of this item
     */
    public function getBoilerplateSegment($id, $query)
    {
        if (!strpos($id, ':')) {
            $id = (int) $id;
            $alias = $this->getAlias($id);
            if ($alias) {
                $id .= ':' . $alias;
            }
        }

        if ($this->noIDs) {
            list($void, $segment) = explode(':', $id, 2);
            return [$void => $segment];
        }

        return [(int) $id => $id];
    }

    /**
     * Method to get the segment(s) for a form
     *
     * @param   string  $id     ID of the article form to retrieve the segments for
     * @param   array   $query  The request that is built right now
     *
     * @return  array|string  The segments of this item
     *
     * @since   3.7.3
     */
    public function getFormSegment($id, $query): array|string
    {
        return $this->getBoilerplateSegment($id, $query);
    }

    /**
     * Method to get the id for a category
     *
     * @param   string  $segment  Segment to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  mixed   The id of this item or false
     */
    public function getCategoryId($segment, $query): bool|int
    {
        if (isset($query['id'])) {
            $category = $this->getCategories(['access' => false])->get($query['id']);

            if ($category) {
                foreach ($category->getChildren() as $child) {
                    if ($this->noIDs) {
                        if ($child->alias == $segment) {
                            return $child->id;
                        }
                    } else {
                        if ($child->id == (int) $segment) {
                            return $child->id;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Method to get the segment(s) for a category
     *
     * @param   string  $segment  Segment to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  mixed   The id of this item or false
     */
    public function getCategoriesId($segment, $query)
    {
        return $this->getCategoryId($segment, $query);
    }

    /**
     * Method to get the segment(s) for an article
     *
     * @param   string  $segment  Segment of the article to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  mixed   The id of this item or false
     */
    public function getBoilerplateId($segment, $query)
    {
        if ($this->noIDs) {
            $dbquery = $this->db->getQuery(true);
            $dbquery->select($this->db->quoteName('id'))
                ->from($this->db->quoteName('#__boilerplate_boilerplate'))
                ->where(
                    [
                        $this->db->quoteName('alias') . ' = :alias',
                        $this->db->quoteName('catid') . ' = :catid',
                    ]
                )
                ->bind(':alias', $segment)
                ->bind(':catid', $query['id'], ParameterType::INTEGER);
            $this->db->setQuery($dbquery);


            return (int) $this->db->loadResult();
        }

        return (int) $segment;
    }

    /**
     * Method to get categories from cache
     *
     * @param   array  $options   The options for retrieving categories
     *
     * @return  CategoryInterface  The object containing categories
     *
     * @since   4.0.0
     */
    private function getCategories(array $options = []): CategoryInterface
    {
        $key = serialize($options);

        if (!isset($this->categoryCache[$key])) {
            $this->categoryCache[$key] = $this->categoryFactory->createCategory($options);
        }

        return $this->categoryCache[$key];
    }

    private function getAlias(int $id): string
    {
        $query = $this->db->getQuery(true)
            ->select($this->db->quoteName('alias'))
            ->from($this->db->quoteName('#__boilerplate_boilerplate'))
            ->where($this->db->quoteName('id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);

        $this->db->setQuery($query);

        try {
            return $this->db->loadResult() ?: '';
        } catch (\RuntimeException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode());
        }
    }
}
