<?php
declare(strict_types=1);

namespace FriendsOfTYPO3\FluidCompiler\Tests\Fluid\Visitor;

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

use FriendsOfTYPO3\FluidCompiler\Compiler\Compiler;
use FriendsOfTYPO3\FluidCompiler\Fluid;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Factory;
use PHPUnit\Framework\TestCase;

class NamespaceVisitorTest extends TestCase
{
    /**
     * @test
     */
    public function dumpMatchesSyntaxTree(): void
    {
        $compiler = new Compiler(Fluid::VERSION_2x);
        $syntaxTree = $compiler->parseSource(
            '<html xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers" data-namespace-typo3-fluid="true">'
            . '{namespace a=Vendor\A}'
            . '{namespace b=Vendor\B}'
        );

        $factory = new Factory();
        $fluidTree = $factory->buildFromTree($syntaxTree);

        $namespaceVisitor = new Fluid\Visitor\NamespaceVisitor();
        $context = new Fluid\Context(Fluid::VERSION_2x);
        $context->addVisitor('namespace', $namespaceVisitor);
        $traverser = new Fluid\Traverser($fluidTree, $context);
        $traverser->traverse();

        self::assertSame([
            'f' => 'TYPO3\CMS\Fluid\ViewHelpers',
            'a' => 'Vendor\A',
            'b' => 'Vendor\B',
        ], $namespaceVisitor->getNamespaces());
    }
}
