<?php
/**
 * Contao - News most read bundle
 *
 * Created by MEN AT WORK Werbeagentur GmbH
 *
 * @copyright  MEN AT WORK Werbeagentur GmbH 2018
 * @author     Sven Meierhans <meierhans@men-at-work.de>
 */

/**
 * Extend palettes
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

PaletteManipulator::create()
    ->addField(
        [ 'news_displayMostRead' ],
        'config_legend',
        PaletteManipulator::POSITION_PREPEND
    )
    ->applyToPalette('newslist', 'tl_module');

/**
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['news_displayMostRead'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['news_displayMostRead'],
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'sql'                     => "char(1) NOT NULL default ''"
);
