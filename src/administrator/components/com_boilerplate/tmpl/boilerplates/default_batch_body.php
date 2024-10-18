<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_boilerplate
 *
 * @copyright   (C) 2024 Jörg Schöneburg
 * @license     MIT License (MIT) see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Multilanguage;

/** @var \Joomla\Component\Boilerplate\Administrator\View\Boilerplates\HtmlView $this */

$state = $this->getState();
$published = (int) $state->get('filter.published');
?>

<div class="p-3">
    <div class="row">
        <?php if (Multilanguage::isEnabled()): ?>
            <div class="form-group col-md-6">
                <div class="controls">
                    <?php echo LayoutHelper::render('joomla.html.batch.language', []); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="row">
        <?php if ($published >= 0): ?>
            <div class="form-group col-md-6">
                <div class="controls">
                    <?php echo LayoutHelper::render('joomla.html.batch.item', ['extension' => 'com_boilerplate']); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<div class="btn-toolbar p-3">
    <joomla-toolbar-button task="boilerplate.batch" class="ms-auto">
        <button type="button" class="btn btn-success"><?php echo Text::_('JGLOBAL_BATCH_PROCESS'); ?></button>
    </joomla-toolbar-button>
</div>