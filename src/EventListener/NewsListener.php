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

namespace MenAtWork\NewsMostReadBundle\EventListener;

use Contao\ModuleNews;
use Contao\NewsModel;
use Doctrine\DBAL\Connection;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use MenAtWork\NewsMostReadBundle\Services\NewsReadCountService;

/**
 * Class NewsListener
 *
 * @package MenAtWork\NewsMostReadBundle\EventListener
 */
class NewsListener
{
    /**
     * @var NewsReadCountService
     */
    private $newsReadCountService;

    /**
     * @var Connection
     */
    private $databaseConnection;

    /**
     * NewsListener constructor.
     *
     * @param NewsReadCountService $newsReadCountService
     *
     * @param Connection           $databaseConnection
     */
    public function __construct(
        NewsReadCountService $newsReadCountService,
        Connection $databaseConnection
    ) {
        $this->newsReadCountService = $newsReadCountService;
        $this->databaseConnection   = $databaseConnection;
    }

    /**
     * Reorders the fetched news items collection by news_read_count descanding.
     *
     * @param array      $newsArchives The news archive.
     * @param boolean    $blnFeatured  If true, return only featured news, if false, return only unfeatured news.
     * @param integer    $intLimit     An optional limit.
     * @param integer    $intOffset    An optional offset.
     * @param ModuleNews $objModule    The news module object.
     *
     * @return \Contao\Model\Collection|NewsModel[]|NewsModel|null A collection of models or null if there are no news
     */
    public function onNewsListFetchItems($newsArchives, $blnFeatured, $limit, $offset, $objModule)
    {
        if ($objModule->type !== 'newslist' || empty($objModule->news_displayMostRead_mode)) {
            return false;
        }

        if ($objModule->news_displayMostRead_mode == 1) {
            $news = \Contao\NewsModel::findPublishedByPids($newsArchives, $blnFeatured, $limit, $offset, [
                'order' => 'read_count desc, tl_news.date desc',
            ]);
        } elseif ($objModule->news_displayMostRead_mode == 2) {
            $news = \Contao\NewsModel::findPublishedByPids($newsArchives, $blnFeatured, $limit, $offset, [
                'order' => 'dT_read_count desc, tl_news.date desc',
            ]);
        }

        return $news;
    }

    /**
     * Hooks the parseArticles to increment the news read count.
     *
     * @param $objTemplate
     * @param $row
     * @param $objModuleNews
     */
    public function onParseArticles($objTemplate, $row, $objModuleNews)
    {
        // Check if the current module is a reader
        if ($objModuleNews->type !== 'newsreader') {
            return;
        }

        // Skip, if this is a request from a known crawler
        $CrawlerDetect = new CrawlerDetect();
        if ($CrawlerDetect->isCrawler()) {
            return;
        }

        // Skip if the news item has been already read in this session
        if ($this->newsReadCountService->hasItem($row['id'])) {
            return;
        }

        // Get the numeric value of the current day.
        $currentDayId       = \date('w');
        $countDayColumnName = \sprintf('d%s_read_count', $currentDayId);

        // increment news counter
        $newsModel             = NewsModel::findById($row['id']);
        $newsModel->read_count = ++$newsModel->read_count;

        // Reset the value.
        if ($newsModel->d_read_count_reset != $currentDayId) {
            $newsModel->d_read_count_reset  = $currentDayId;
            $newsModel->$countDayColumnName = 0;
        }

        // Set the new 7 day value.
        $newsModel->$countDayColumnName = ++$newsModel->$countDayColumnName;

        // Add total count.
        $total = 0;
        for ($i = 0; $i < 7; $i++) {
            $column = \sprintf('d%s_read_count', $i);
            $total  += (int)$newsModel->$column;
        }
        $newsModel->dT_read_count = $total;

        $newsModel->save();

        $this->updateTotalDailyCount($newsModel->id);

        // store news id in session bag
        $this->newsReadCountService->add($row['id']);
    }

    /**
     * Reset the counter of all news.
     *
     * Search for all news, where the 'd_read_count_reset' is not the
     * same like the current id of the date (php: date('w')).
     *
     * For this entires, set the last reset to the current day and
     * set the value of this date to 0.
     *
     * @return void
     */
    public function onHourly(): void
    {
        $currentDayId       = \date('w');
        $countDayColumnName = \sprintf('d%s_read_count', $currentDayId);

        $this->databaseConnection
            ->createQueryBuilder()
            ->update('tl_news')
            ->set('d_read_count_reset', '?date')
            ->set($countDayColumnName, 0)
            ->where('d_read_count_reset != ?date')
            ->setParameter('?date', $currentDayId)
            ->execute();

        $this->updateTotalDailyCount();
    }

    /**
     * Update the total count.
     */
    public function updateTotalDailyCount($id = null)
    {
        $queryBuilder = $this
            ->databaseConnection
            ->createQueryBuilder()
            ->update('tl_news')
            ->set(
                'dT_read_count',
                '(
                    d0_read_count 
                    + d1_read_count 
                    + d2_read_count 
                    + d3_read_count 
                    + d4_read_count 
                    + d5_read_count 
                    + d6_read_count 
                    + d7_read_count
                )'
            );

        if ($id !== null) {
            $queryBuilder
                ->where('id', '?id')
                ->setParameter('id', $id);
        }

        $queryBuilder->execute();
    }
}
