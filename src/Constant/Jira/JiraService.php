<?php namespace Constant\Jira;

use Constant\Service\BaseService;
use Psr\Log\LoggerInterface;

/**
 * Class JiraService
 *
 * @author  Joe Constant <joe@joeconstant.com>
 * @link    http://joeconstant.com/
 * @license MIT
 */
class JiraService extends BaseService
{
    /**
     * @var array
     */
    protected $jira;

    protected $site;

    /**
     * Initialize class
     * @param LoggerInterface $log
     * @param $username
     * @param $password
     * @param array $options
     */
    public function __construct(LoggerInterface $log, $username, $password, $options = [])
    {
        $this->output = $log;
        $this->site = $options['site'];
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Create worklog entry in Jira
     *
     * @param array $site
     * @param string $date
     * @param string $ticket
     * @param mixed $timeSpent
     * @param string $comment
     * @throws Exception
     * @return mixed
     */
    public function setWorklog($date, $ticket, $timeSpent, $comment = '')
    {
        $this->uri = "{$this->site}/rest/api/2/issue/{$ticket}/worklog";

        if (is_numeric($timeSpent)) {
            $timeSpent .= 'h';
        }

        $startedDate = $this->_formatTimestamp($date);
        $data = [
            'timeSpent' => $timeSpent,
            'started' => $startedDate,
            'comment' => $comment
        ];

        $this->output->debug(print_r($data, true));
        $data = json_encode($data);

        $response = $this->processRequest($data);
        $this->_handleError($data, $response);
        return $response;
    }

    /**
     * Formats timestamp in format 0000-00-00T00:00:00.000000-000
     *
     * @param $date
     *
     * @return string
     */
    protected function _formatTimestamp($date)
    {
        $startedDate = date('Y-m-d|H:i:s.000O', strtotime($date));
        return str_replace('|', 'T', $startedDate);
    }

}
