<?php

declare(strict_types=1);

namespace LejSample\Subscription\Domain\Model;

use Lej\Component\Domain\Model\ValueObject;

interface User extends ValueObject
{
    /**
     * @return string
     */
    public function name() : string;
}
