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
     */
    public function __construct($content = '', $params = null) {
        $this->content = $content;
        $this->params = $params;
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
     * Set the message type.
     *
     * @return $this
     */
    public function unicode() {
        $this->type = 'unicode';
        return $this;
    }
}