<?php
declare(strict_types=1);

namespace FriendsOfTYPO3\FluidCompiler\Fluid\Model\Inline\Assignable;

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

use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Assignable as AllAssignable;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Inline\Assignable;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Token;

class Quoted implements Assignable
{
    private $escapeLevel;
    private $name;
    private $value;

    public function __construct(int $escapeLevel, Token $name, AllAssignable $value)
    {
        $this->escapeLevel = $escapeLevel;
        $this->name = $name;
        $this->value = $value;
    }

    public function dump(): string
    {
        if ($this->value === null) {
            return $this->name->dump();
        }
        $quote = str_repeat('\\', $this->escapeLevel) . '\'';
        // @todo resolve quoting level (`'`, `\'`, ...)
        return sprintf(
            '%2$s:%1$s%3$s%1$s',
            $quote,
            $this->name->dump(),
            $this->value->dump()
        );
    }

}
