<?php

namespace Jambura\Sqs;

use Aws\Result;
use Aws\Sqs\SqsClient;

class Aws
{
    /**
     * sqs client
     *
     * @var SQSClient
     */
    private $_sqsClient;

    /**
     * AWS Configurations
     *
     * @var array
     */
    private $_awsConfig;

    public function __construct(string $region, array $credentials, string $version)
    {
        $this->_awsConfig = [
            'region' => $region,
            'version' => $version,
            'credentials' => [
                'key' => $credentials['key'],
                'secret' => $credentials['secret']
            ]
        ];
    }

    /**
     * load sqs client
     *
     * @return Aws
     */
    public function loadSQS(): Aws
    {
        if (!$this->_sqsClient) {
            $this->_sqsClient = new SqsClient($this->_awsConfig);
        }
        return $this;
    }

    /**
     * get first message in the queue using queueUrl
     *
     * @param string $queueUrl
     * 
     * @return void
     */
    public function getFirstMessage(string $queueUrl)
    {
        $this->_sqsClient->receiveMessage([
            'QueueUrl' => $queueUrl,
            'MaxNumberOfMessages' => 1,
        ])->get('Messages');
    }

    /**
     * delete a Message of the queue using the queue url
     *
     * @param string $queueUrl
     * @param string $receiptHandleMessage
     * 
     * @return Result
     */
    public function deleteMessage(string $queueUrl, string $receiptHandleMessage): Result
    {
        return $this->_sqsClient->deleteMessage([
            'QueueUrl' => $queueUrl,
            'ReceiptHandle' => $receiptHandleMessage,
        ]);
    }

    /**
     * receive message by long polling.
     * AWS will wait for 10 seconds before sending the response.
     * if within this 10 seconds, any message gets queued up, AWS will send the response with the message.
     *
     * @param string $queueUrl
     * 
     * @return Result
     */
    public function receiveMessageByLongPolling(string $queueUrl): Result
    {
        return $this->_sqsClient->receiveMessage(array(
            'AttributeNames' => ['SentTimestamp'],
            'MaxNumberOfMessages' => 1,
            'MessageAttributeNames' => ['All'],
            'QueueUrl' => $queueUrl,
            'WaitTimeSeconds' => 10,
        ));
    }

    /**
     * send message to sqs
     *
     * @param string $message
     * @param string $queueUrl
     * @param array $messageAttribute
     * 
     * @return Result
     */
    public function sendMessage(string $message, string $queueUrl, array $messageAttribute = []): Result
    {
        $params = [
            'DelaySeconds' => 0,
            'MessageAttributes' => $messageAttribute,
            'MessageBody' => $message,
            'QueueUrl' => $queueUrl
        ];

        return $this->_sqsClient->sendMessage($params);
    }
}
