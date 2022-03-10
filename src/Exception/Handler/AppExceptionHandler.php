<?php

declare(strict_types=1);
/**
 * This file is part of Endness.
 *
 * @link     https://www.yunEndness.cn
 * @document https://www.yunEndness.cn/document/
 * @contact  www.yunEndness.cn 7*12 9:00-21:00
 * @license  https://www.yunEndness.cn/LICENSE
 */
namespace Endness\Exception\Handler;

use Endness\Helper\ApiHelper;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Utils\Codec\Json;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class AppExceptionHandler extends ExceptionHandler
{
    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    public function __construct(StdoutLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        switch (true) {
            case $throwable instanceof ValidationException:
                $message = $throwable->validator->errors()->first();
                $this->logger->warning('validation: ' . $message);
                $errorData = Json::encode(ApiHelper::genErrorData($message));
                return $response->withHeader('Content-Type', 'application/json;charset=utf-8')->withHeader('Server', 'Hyperf')->withStatus(200)->withBody(new SwooleStream($errorData));
        }
        $errMsg = sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile());
        $this->logger->error($errMsg);
        $this->logger->error($throwable->getTraceAsString());

        $errorData = Json::encode(ApiHelper::genErrorData(env('APP_ENV') == 'dev' ? $errMsg : 'Internal Server Error.', ApiHelper::CODE_ERROR));

        return $response->withHeader('Server', 'Hyperf')->withStatus(500)->withBody(new SwooleStream($errorData));
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
