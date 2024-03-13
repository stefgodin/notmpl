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

use StefGodin\NoTmpl\Engine\EnderInterface;
use StefGodin\NoTmpl\Engine\Node\ComponentNode;
use StefGodin\NoTmpl\Engine\RenderContextStack;
use Traversable;

/**
 * Starts a component block and loads a specific file for it.
 *
 * @param string $name The component to render
 * @param array $parameters Specified additional parameters
 * @return EnderInterface
 * @noinspection PhpDocMissingThrowsInspection
 */
function component(string $name, array $parameters = []): EnderInterface
{
    return RenderContextStack::current()->component($name, $parameters);
}

/**
 * Ends the last open {@see component} node
 *
 * @return void
 * @noinspection PhpDocMissingThrowsInspection
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
 * @param string $name The slot name
 * @param array $bindings Parameters to provide to the use-slots bindings
 * @return EnderInterface
 * @noinspection PhpDocMissingThrowsInspection
 */
function slot(string $name = ComponentNode::DEFAULT_SLOT, array $bindings = []): EnderInterface
{
    return RenderContextStack::current()->slot($name, $bindings);
}

/**
 * Ends the last open {@see slot} node
 *
 * @return void
 * @noinspection PhpDocMissingThrowsInspection
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
 * @param string $name The used slot name
 * @param mixed|array &$bindings The slot bindings to access some exposed values
 * @return EnderInterface
 * @noinspection PhpDocMissingThrowsInspection
 */
function use_slot(string $name = ComponentNode::DEFAULT_SLOT, mixed &$bindings = null): EnderInterface
{
    return RenderContextStack::current()->useSlot($name, $bindings);
}

/**
 * Renders the content of the parent slot within a {@see use_slot} context
 *
 * @return void
 * @noinspection PhpDocMissingThrowsInspection
 */
function parent_slot(): void
{
    RenderContextStack::current()->parentSlot();
}

/**
 * Ends the last open {@see use_slot} node and resets the bindings variable referenced to its original value
 *
 * @return void
 * @noinspection PhpDocMissingThrowsInspection
 */
function use_slot_end(): void
{
    RenderContextStack::current()->useSlotEnd();
}

/**
 *
 *
 * @param string $name
 * @return Traversable&EnderInterface
 * @noinspection PhpDocMissingThrowsInspection
 */
function use_repeat_slots(string $name): Traversable&EnderInterface
{
    return RenderContextStack::current()->useRepeatSlots($name);
}