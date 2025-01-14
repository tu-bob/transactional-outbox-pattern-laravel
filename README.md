# Transactional Outbox Pattern Example with Laravel

This is an example of how to implement the Transactional Outbox Pattern in Laravel.

## Installation

### Prerequisites
- Docker
  - If you don't have docker installed, you can install it by following the instructions
  [here](https://docs.docker.com/get-docker/)
  - If you want to run the service without docker, you need to install php, composer, and a database server (e.g. mysql). 
  Do not forget to create .env file and update database related variables.
### First launch
1. Clone the repository
2. Run the **Make** command to build and run the project
```shell
  make run
```
If you don't have **Make** then you can run the following commands:
```shell
  docker compose up --build -d
  docker exec test-app composer install
  docker exec test-app php artisan migrate
  docker exec test-app php artisan db:seed
```

**Note:** You can find the Makefile with all commands in the root directory of the project.
___
Once the project is up, you can run the `ProcessWebhooks` command with test data:
```shell
  make process-webhooks
```
____

You can also run phpunit tests with the following command:
```shell
  make test
```

---

You can add a new webhook using API endpoint:
```
localhost/api/webhooks
```
Example payload:
```json
{
  "url": "https://webhook-test.info1100.workers.dev/success1",
  "order_id": 23,
  "name": "Terry Smith",
  "event": "Fun night"
}
```

## Description

The project consists of two main parts:
1. `SendWebhooksUseCase` - this is a use case that processes the webhooks and sends them to the external service
using the Transactional Outbox Pattern. In case of an error, a record is saved to `queue_items` table with `retry_at`,
unless the maximum delay is reached. A record in the `queue_items` is ignored if the maximum failed attempts are reached
for the given webhook URL (see `QueueItemRepository`). 

Note: Table row is locked for update.

```php
QueueItemModel::query()
  ...
  ->leftJoin(
      'failed_attempts',
      ...
  )
  ->where('status', WebhookStatus::PENDING)
  ->where('retry_at', '<=', now())
  ->whereRaw('coalesce(failed_attempts.attempts, 0) <= ?', [self::MAX_FAILED_ATTEMPTS])
  ->lockForUpdate()
  ->first();
```
2. `ProcessWebhooks` command - this is a simple Laravel command that reruns the `SendWebhooksUseCase` for all pending webhooks.
To stop the command, you can use Ctrl+C.

## Further improvements

1. Cache - to avoid unnecessary database queries, we can use cache for the `queue_items`, `failed_attempts` tables. 
Cached data can be persisted in batches. Usually, it is done in the end of certain interval or after a certain number 
of processed webhooks.
2. Database - table partitioning is often used to improve the performance of large tables. Tables are usually partitioned
by `retry_at` date. 
3. Metrics - to monitor the performance of the service, we can use metrics. For example, we can measure the number of
processed webhooks, the number of failed webhooks, the number of retries, etc.
4. Entity serialization to improve logging. 
5. Dockerfile - use multi-stage builds to reduce the size of the final image. Apache2 is used for simplicity, but it is
better to use Nginx and PHP-FPM for production. 
