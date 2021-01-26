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
\Contao\CoreBundle\DataContainer\PaletteManipulator::create()
    ->addField(
        ['news_displayMostRead_mode'],
        'news_order',
        \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_BEFORE
    )
    ->applyToPalette('newslist', 'tl_module');

/**
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['news_displayMostRead_mode'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_displayMostRead_mode'],
    'exclude'   => true,
    'inputType' => 'select',
    'options'   => [1, 2],
    'reference' => $GLOBALS['TL_LANG']['tl_module']['news_displayMostRead_options'],
    'eval'      => [
        'includeBlankOption' => true,
        'tl_class'           => 'w50'
    ],
    'sql'       => "char(1) NOT NULL default ''"
);
