<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_boilerplate
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Boilerplate\Site\Service;

use Joomla\CMS\Categories\Categories;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Boilerplate Component Category Tree
 *
 * @since  1.0.0
 */
class Category extends Categories
{
    /**
     * Constructor
     *
     * @param   array  $options  Array of options
     *
     * @since   1.0.0
     */
    public function __construct($options = [])
    {
        $options['table'] = '#__boilerplate_boilerplate';
        $options['extension'] = 'com_boilerplate';

        parent::__construct($options);
    }
}
