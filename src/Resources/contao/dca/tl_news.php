<?php

/**
 * Contao - News most read bundle
 *
 * Created by MEN AT WORK Werbeagentur GmbH
 *
 * @copyright  MEN AT WORK Werbeagentur GmbH 2018
 *
 * @author     Sven Meierhans <meierhans@men-at-work.de>
 * @author     Stefan Heimes <heimes@men-at-work.de>
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

/**
 * Extend palettes
 */
PaletteManipulator::create()
    ->addLegend('read_count_legend', 'publish_legend',PaletteManipulator::POSITION_AFTER)
    ->addField(
        [
            'read_count',
            'd_read_count_reset',
            'dT_read_count',
            'd0_read_count',
            'd1_read_count',
            'd2_read_count',
            'd3_read_count',
            'd4_read_count',
            'd5_read_count',
            'd6_read_count',
            'd7_read_count',
        ],
        'read_count_legend',
        PaletteManipulator::POSITION_APPEND
    )
    ->applyToPalette('default', 'tl_news');

/**
 * Add DCA field to track read count..
 */

$GLOBALS['TL_DCA']['tl_news']['fields']['read_count'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_news']['read_count'],
    'inputType' => 'text',
    'eval'      => ['rgxp' => 'digit', 'tl_class' => 'w50'],
    'sql'       => "int(11) unsigned NOT NULL DEFAULT '0'",
];

$GLOBALS['TL_DCA']['tl_news']['fields']['d_read_count_reset'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_news']['d_read_count_reset'],
    'inputType' => 'text',
    'eval'      => ['rgxp' => 'digit', 'tl_class' => 'w50'],
    'sql'       => "int(11) unsigned NOT NULL DEFAULT '0'",
];

$GLOBALS['TL_DCA']['tl_news']['fields']['dT_read_count'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_news']['dT_read_count'],
    'inputType' => 'text',
    'eval'      => ['rgxp' => 'digit', 'tl_class' => 'w50'],
    'sql'       => "int(11) unsigned NOT NULL DEFAULT '0'",
];

$basicSettings = [
    'label'     => &$GLOBALS['TL_LANG']['tl_news']['d_read_count'],
    'inputType' => 'text',
    'eval'      => ['rgxp' => 'digit', 'tl_class' => 'w50'],
    'sql'       => "int unsigned NOT NULL DEFAULT '0'",
];

$GLOBALS['TL_DCA']['tl_news']['fields']['d0_read_count'] = $basicSettings;
$GLOBALS['TL_DCA']['tl_news']['fields']['d1_read_count'] = $basicSettings;
$GLOBALS['TL_DCA']['tl_news']['fields']['d2_read_count'] = $basicSettings;
$GLOBALS['TL_DCA']['tl_news']['fields']['d3_read_count'] = $basicSettings;
$GLOBALS['TL_DCA']['tl_news']['fields']['d4_read_count'] = $basicSettings;
$GLOBALS['TL_DCA']['tl_news']['fields']['d5_read_count'] = $basicSettings;
$GLOBALS['TL_DCA']['tl_news']['fields']['d6_read_count'] = $basicSettings;
$GLOBALS['TL_DCA']['tl_news']['fields']['d7_read_count'] = $basicSettings;
