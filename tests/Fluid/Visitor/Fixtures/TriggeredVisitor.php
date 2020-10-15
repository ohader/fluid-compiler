<?php
declare(strict_types=1);

namespace FriendsOfTYPO3\FluidCompiler\Tests\Fluid\Visitor\Fixtures;

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

use FriendsOfTYPO3\FluidCompiler\Fluid;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Parsable;
use FriendsOfTYPO3\FluidCompiler\Fluid\Visitor\SilentVisitor;
use FriendsOfTYPO3\FluidCompiler\Fluid\Visitor\Visitable;

class TriggeredVisitor extends SilentVisitor implements Visitable
{
    private $context;
    private $triggerName;
    private $contextName;

    private $value;

    public function __construct(Fluid\Context $context, string $triggerName, string $contextName)
    {
        $this->context = $context;
        $this->triggerName = $triggerName;
        $this->contextName = $contextName;
    }

    public function enter(Parsable $parsable, ?string $index): ?Parsable
    {
        if ($parsable instanceof Fluid\Model\Node\SelfClosing
            && (string)$parsable->getName() === $this->triggerName
        ) {
            $this->value = $this->context->getVisitor($this->contextName)->get();
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
