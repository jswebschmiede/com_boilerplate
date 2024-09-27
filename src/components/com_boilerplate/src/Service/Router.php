<?php

/**
 * @package     Joomla.Site
 * @package     com_boilerplate
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Boilerplate\Site\Service;

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
    protected $noIDs = false;

    /**
     * The category factory
     *
     * @var CategoryFactoryInterface
     *
     * @since  4.0.0
     */
    private $categoryFactory;

    /**
     * The category cache
     *
     * @var  array
     *
     * @since  4.0.0
     */
    private $categoryCache = [];

    /**
     * The db
     *
     * @var DatabaseInterface
     *
     * @since  4.0.0
     */
    private $db;

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

        $boilerplates = new RouterViewConfiguration('boilerplates');
        $this->registerView($boilerplates);

        $boilerplate = new RouterViewConfiguration('boilerplate');
        $boilerplate->setKey('id')->setNestable();
        $this->registerView($boilerplate);

        parent::__construct($app, $menu);

        $this->attachRule(new MenuRules($this));
        $this->attachRule(new StandardRules($this));
        $this->attachRule(new NomenuRules($this));
    }

    /**
     * Method to build the segment(s) for a boilerplate
     *
     * @param   array  $query  The request that is built right now
     *
     * @return  array  The segments of this item
     */
    public function build(&$query): array
    {
        $segments = [];

        // Sicherstellen, dass $query ein Array ist
        if (!is_array($query)) {
            return $segments;
        }

        // Behandlung der 'boilerplates' Ansicht
        if (isset($query['view']) && $query['view'] === 'boilerplates') {
            // Entfernen Sie 'view' aus der Query, da es die Standardansicht ist
            unset($query['view']);
            return $segments;
        }

        // Behandlung der 'boilerplate' Ansicht
        if (isset($query['view']) && $query['view'] === 'boilerplate') {
            unset($query['view']);

            if (isset($query['id'])) {
                // Abrufen des Alias aus der Datenbank
                $dbQuery = $this->db->getQuery(true)
                    ->select($this->db->quoteName('alias'))
                    ->from($this->db->quoteName('#__boilerplate_boilerplate'))
                    ->where($this->db->quoteName('id') . ' = :id')
                    ->bind(':id', $query['id'], ParameterType::INTEGER);
                $this->db->setQuery($dbQuery);
                $alias = $this->db->loadResult();

                if ($alias) {
                    $segments[] = $alias;
                    unset($query['id']);
                }
            }
        }

        return $segments;
    }

    /**
     * Method to parse the segment(s) for a boilerplate
     *
     * @param   array  $segments  The segments of this item
     *
     * @return  array  The variables to be used in the request
     */
    public function parse(&$segments): array
    {
        $vars = [];

        // Wenn es Segmente gibt, ist es wahrscheinlich der Alias eines Boilerplate
        if (count($segments) > 0) {
            $vars['view'] = 'boilerplate';

            // Abrufen der ID aus der Datenbank mit dem Alias
            $dbQuery = $this->db->getQuery(true)
                ->select($this->db->quoteName('id'))
                ->from($this->db->quoteName('#__boilerplate_boilerplate'))
                ->where($this->db->quoteName('alias') . ' = :alias')
                ->bind(':alias', $segments[0]);
            $this->db->setQuery($dbQuery);
            $id = $this->db->loadResult();

            if ($id) {
                $vars['id'] = $id;
            } else {
                // Wenn keine passende ID gefunden wurde, setzen Sie auf die Standardansicht
                $vars['view'] = 'boilerplates';
            }

            // Entfernen Sie das verarbeitete Segment
            array_shift($segments);
        } else {
            // Wenn es keine Segmente gibt, nehmen Sie an, es ist die Boilerplates-Ansicht
            $vars['view'] = 'boilerplates';
        }

        return $vars;
    }

    /**
     * Get the path segments for a given query.
     *
     * This method is crucial for the MenuRules to function correctly.
     * It defines how the router should break down a query into path segments.
     *
     * @param   array  $query  The query parameters
     *
     * @return  array  An array of path segments
     */
    public function getPath($query)
    {
        $segments = [];

        if (isset($query['view'])) {
            $segments[] = $query['view'];
        }

        if (isset($query['id'])) {
            if ($query['view'] === 'boilerplate') {
                // Abrufen des Alias aus der Datenbank
                $dbQuery = $this->db->getQuery(true)
                    ->select($this->db->quoteName('alias'))
                    ->from($this->db->quoteName('#__boilerplate_boilerplate'))
                    ->where($this->db->quoteName('id') . ' = :id')
                    ->bind(':id', $query['id'], ParameterType::INTEGER);
                $this->db->setQuery($dbQuery);
                $alias = $this->db->loadResult();

                if ($alias) {
                    $segments[] = $alias;
                } elseif (!$this->noIDs) {
                    $segments[] = $query['id'];
                }
            } elseif (!$this->noIDs) {
                $segments[] = $query['id'];
            }
        }

        return $segments;
    }
}
