<?php
declare(strict_types=1);

namespace FriendsOfTYPO3\FluidCompiler\Fluid\Exporter;

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

use FriendsOfTYPO3\FluidCompiler\Fluid\Model;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Parsable;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\StringRepresentable;

class DumpingExporter implements StackedDumping
{
    public function dumpStacked(Parsable $parsable, ?array $stack): string
    {
        if ($parsable instanceof StringRepresentable) {
            return (string)$parsable;
        }
        if (null !== $nodeResult = $this->dumpStackedNode($parsable, $stack ?? [])) {
            return $nodeResult;
        }
        if (null !== $assignableResult = $this->dumpStackedInlineAssignable($parsable, $stack ?? [])) {
            return $assignableResult;
        }
        if ($parsable instanceof Model\ViewHelper\Inline) {
            $name = $this->arrayShift($stack, 'name');
            return sprintf('%s(%s)', $name, implode(', ', $stack));
        }
        if ($parsable instanceof Model\Attribute) {
            return sprintf(
                ' %s%s',
                $stack[0],
                isset($stack[1]) ? sprintf('="%s"', $stack[1]) : ''
            );
        }
        if ($parsable instanceof Model\Pragma\InlineNamespace) {
            return sprintf(
                '{namespace %s%s}',
                $parsable->getPrefix(),
                $parsable->getReference() !== null ? '=' . (string)$parsable->getReference() : ''
            );
        }
        if ($parsable instanceof Model\Pragma\InlineEscaping) {
            return sprintf('{escaping %s}', $parsable->isEnabled() ? 'true' : 'false');
        }
        if ($parsable instanceof Model\Inline\Ternary) {
            return sprintf(
                '%s %s%s',
                $stack['if'],
                isset($stack['then']) ? '? ' . $stack['then'] : '?',
                isset($stack['else']) ? (isset($stack['then']) ? ' ' : '') . ': ' . $stack['else'] : ''
            );
        }
        if ($parsable instanceof Model\Inline\Wrapped) {
            return sprintf('{%s}', implode('', $stack));
        }
        if ($parsable instanceof Model\Inline\Chained) {
            return sprintf(' -> %s', implode('', $stack));
        }
        return $stack !== null ? implode('', $stack) : '';
    }

    private function dumpStackedNode(Parsable $parsable, array $stack): ?string
    {
        if ($parsable instanceof Model\Node\Opening
            || $parsable instanceof Model\ViewHelper\Opening
        ) {
            return sprintf('<%s>', implode('', $stack));
        }
        if ($parsable instanceof Model\Node\SelfClosing
            || $parsable instanceof Model\ViewHelper\SelfClosing
        ) {
            return sprintf('<%s />', implode('', $stack));
        }
        if ($parsable instanceof Model\Node\Closing
            || $parsable instanceof Model\ViewHelper\Closing
        ) {
            return sprintf('</%s>', implode('', $stack));
        }
        return null;
    }

    private function dumpStackedInlineAssignable(Parsable $parsable, array $stack): ?string
    {
        if ($parsable instanceof Model\Inline\Assignable\Numeric) {
            return sprintf('%d:%s', $stack['index'], $stack['value']);
        }
        if ($parsable instanceof Model\Inline\Assignable\Variable) {
            return sprintf('%s:%s', $stack['name'], $stack['value']);
        }
        if ($parsable instanceof Model\Inline\Assignable\Quoted) {
            return sprintf(
                '%2$s:%1$s%3$s%1$s',
                str_repeat('\\', $parsable->getEscapeLevel()) . '\'',
                $stack['name'],
                $stack['value']
            );
        }
        return null;
    }

    private function arrayShift(array &$array, string $index): string
    {
        $value = $array[$index];
        unset($array[$index]);
        return $value;
    }
}
