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


use Contao\NewsModel;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use MenAtWork\NewsMostReadBundle\Services\NewsReadCountService;

class NewsListener
{

    private $newsReadCountService;

    public function __construct(NewsReadCountService $newsReadCountService)
    {
        $this->newsReadCountService = $newsReadCountService;
    }


    public function onNewsListCountItems($newsArchives, $blnFeatured, $objNewsList)
    {
//        dump($newsArchives);
//        dump($blnFeatured);
//        dump($objNewsList);
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
        $newsModel->read_count = ++$newsModel->read_count;
        $newsModel->save();

        // store news id in session bag
        $this->newsReadCountService->add($row['id']);
    }
}