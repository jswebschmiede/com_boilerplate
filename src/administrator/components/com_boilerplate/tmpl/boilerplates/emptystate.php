<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_boilerplate
 *
 * @copyright   (C) 2021 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;

/** @var \Joomla\Component\Boilerplate\Administrator\View\Boilerplates\HtmlView $this */

$displayData = [
    'textPrefix' => 'COM_BOILERPLATE',
    'formURL' => 'index.php?option=com_boilerplate&view=boilerplates',
    'icon' => 'icon-bookmark boilerplates',
];

$user = $this->getCurrentUser();

if ($user->authorise('core.create', 'com_boilerplate') || count($user->getAuthorisedCategories('com_boilerplate', 'core.create')) > 0) {
    $displayData['createURL'] = 'index.php?option=com_boilerplate&task=boilerplate.add';
}

echo LayoutHelper::render('joomla.content.emptystate', $displayData);
