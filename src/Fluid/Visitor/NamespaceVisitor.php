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

use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Attribute;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Node\Opening;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Node\SelfClosing;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Parsable;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\ParsableTrait;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Pragma\InlineNamespace;

class NamespaceVisitor extends SilentVisitor
{
    use ParsableTrait;

    private $namespaces = [];

    public function enter(Parsable $parsable, ?string $index): ?Parsable
    {
        $descendants = $this->retrieveDescendants($parsable);
        if ($parsable instanceof InlineNamespace) {
            $this->assignNamespace(
                (string)$parsable->getPrefix(),
                (string)$parsable->getReference()
            );
        } elseif (
            !empty($descendants)
            && ($parsable instanceof Opening || $parsable instanceof SelfClosing)
            && $this->hasSomeChild(Attribute::class, '__CLASS__', ...$descendants)
            && $this->hasSomeChild('data-namespace-typo3-fluid', 'name', ...$descendants)
            && $this->hasSomeChild('true', 'value', ...$descendants)
        ) {
            foreach ($parsable->getAttributes() as $attribute) {
                if (preg_match('#^xmlns:(.+)$#', (string)$attribute->getName(), $nameMatches)
                    && preg_match('#^\s*http://typo3.org/ns/(.+)$#', (string)$attribute->getValue(), $valueMatches)
                ) {
                    $this->assignNamespace(
                        $nameMatches[1],
                        $this->asPhpNamespace($valueMatches[1])
                    );
                }
            }
        }
        return null;
    }

    /**
     * @return array
     */
    public function getNamespaces(): array
    {
        return $this->namespaces;
    }

    private function asPhpNamespace(string $uriNamespace): string
    {
        return str_replace('/', '\\', $uriNamespace);
    }

    private function assignNamespace(string $prefix, string $reference): void
    {
        $this->namespaces[$prefix] = $reference;
    }
}
