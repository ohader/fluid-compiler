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

use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Inline\Assignable;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Token;

class Inline implements Assignable
{
    private $name;
    private $arguments;

    public function __construct(Token $name, Assignable ...$arguments)
    {
        $this->name = $name;
        $this->arguments = $arguments;
    }

    public function dump(): string
    {
        $arguments = array_map(
            function (Assignable $attribute) {
                return $attribute->dump();
            },
            $this->arguments
        );
        return sprintf(
            '%s(%s)',
            $this->name->dump(),
            implode(', ', $arguments)
        );
    }
}
