<?php
declare(strict_types=1);

namespace FriendsOfTYPO3\FluidCompiler\Fluid\Model\Node;

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

use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Closable;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Descending;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Parsable;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Token;

class Closing implements Parsable, Descending, Closable
{
    private $name;

    public function __construct(Token $name)
    {
        $this->name = $name;
    }

    public function closes(Parsable $other): bool
    {
        if (!$other instanceof Opening) {
            return false;
        }
        return (string)$this->name === (string)$other->getName();
    }

    public function getDescendants(): array
    {
        return [
            $this->name
        ];
    }

    /**
     * @return Token
     */
    public function getName(): Token
    {
        return $this->name;
    }
}
