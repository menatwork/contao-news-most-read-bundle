<?php
/**
 * Contao - News most read bundle
 *
 * Created by MEN AT WORK Werbeagentur GmbH
 *
 * @copyright  MEN AT WORK Werbeagentur GmbH 2018
 * @author     Sven Meierhans <meierhans@men-at-work.de>
 */

namespace MenAtWork\NewsMostReadBundle\Services;


use Symfony\Component\HttpFoundation\Session\Session;

class NewsReadCountService
{

    private $session;

    const NEWS_COUNT_SESSION_BAG = 'news_count_session_bag';

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Adds a news id to the session bag which stores all news id's read in this current session.
     *
     * @param int $item The news model id.
     *
     * @return bool Returns true, if the entry was added successfully.
     */
    public function add($newsId)
    {
        $newsRead = $this->session->get(self::NEWS_COUNT_SESSION_BAG);

        if ($newsRead === null) {
            $this->session->set(self::NEWS_COUNT_SESSION_BAG, [ $newsId ]);

            return true;
        }

        if (in_array($newsId, $newsRead)) {
            return false;
        }

        $newsRead[] = $newsId;
        $this->session->set(self::NEWS_COUNT_SESSION_BAG, $newsRead);

        return true;
    }

    /**
     * Checks if a given news id exists in the current session bag.
     *
     * @param int $newsId The news model id.
     */
    public function hasItem($newsId)
    {
        $newsRead = $this->session->get(self::NEWS_COUNT_SESSION_BAG);

        if ($newsRead === null) {
            return false;
        }

        if (in_array($newsId, $newsRead)) {
            return true;
        }

        return false;
    }
}