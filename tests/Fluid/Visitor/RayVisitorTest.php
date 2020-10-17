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
use FriendsOfTYPO3\FluidCompiler\Tests\Fluid\Visitor\Fixtures\TriggeredVisitor;
use PHPUnit\Framework\TestCase;

class RayVisitorTest extends TestCase
{
    /**
     * @test
     */
    public function dumpMatchesSyntaxTree(): void
    {
        $compiler = new Compiler(Fluid::VERSION_2x);
        $syntaxTree = $compiler->parseSource(
            '<div data-a data-b="c">'
            . '{escaping on}'
            . '<f:anything below="{this.node}">'
            . '<trigger />'
        );

        $factory = new Factory();
        $fluidTree = $factory->buildFromTree($syntaxTree);

        $rayVisitor = new Fluid\Visitor\RayVisitor();
        $context = new Fluid\Context(Fluid::VERSION_2x);
        $context->addVisitor('ray', $rayVisitor);

        $traverser = new Fluid\Traverser($fluidTree, $context);
        $triggeredVisitor = new TriggeredVisitor($context, 'trigger', 'ray');
        $traverser->addVisitor($triggeredVisitor);
        $traverser->traverse();

        /** @var Fluid\Visitor\RayItem $rayItem */
        $rayItem = $triggeredVisitor->getValue();
        self::assertCount(4, $rayItem);
        self::assertInstanceOf(Fluid\Visitor\RayItem::class, $rayItem);

        $ray = $rayItem->get();
        self::assertInstanceOf(Fluid\Model\Node\Opening::class, $ray[0]);
        self::assertSame('div', (string)$ray[0]->getName());
        self::assertInstanceOf(Fluid\Model\Pragma\InlineEscaping::class, $ray[1]);
        self::assertTrue($ray[1]->isEnabled());
        self::assertInstanceOf(Fluid\Model\ViewHelper\Opening::class, $ray[2]);
        self::assertSame('f:anything', (string)$ray[2]->getName());
        self::assertInstanceOf(Fluid\Model\Node\SelfClosing::class, $ray[3]);
        self::assertSame('trigger', (string)$ray[3]->getName());
    }
}
