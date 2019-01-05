<?php 

namespace App\Services\Dingtalk\Server;

abstract class Message
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Message constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @param int|string|\EasyDingTalk\Kernel\Messages\Message $message
     *
     * @return \EasyDingTalk\Kernel\Messages\Message
     */
    public static function parse($message): self
    {
        if (is_int($message) || is_string($message)) {
            $message = new Text($message);
        }
        return $message;
    }

    public function type()
    {
        return $this->type;
    }

    public function body()
    {
        return $this->attributes;
    }
    
    /**
     * @return array
     */
    public function transform(): array
    {
        return [
            'msgtype' => $this->type,
            $this->type => $this->attributes,
        ];
    }
}

class Text extends Message
{
    protected $type = 'text';
    
    public function __construct(string $content)
    {
        parent::__construct(compact('content'));
    }
}