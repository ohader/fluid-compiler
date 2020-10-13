<?php
declare(strict_types=1);

namespace FriendsOfTYPO3\FluidCompiler\Fluid\Model;

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

use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Assignable;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Dumping;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Parsable;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Token;

class Attribute implements Parsable, Dumping
{
    private $name;
    private $value;

    public function __construct(Token $name, Assignable $value = null)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function dump(): string
    {
        if ($this->value === null) {
            return $this->name->dump();
        }
        return sprintf(
            '%s="%s"',
            $this->name->dump(),
            $this->value->dump()
        );
    }
}
