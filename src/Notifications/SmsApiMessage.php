<?php
namespace Gr8Shivam\SmsApi\Notifications;

class SmsApiMessage
{
    /**
     * The message content.
     *
     * @var string
     */
    public $content;

    /**
     * Additional Parameters.
     *
     * @var array
     */
    public $params;

    /**
     * Add Headers.
     *
     * @var array
     */
    public $headers=[];

    /**
     * The message type.
     *
     * @var string
     */
    public $type = 'text';

    /**
     * Create a new message instance.
     *
     * @param  string $content
     * @param array $params
     * @param array $headers
     */
    public function __construct($content = '', $params = null, $headers=[]) {
        $this->content = $content;
        $this->params = $params;
        $this->headers = $headers;
    }

    /**
     * Set the message content.
     *
     * @param  string  $content
     * @return $this
     */
    public function content($content) {
        $this->content = $content;
        return $this;
    }

    /**
     * Set the message params.
     *
     * @param  array  $params
     * @return $this
     */
    public function params($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @param $headers
     * @return $this
     */
    public function headers($headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Set the message type.
     *
     * @return $this
     */
    public function unicode() {
        $this->type = 'unicode';
        return $this;
    }
}