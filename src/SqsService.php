<?php

namespace Jambura\Sqs;

use Aws\Result;

class JamSqsServiceException extends \Exception
{
}
class SqsService
{
    /**
     * aws class object
     *
     * @var Aws
     */
    private $_aws;

    /**
     * SQS queue url to identify
     *
     * @var string
     */
    private $_queueUrl;

    /**
     * constructor
     *
     * @param string $region - region of the aws sqs
     * @param array $credentials - array of credentials. eg.['key' => 'xxxxx', 'secret' => 'xxxxxxx']
     * @param string $version - version of the aws
     * @param string $queueUrl - url for the queue
     */
    public function __construct(string $region, array $credentials, string $version, string $queueUrl)
    {
        $this->_aws = new Aws($region, $credentials, $version);
        $this->_queueUrl = $queueUrl;
    }

    /**
     * returns \Jambura\Sqs\Aws object
     *
     * @return Aws
     */
    public function aws(): Aws
    {
        return $this->_aws;
    }

    /**
     * returns queue url
     *
     * @return string
     */
    public function getQueueUrl(): string
    {
        return $this->_queueUrl;
    }

    /**
     * send message immediately
     *
     * @param string $message
     * @param array $messageAttribute - eg. ['Title' => ['DataType' => 'String', 'StringValue' => 'Service Run']]
     * 
     * @return Result
     */
    protected function sendImmediateMessage(string $message, array $messageAttribute = []): Result
    {
        return $this->_aws->sendMessage($message, $this->getQueueUrl(), $messageAttribute);
    }

    /**
     * this function will contain all the business logic required upon message receival.
     *
     * @param string $message
     * 
     * @return void
     */
    protected function handle(string $message)
    {
    }

    /**
     * will handle the process after receival of the message.
     * Once done, it will seek for the next message again.
     * to function it properly one must use a callback function or override the handle function
     * for both callback function and the handle function, it will receive the message as a string
     *
     * @param callable $callback
     *
     * @return void
     */
    public function run(callable $callback = null)
    {
        while ($result = $this->_aws->loadSQS()->receiveMessageByLongPolling($this->getQueueUrl())) {

            if (empty($result->get('Messages'))) {
                sleep(1);
                continue;
            }

            if ($callback !== null) {
                call_user_func($callback, $result->get('Messages')[0]['Body']);
            } else {
                $this->handle($result->get('Messages')[0]['Body']);
            }

            $this->_aws->deleteMessage($this->getQueueUrl(), $result->get('Messages')[0]['ReceiptHandle']);
        }
    }
}
