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
use FriendsOfTYPO3\FluidCompiler\Fluid\Visitor\Visitable;

class Traverser
{
    private $tree;
    private $context;

    /**
     * @var Visitable[]
     */
    private $visitors = [];

    public function __construct(Tree $tree, Context $context = null)
    {
        $this->tree = $tree;
        $this->context = $context ?? Context::buildDefault(Fluid::VERSION_2x);

        foreach ($this->context->getVisitors() as $visitor) {
            $this->addVisitor($visitor);
        }
    }

    /**
     * @return Tree
     */
    public function getTree(): Tree
    {
        return $this->tree;
    }

    /**
     * @return Context
     */
    public function getContext(): Context
    {
        return $this->context;
    }

    public function addVisitor(Visitable $visitor): void
    {
        $this->visitors[] = $visitor;
    }

    public function traverse(): void
    {
        $this->traverseParsable($this->tree, null);
    }

    private function traverseParsable(Fluid\Model\Parsable $parsable, ?string $index): void
    {
        foreach ($this->visitors as $visitor) {
            $visitor->enter($parsable, $index);
            $this->traverseDecentants($parsable);
            $visitor->leave($parsable, $index);
        }
    }

    private function traverseDecentants(Fluid\Model\Parsable $parsable): void
    {
        if (!$parsable instanceof Fluid\Model\Descending) {
            return;
        }
        foreach ($parsable->getDescendants() as $index => $descendant) {
            if ($descendant !== null) {
                $this->traverseParsable($descendant, (string)$index);
            }
        }
    }
}
