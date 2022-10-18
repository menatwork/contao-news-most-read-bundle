<?php
/**
 * Contao - News most read bundle
 *
 * Created by MEN AT WORK Werbeagentur GmbH
 *
 * @copyright  MEN AT WORK Werbeagentur GmbH 2018
 * @author     Sven Meierhans <meierhans@men-at-work.de>
 */

namespace MenAtWork\NewsMostReadBundle\EventListener;


use Contao\Model\Collection;
use Contao\ModuleNews;
use Contao\NewsModel;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use MenAtWork\NewsMostReadBundle\Services\NewsReadCountService;

class NewsListener
{

    private NewsReadCountService $newsReadCountService;

    public function __construct(NewsReadCountService $newsReadCountService)
    {
        $this->newsReadCountService = $newsReadCountService;
    }

    /**
     * Reorders the fetched news items collection by news_read_count descanding.
     *
     * @param array      $newsArchives The news archive.
     * @param boolean    $blnFeatured  If true, return only featured news, if false, return only unfeatured news.
     * @param integer    $limit     An optional limit.
     * @param integer    $offset    An optional offset.
     * @param ModuleNews $objModule    The news module object.
     *
     * @return Collection|NewsModel[]|NewsModel|null A collection of models or null if there are no news
     */
    public function onNewsListFetchItems($newsArchives, $blnFeatured, $limit, $offset, $objModule)
    {
        if ($objModule->type !== 'newslist' || !$objModule->news_displayMostRead) {
            return null;
        }

        return NewsModel::findPublishedByPids($newsArchives, $blnFeatured, $limit, $offset, [
            'order' => 'read_count desc, tl_news.date desc',
        ]);
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

        // increment news counter
        $newsModel             = NewsModel::findById($row['id']);

        if (null === $newsModel) {
            return;
        }

        $newsModel->read_count = ++$newsModel->read_count;
        $newsModel->save();

        // store news id in session bag
        $this->newsReadCountService->add($row['id']);
    }
}
