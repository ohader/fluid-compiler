<?php
declare(strict_types=1);

namespace FriendsOfTYPO3\FluidCompiler\Compiler;

use Hoa\Compiler\Llk\TreeNode;
use Hoa\Visitor\Element;
use Hoa\Visitor\Visit;

/**
 * Class \Hoa\Compiler\Visitor\Dump.
 *
 * Dump AST produced by LL(k) compiler.
 *
 * @copyright  Copyright © 2007-2017 Hoa community
 * @license    New BSD License
 */
class Visitor implements Visit
{
    /**
     * Indentation depth.
     *
     * @var int
     */
    protected static $_i = 0;

    private $ignoredIds;

    public function __construct(string ...$ignoredIds)
    {
        $this->ignoredIds = $ignoredIds;
    }

    /**
     * Visit an element.
     *
     * @param Element $element Element to visit.
     * @param mixed                 &$handle Handle (reference).
     * @param mixed $eldnah Handle (not reference).
     * @return  mixed
     */
    public function visit(Element $element, &$handle = null, $eldnah = null): string
    {
        ++self::$_i;

        $out = str_repeat('>  ', self::$_i);
        $skipChildren = false;

        if (strpos($element->getId(), '#name') === 0) {
            $skipChildren = true;
            $values = array_map(
                function (TreeNode $child) {
                    return $child->getValue()['value'];
                },
                $element->getChildren()
            );
            $out .= '#name' . '(' . implode('', $values) . ')';
        } else {
            $out .= $element->getId();
        }

        /** @var $element TreeNode */
        if (null !== $value = $element->getValue()) {
            $out .=
                '(' .
                ('default' !== $value['namespace']
                    ? $value['namespace'] . ':'
                    : '') .
                $value['token'] . ', ' .
                $value['value'] . ')';
        }

        $data = $element->getData();

        if (!empty($data)) {
            $out .= ' ' . $this->dumpData($data);
        }

        $out .= "\n";

        if (!$skipChildren) {
            $out .= $this->renderChildren($element, $handle, $eldnah);
        }

        --self::$_i;

        return $out;
    }

    private function getValue(Element $element)
    {
        $elementValue = $element->getValue();
        if (!$this->isNamedNode()) {
            return $elementValue;
        }
        $values = array_map(
            function (TreeNode $child) {
                return $child->getValue()['value'];
            },
            $element->getChildren()
        );
        return [
            'namespace' => $elementValue['namespace'],
            'token' => implode('', $values),
        ];
    }

    private function isNamedNode(Element $element): bool
    {
        return strpos($element->getId(), '#name') === 0;
    }

    private function renderChildren(Element $element, &$handle = null, $eldnah = null): string
    {
        $out = '';
        foreach ($element->getChildren() as $child) {
            $out .= $child->accept($this, $handle, $eldnah);
        }
        return $out;
    }

    /**
     * Dump data.
     *
     * @param   mixed  $data    Data.
     * @return  string
     */
    protected function dumpData($data): string
    {
        $out = null;

        if (!is_array($data)) {
            return $data;
        }

        foreach ($data as $key => $value) {
            $out .= '[' . $key . ' => ' . $this->dumpData($value) . ']';
        }

        return $out;
    }
}
