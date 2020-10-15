<?php
declare(strict_types=1);

namespace FriendsOfTYPO3\FluidCompiler\Fluid\Model;

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

use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Node;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\ViewHelper;
use Hoa\Compiler\Llk\TreeNode;

class Factory
{
    use ParsableTrait;

    private const NODE_CLASS_NAMES = [
        'Node' => [
            '#node_' => Node\Opening::class,
            '#_node_' => Node\SelfClosing::class,
            '#_node' => Node\Closing::class,
        ],
        'ViewHelper' => [
            '#node_' => ViewHelper\Opening::class,
            '#_node_' => ViewHelper\SelfClosing::class,
            '#_node' => ViewHelper\Closing::class,
        ],
    ];

    public function buildFromTree(TreeNode $node): ?Tree
    {
        $tree = $this->buildFromNode($node);
        if ($tree instanceof Tree) {
            return $tree;
        }
        return null;
    }

    public function buildFromNode(TreeNode $node, TreeNode $parentNode = null): ?Parsable
    {
        $id = $node->getId();
        $children = $this->buildChildren($node);
        switch ($id) {
            case 'token':
                $value = $this->getTokenValue($node);
                return new Token(
                    $value['value'],
                    $value['offset'],
                    $value['token'],
                    $value['namespace']
                );
            case '#root':
                return new Tree(...$children);
            case '#ns':
                return new Pragma\InlineNamespace(...$children);
            case '#escaping':
                return new Pragma\InlineEscaping(...$children);
            case '#text':
                return new Text(...$children);
            case '#node_':
            case '#_node_':
            case '#_node':
                if ($this->hasSomeChild('name_vh', 'token', ...$children)) {
                    $className = static::NODE_CLASS_NAMES['ViewHelper'][$id];
                } else {
                    $className = static::NODE_CLASS_NAMES['Node'][$id];
                }
                return new $className(...$children);
            case '#attr':
                return new Attribute(...$children);
            case '#variable':
                return new Inline\Variable(...$children);
            case '#ternary':
                return new Inline\Ternary(...$children);
            case '#ternary_if':
                return new Inline\Ternary\IfPart(...$children);
            case '#ternary_then':
                return new Inline\Ternary\ThenPart(...$children);
            case '#ternary_else':
                return new Inline\Ternary\ElsePart(...$children);
            case '#arg_variable':
                return new Inline\Assignable\Variable(...$children);
            case '#arg_numeric':
                return new Inline\Assignable\Numeric(...$children);
            case '#arg_quoted':
                return new Inline\Assignable\Quoted(0, ...$children);
            case '#arg_quoted_esc':
                return new Inline\Assignable\Quoted(1, ...$children);
            case '#inline_vh':
                return new ViewHelper\Inline(...$children);
            case '#inline_wrapped':
                return new Inline\Wrapped(...$children);
            case '#inline_chained':
                return new Inline\Chained(...$children);
            default:
                printf(
                    '+ Unmatched %s(%s)' . PHP_EOL,
                    $id,
                    implode(', ', $this->identifyChildren(...$children))
                );
        }
        return null;
    }

    /**
     * @param TreeNode $node
     * @return Parsable[]
     */
    private function buildChildren(TreeNode $node): array
    {
        return array_filter(
            array_map(
                function (TreeNode $childNode) use ($node) {
                    return $this->buildFromNode($childNode, $node);
                },
                $node->getChildren()
            )
        );
    }

    private function identifyChildren(Parsable ...$children): array
    {
        return array_map(
            function (Parsable $child) {
                $type = sprintf('<%s>', get_class($child));
                if ($child instanceof Identifiable) {
                    return sprintf('%s%s', $type, $child->identify());
                } else {
                    return $type;
                }
            },
            $children
        );
    }

    private function getTokenValue(TreeNode $node): array
    {
        $value = $node->getValue();
        if ($value['token'] === 'quote_empty') {
            $value['value'] = '';
        }
        return $value;
    }
}
