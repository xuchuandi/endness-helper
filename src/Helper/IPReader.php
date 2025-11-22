<?php

declare(strict_types=1);
/**
 * This file is part of Kuaijing Bailing.
 *
 * @link     https://www.kuaijingai.com
 * @document https://help.kuaijingai.com
 * @contact  www.kuaijingai.com 7*12 9:00-21:00
 */
namespace Endness\Helper;

use Hyperf\Contract\IPReaderInterface;
use Hyperf\Support\Exception\IPReadFailedException;
use Hyperf\Support\Network;
use Throwable;
use function Hyperf\Support\env;

class IPReader implements IPReaderInterface
{
    public function read(): string
    {
        try {
            $fixedServerIp = env('SERVER_FIXED_IP');
            if ($fixedServerIp) {
                return $fixedServerIp;
            }
            return Network::ip();
        } catch (Throwable $throwable) {
            throw new IPReadFailedException($throwable->getMessage());
        }
    }
}
