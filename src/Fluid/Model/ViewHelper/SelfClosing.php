<?php
declare(strict_types=1);

namespace FriendsOfTYPO3\FluidCompiler\Fluid\Model\ViewHelper;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Attribute;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Dumping;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Parsable;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Token;

class SelfClosing implements Parsable, Dumping
{
    private $name;
    private $attributes;

    public function __construct(Token $name, Attribute ...$attributes)
    {
        $this->name = $name;
        $this->attributes = $attributes;
    }

    public function dump(): string
    {
        $attributes = array_map(
            function (Attribute $attribute) {
                return $attribute->dump();
            },
            $this->attributes
        );
        array_unshift($attributes, '');
        return sprintf(
            '<%s%s />',
            $this->name->dump(),
            implode(' ', $attributes)
        );
    }
}
