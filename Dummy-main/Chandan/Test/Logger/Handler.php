<?php

namespace Chandan\Test\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * @var int
     */
    protected $loggerType = StripeLogger::CRITICAL;

    /**
     * @var string
     */
    protected $fileName = '/var/log/test.log';
}
