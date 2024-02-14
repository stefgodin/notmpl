<?php


namespace StefGodin\NoTmpl;

use StefGodin\NoTmpl\Engine\RenderContextStack;

/**
 * Starts a component block and loads a specific template for it.
 * Slots of the component are not shared with the parent component which allows reuse of names.
 *
 * @param string $name - The component to render
 * @param array $parameters - Specified additional parameters
 * @return void
 * @throws \StefGodin\NoTmpl\Engine\EngineException
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */
function component(string $name, array $parameters = []): void
{
    RenderContextStack::current()->component($name, $parameters);
}

/**
 * Ends the last open {@see component} tag
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
 * Starts the context of a slot within a component template to allow replacement of the slot content on demand.
 * Slot names must be unique within a component.
 *
 * @param string $name - The slot name
 * @param array $bindings - Parameters to provide to the use-slots bindings
 * @return void
 * @throws \StefGodin\NoTmpl\Engine\EngineException
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */
function slot(string $name = 'default', array $bindings = []): void
{
    RenderContextStack::current()->slot($name, $bindings);
}

/**
 * Ends the context of the last open {@see slot} tag.
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
 * {@see component} tags.
 *
 * @param string $name - The used slot name
 * @param mixed $bindings - The slot bindings to access some exposed values
 * @return void
 * @throws \StefGodin\NoTmpl\Engine\EngineException
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */
function use_slot(string $name = 'default', mixed &$bindings = null): void
{
    RenderContextStack::current()->useSlot($name, $bindings);
}

/**
 * Renders the content of the used parent slot
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
 * Ends the context of the last open {@see use_slot} tag
 *
 * @return void
 * @throws \StefGodin\NoTmpl\Engine\EngineException
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */
function use_slot_end(): void
{
    RenderContextStack::current()->useSlotEnd();
}