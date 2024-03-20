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

use Generator;
use StefGodin\NoTmpl\Engine\EnderInterface;
use StefGodin\NoTmpl\Engine\Node\ComponentNode;
use StefGodin\NoTmpl\Engine\RenderContextStack;

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
 * Slot names can be reused, but needs to be used (via {@see use_slot}) the same number times to allow proper context
 * bindings
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
 * Use-slot names can be repeated but will only share the context of their index-equivalent slot.
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
 * Creates a generator that will start the context of a use slot and close it on every iteration
 * The number of time iterated is the number of unused slots of the given name defined in the component.
 *
 * ```php
 * <?php foreach(use_repeat_slots('my_slot') as $binds): ?>
 *     // within the context of my_slot
 * <?php endforeach ?>
 * ```
 *
 * @param string $name
 * @return Generator
 * @noinspection PhpDocMissingThrowsInspection
 */
function use_repeat_slots(string $name = ComponentNode::DEFAULT_SLOT): Generator
{
    return RenderContextStack::current()->useRepeatSlots($name);
}

/**
 * Checks if there are slots of the given name that were not yet used.
 *
 * @param string $name
 * @return bool
 * @noinspection PhpDocMissingThrowsInspection
 */
function has_slot(string $name = ComponentNode::DEFAULT_SLOT): bool
{
    return RenderContextStack::current()->hasSlot($name);
}