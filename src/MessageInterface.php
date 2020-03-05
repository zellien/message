<?php
/**
 * Created by Bogdan Tereshchenko <development.sites@gmail.com>
 * Copyright: 2006-2019 Bogdan Tereshchenko
 * Link: https://zelliengroup.com/
 * Date: 04.03.2020 20:54
 */

declare(strict_types=1);

namespace Zellien\Message;

use JsonSerializable;

/**
 * Interface MessageInterface
 * @package Zellien\Message
 */
interface MessageInterface extends JsonSerializable {

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name);

}
