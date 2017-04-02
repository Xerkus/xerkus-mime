<?php
/**
 * @see       https://github.com/zendframework/zend-mime for the canonical source repository
 * @copyright Copyright (c) 2005-2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-mime/blob/master/LICENSE.md New BSD License
 */

declare(strict_types = 1);

namespace Zend\Mime\Header;

interface Header
{
    public function getFieldName() : string;
    public function getFieldValue() : string;
}
