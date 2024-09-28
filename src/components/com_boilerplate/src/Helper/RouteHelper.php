<?php


/**
 * @package     com_boilerplate
 * @version     1.0.0
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     MIT License (MIT) see LICENSE.txt
 * @author      Jörg Schöneburg <info@joerg-schoeneburg.de> - https://joerg-schoeneburg.de
 */

namespace Joomla\Component\Boilerplate\Site\Helper;

use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Categories\CategoryNode;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Boilerplate Component Route Helper.
 *
 * @since  1.5
 */
abstract class RouteHelper
{
    /**
     * Get the boilerplate route.
     *
     * @param   integer  $id        The route of the content item.
     * @param   integer  $catid     The category ID.
     * @param   string   $language  The language code.
     * @param   string   $layout    The layout value.
     *
     * @return  string  The boilerplate route.
     *
     * @since   1.5
     */
    public static function getBoilerplateRoute($id, $catid = 0, $language = null, $layout = null)
    {
        // Create the link
        $link = 'index.php?option=com_boilerplate&view=boilerplate&id=' . $id;

        if ((int) $catid > 1) {
            $link .= '&catid=' . $catid;
        }

        if (!empty($language) && $language !== '*' && Multilanguage::isEnabled()) {
            $link .= '&lang=' . $language;
        }

        if ($layout) {
            $link .= '&layout=' . $layout;
        }

        return $link;
    }

    /**
     * Get the category route.
     *
     * @param   integer  $catid     The category ID.
     * @param   integer  $language  The language code.
     * @param   string   $layout    The layout value.
     *
     * @return  string  The category route.
     *
     * @since   1.5
     */
    public static function getCategoryRoute($catid, $language = 0, $layout = null)
    {
        if ($catid instanceof CategoryNode) {
            $id = $catid->id;
        } else {
            $id = (int) $catid;
        }

        if ($id < 1) {
            return '';
        }

        $link = 'index.php?option=com_boilerplate&view=category&id=' . $id;

        if ($language && $language !== '*' && Multilanguage::isEnabled()) {
            $link .= '&lang=' . $language;
        }

        if ($layout) {
            $link .= '&layout=' . $layout;
        }

        return $link;
    }
}
