# jambura-sqs-service
**jambura-sqs-service** is a lightweight and straightforward SQS service designed for receiving and processing messages. This package functions as a daemon, instantly capturing messages pushed to the designated AWS Simple Queue Service. It also empowers developers with the flexibility to define their message processing logic. This can be achieved either by overriding a specific function or by providing a callback function.

# Usage
You have two options for utilizing this package: either by overriding the **`handle`** function or by supplying a callback function when calling the **`run`** function.

# Overriding Handle Function
1. Create a class that extends the **`SqsService`** class.
```
<?php
require __DIR__ . '/../vendor/autoload.php';

use Jambura\Sqs\SqsService;

class CService extends SqsService {
    protected function handle(string $message) {
        // Your code here
        var_dump($message);
    }
}
```
2. Instantiate the class and call the **`run()`** function.
```
<?php
require __DIR__ . '/CService.php';

(new CService(
    'us-west-2',
    [
        'key' => 'xxxxxx', 'secret' => 'shGxxxxx98y7xxx'
    ],
    'latest',
    'https://sqs.us-west-2.amazonaws.com/1234567890/my-sqs'
    )
)->run();
```
# Using a Callback Function
1. Create an instance of the **`SqsService`** class and provide a callback function as an argument to the **`run()`** function. The callback function will receive the message body as a string parameter.
```
require __DIR__ . '/../vendor/autoload.php';

use Jambura\Sqs\SqsService;

(new SqsService(
    'us-west-2',
    [
        'key' => 'xxxxxxxxx', 'secret' => 'shGxxxxx98y7xxx'
    ],
    'latest',
    'https://sqs.us-west-2.amazonaws.com/1234567890/my-sqs'
    )
)->run(function($message) {
  //your code here
  var_dump($message);
});
```
**Note:** Both of the sample code will output the message.
# Contact
For any inquiries or feedback, feel free to reach out to [support@prepmock.com].

Thank you for choosing jambura-sqs-service! We hope this package simplifies your SQS message handling needs. If you encounter any issues or have suggestions for improvement, please don't hesitate to let us know. Happy coding!
