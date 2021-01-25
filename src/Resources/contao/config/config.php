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
 * Register news list hooks
 */
$GLOBALS['TL_HOOKS']['parseArticles'][]      = [ 'menatwork_news_most_read.listener.news_module', 'onParseArticles' ];
$GLOBALS['TL_HOOKS']['newsListFetchItems'][] = [ 'menatwork_news_most_read.listener.news_module', 'onNewsListFetchItems' ];

/**
 * Crons
 */
$GLOBALS['TL_CRON']['hourly'][] = ['menatwork_news_most_read.services.news_read_count', 'onHourly'];
