<?php


namespace StefGodin\NoTmpl;

/**
 * Proxies {@see NoTmpl::component}
 *
 * @param string $name - The component to render
 * @param array $parameters - Specified additional parameters
 * @return void
 * @throws \StefGodin\NoTmpl\Engine\EngineException
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */
function component(string $name, array $parameters = []): void
{
    NoTmpl::component($name, $parameters);
}

/**
 * Proxies {@see NoTmpl::componentEnd}
 *
 * @return void
 * @throws \StefGodin\NoTmpl\Engine\EngineException
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */
function component_end(): void
{
    NoTmpl::componentEnd();
}

/**
 * Proxies {@see NoTmpl::slot}
 *
 * @param string $name - The slot name
 * @param array $bindings - Parameters to provide to the use-slots bindings
 * @return void
 * @throws \StefGodin\NoTmpl\Engine\EngineException
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */
function slot(string $name = 'default', array $bindings = []): void
{
    NoTmpl::slot($name, $bindings);
}

/**
 * Proxies {@see NoTmpl::slotEnd}
 *
 * @return void
 * @throws \StefGodin\NoTmpl\Engine\EngineException
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */
function slot_end(): void
{
    NoTmpl::slotEnd();
}

/**
 * Proxies {@see NoTmpl::slot}
 *
 * @param string $name - The slot name
 * @param array|null $bindings - The slot bindings to access some exposed values
 * @return void
 * @throws \StefGodin\NoTmpl\Engine\EngineException
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */
function use_slot(string $name = 'default', array|null &$bindings = null): void
{
    NoTmpl::useSlot($name, $bindings);
}

/**
 * Proxies {@see NoTmpl::parentSlot}
 *
 * @return void
 * @throws \StefGodin\NoTmpl\Engine\EngineException
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */
function parent_slot(): void
{
    NoTmpl::parentSlot();
}

/**
 * Proxies {@see NoTmpl::slotEnd}
 *
 * @return void
 * @throws \StefGodin\NoTmpl\Engine\EngineException
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */
function use_slot_end(): void
{
    NoTmpl::useSlotEnd();
}