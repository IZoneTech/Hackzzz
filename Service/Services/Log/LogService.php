<?php
/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Service\Services\Log;

use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * Log
 *
 * @package     Niambie
 * @subpackage  Service
 * @since       1.0
 */
Class LogService
{
    /**
     * Valid Priorities
     *
     * @var    object
     * @since  1.0
     */
    protected $priorities;

    /**
     * Options
     *
     * @var    array
     * @since  1.0
     */
    protected $options;

    /**
     * Valid Loggers
     *
     * @var    object
     * @since  1.0
     */
    protected $loggers;

    /**
     * Class constructor
     *
     * @return boolean
     * @since   1.0
     */
    public function initialise()
    {
        /** Valid Priorities */
        $this->priorities = array();

        $this->priorities[] = LOG_TYPE_EMERGENCY;
        $this->priorities[] = LOG_TYPE_ALERT;
        $this->priorities[] = LOG_TYPE_CRITICAL;
        $this->priorities[] = LOG_TYPE_ERROR;
        $this->priorities[] = LOG_TYPE_WARNING;
        $this->priorities[] = LOG_TYPE_NOTICE;
        $this->priorities[] = LOG_TYPE_INFO;
        $this->priorities[] = LOG_TYPE_PROFILER;
        $this->priorities[] = LOG_TYPE_ALL;

        /** Valid Loggers */
        $this->loggers = array();

        /** Provided with JPlatform */
        $this->loggers[] = LOG_FORMATTEDTEXT_LOGGER;
        $this->loggers[] = LOG_ECHO_LOGGER;
        $this->loggers[] = LOG_DATABASE_LOGGER;

        /** Custom Molajo loggers */
        $this->loggers[] = LOG_MESSAGES_LOGGER;
        $this->loggers[] = LOG_EMAIL_LOGGER;
        $this->loggers[] = LOG_CONSOLE_LOGGER;

        if (Services::Registry()->get(PROFILER_LITERAL, 'CurrentPhase') == INITIALISE) {
            $response = Services::Profiler()->setProfilerLogger();
            if ($response === false) {
                Services::Profiler()->setConfigurationComplete();

                return $this;
            }
            $this->setLog($response['options'], $response['priority'], $response['types']);
            Services::Profiler()->setConfigurationComplete();
        }

        return $this;
    }

    /**
     * Initiate a logging activity and define logging options
     *
     * @param array   $options  Configuration array
     * @param integer $priority Valid priority for log
     * @param array   $types    Valid types for log
     *
     * $options array
     *
     * 0. logger is a required option
     *
     * $options['logger'] valid values include: console, echo (default), database, formattedtext, messages
     *
     * 1. Echo
     *
     * $options['line_separator'] <br /> or /n (default)
     *
     * 2. Text
     *
     * $options['text_file'] ex. error.php (default)
     * $options['text_file_path'] ex. /users/amystephen/sites/molajo/source/site/1/logs (default SITES_LOGS_FOLDER)
     * $options['text_file_no_php'] false - adds die('Forbidden') to top of file (true prevents the precaution)
     * $options['text_entry_format'] - can be used to specify a custom log format
     *
     * 3. Database
     *
     * $options['dbo'] - Services::Database();
     * $options['db_table'] - #__log
     *
     * +++ Molajo custom loggers
     *
     * 4. Email
     *
     * $this->options['sender'] = array(
     *     Services::Registry()->get(CONFIGURATION_LITERAL, 'mailer_mail_from'),
     *     Services::Registry()->get(CONFIGURATION_LITERAL, 'mailer_mail_from_name')
     * };
     * $this->options['recipient'] = Services::Registry()->get(CONFIGURATION_LITERAL, 'mailer_mail_from_email_address');
     * $this->options['subject'] = Services::Language()->translate('LOG_ALERT_EMAIL_SUBJECT'));
     * $this->options['mailer'] = Services::Mail();
     *
     * 5. ChromePHP
     *
     * No addition $option[] values. However, this option requires using Google Chrome and installing this
     * Google Chrome extension: https://chrome.google.com/webstore/detail/noaneddfkdjfnfdakjjmocngnfkfehhd
     * and https://github.com/ccampbell/chromephp
     *
     * @return boolean
     *
     * @since   1.0
     * @throws \RuntimeException
     */
    public function setLog($options = array(), $priority = LOG_TYPE_ALL, $types = array())
    {
        try {
            $class = 'JPlatform\\log\\JLog';
            $class::addLogger($options, $priority, $types);

        } catch (\Exception $e) {
            throw new \RuntimeException('Unable to set Log: ' . $e->getMessage());
        }

        return true;
    }

    /**
     * Method to add an entry to a Log
     *
     * @param string  $message
     * @param integer $priority
     * @param array   $type
     * @param string  $date
     *
     * @return boolean
     *
     * @since   1.0
     * @throws \RuntimeException
     */
    public function addEntry($message, $priority = 0, $type = '', $date = '')
    {
        /** Message */
        $message = (string)$message;

        /** Priority */
        if (in_array($priority, $this->priorities)) {
        } else {
            $priority = LOG_TYPE_INFO;
        }

        /** Type */
        $type = (string)strtolower(preg_replace('/[^A-Z0-9_\.-]/i', '', $type));

        /** Date */
        $date = Services::Date()->getDate($date);

        /** Log it */
        try {
            if ($type == 'console') {
                Services::Log()->set($message, $priority, $type, $date);

            } else {
                $class = 'JPlatform\\log\\JLog';
                $class::add($message, $priority, $type, $date);
            }

        } catch (\Exception $e) {
            throw new \RuntimeException('Log entry failed for ' . $message . 'Error: ' . $e->getMessage());
        }

        return true;
    }

    /**
     * set Entry
     *
     * @param   $message
     * @param   $priority
     * @param   $type
     * @param   $date
     *
     * @return  int|LogService
     * @since   1.0
     */
    public function set($message, $priority, $type, $date)
    {
        if ((int)Services::Profiler()->on == 0) {
            return;
        }
        if (Services::Registry()->exists('LogProfiler')) {
        } else {
            Services::Registry()->create('LogProfiler');
        }

        Services::Registry()->set('LogProfiler', array($message, $priority, $type, $date));
    }

    /**
     * get console log
     *
     * @return  mixed| object, integer, array console log entries
     * @since   1.0
     */
    public function get($option = null)
    {
        if ($option == 'count') {
            $array = Services::Registry()->getArray('LogProfiler');

            return count($array);
        }

        return Services::Registry()->getArray('LogProfiler');
    }
}
