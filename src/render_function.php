<?php
/*
 * This file is part of the NoTMPL package.
 *
 * (c) Stéphane Godin
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */


namespace StefGodin\NoTmpl;

use StefGodin\NoTmpl\Engine\Node\ComponentNode;
use StefGodin\NoTmpl\Engine\NodeEnder;
use StefGodin\NoTmpl\Engine\RenderContextStack;

/**
 * Starts a component block and loads a specific file for it.
 *
 * @param string $name - The component to render
 * @param array $parameters - Specified additional parameters
 * @return NodeEnder
 * @throws \StefGodin\NoTmpl\Engine\EngineException
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */
function component(string $name, array $parameters = []): NodeEnder
{
    return RenderContextStack::current()->component($name, $parameters);
}

/**
 * Ends the last open {@see component} node
 *
 * @return void
 * @throws \StefGodin\NoTmpl\Engine\EngineException
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */
function component_end(): void
{
    RenderContextStack::current()->componentEnd();
}

/**
 * Starts the context of a slot within a component to allow replacement of the slot content on demand.
 * Reusing slot names within a component is allowed but may not work with parameters binding as only the last slot
 * bindings will be used
 *
 * @param string $name - The slot name
 * @param array $bindings - Parameters to provide to the use-slots bindings
 * @return NodeEnder
 * @throws \StefGodin\NoTmpl\Engine\EngineException
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */
function slot(string $name = ComponentNode::DEFAULT_SLOT, array $bindings = []): NodeEnder
{
    return RenderContextStack::current()->slot($name, $bindings);
}

/**
 * Ends the last open {@see slot} node
 *
 * @return void
 * @throws \StefGodin\NoTmpl\Engine\EngineException
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */
function slot_end(): void
{
    RenderContextStack::current()->slotEnd();
}

/**
 * Starts the context of a use-slot to overwrite the internal content of a slot within a component.
 * Use-slot names must be unique within a component.
 *
 * Usage of 'default' {@see slot} is optional as an implicit one is created for content put directly within
 * {@see component} nodes.
 *
 * @param string $name - The used slot name
 * @param mixed $bindings - The slot bindings to access some exposed values
 * @return NodeEnder
 * @throws \StefGodin\NoTmpl\Engine\EngineException
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */
function use_slot(string $name = ComponentNode::DEFAULT_SLOT, mixed &$bindings = null): NodeEnder
{
    return RenderContextStack::current()->useSlot($name, $bindings);
}

/**
 * Renders the content of the parent slot within a {@see use_slot} context
 *
 * @return void
 * @throws \StefGodin\NoTmpl\Engine\EngineException
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */
function parent_slot(): void
{
    RenderContextStack::current()->parentSlot();
}

/**
 * Ends the last open {@see use_slot} node
 *
 * @return void
 * @throws \StefGodin\NoTmpl\Engine\EngineException
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */
function use_slot_end(): void
{
    RenderContextStack::current()->useSlotEnd();
}