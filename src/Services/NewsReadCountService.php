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

namespace MenAtWork\NewsMostReadBundle\Services;


use Contao\Database;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class NewsReadCountService
 *
 * @package MenAtWork\NewsMostReadBundle\Services
 */
class NewsReadCountService
{
    /**
     * @var Session
     */
    private $session;

    /**
     * Session name.
     */
    const NEWS_COUNT_SESSION_BAG = 'news_count_session_bag';

    /**
     * NewsReadCountService constructor.
     *
     * @param Session $session
     */
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
            $this->session->set(self::NEWS_COUNT_SESSION_BAG, [$newsId]);

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
     *
     * @return bool True if the news model id already exists.
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

        $sql = \sprintf(
            'UPDATE tl_news SET d_read_count_reset = ?, %s = 0 WHERE d_read_count_reset != ?',
            $countDayColumnName
        );

        Database::getInstance()
            ->prepare($sql)
            ->execute([$currentDayId, $currentDayId]);
    }
}
