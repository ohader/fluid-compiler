<?php
declare(strict_types=1);

namespace FriendsOfTYPO3\FluidCompiler\Fluid\Visitor;

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

use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Parsable;

class SilentVisitor implements Visitable
{
    public function enter(Parsable $parsable, ?string $index): ?Parsable
    {
        return null;
    }

    public function leave(Parsable $parsable, ?string $index): ?Parsable
    {
        return null;
    }
}
