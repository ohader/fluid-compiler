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
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Descending;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Nameable;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Token;

class Variable implements Assignable, Descending, Nameable
{
    private $name;
    private $value;

    public function __construct(Token $name, AllAssignable $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function name(): string
    {
        return (string)$this->name;
    }

    public function getDescendants(): array
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
        ];
    }

    /**
     * @return Token
     */
    public function getName(): Token
    {
        return $this->name;
    }

    /**
     * @return AllAssignable
     */
    public function getValue(): AllAssignable
    {
        return $this->value;
    }
}
