<?php
declare(strict_types=1);

namespace FriendsOfTYPO3\FluidCompiler\Fluid;

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
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Tree;
use FriendsOfTYPO3\FluidCompiler\Fluid\Visitor\NamespaceVisitor;
use FriendsOfTYPO3\FluidCompiler\Fluid\Visitor\Visitable;

class Context
{
    /**
     * @var string
     */
    private $version;

    /**
     * @var array<string, Visitable>
     */
    private $visitors = [];

    public static function buildDefault(string $version = Fluid::VERSION_2x): self
    {
        $target = new static($version);
        $target->addVisitor(NamespaceVisitor::class, new NamespaceVisitor());
        return $target;
    }

    public function __construct(string $version = Fluid::VERSION_2x)
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    public function addVisitor(string $name, Visitable $visitor): void
    {
        $this->visitors[$name] = $visitor;
    }

    public function getVisitor(string $name): ?Visitable
    {
        return $this->visitors[$name] ?? null;
    }

    /**
     * @return array<string, Visitable>
     */
    public function getVisitors(): array
    {
        return $this->visitors;
    }
}
