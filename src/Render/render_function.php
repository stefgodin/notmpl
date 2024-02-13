<?php


namespace StefGodin\NoTmpl\Render;

/**
 * Proxies {@see NoTmpl::component}
 *
 * @param string $name - The component to render
 * @param array $parameters - Specified additional parameters
 * @return void
 * @throws \StefGodin\NoTmpl\Exception\RenderException
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
 * @throws \StefGodin\NoTmpl\Exception\RenderException
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
 * @return void
 * @throws \StefGodin\NoTmpl\Exception\RenderException
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */
function slot(string $name = 'default'): void
{
    NoTmpl::slot($name);
}

/**
 * Proxies {@see NoTmpl::slotEnd}
 *
 * @return void
 * @throws \StefGodin\NoTmpl\Exception\RenderException
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
 * @return void
 * @throws \StefGodin\NoTmpl\Exception\RenderException
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */
function use_slot(string $name): void
{
    NoTmpl::useSlot($name);
}

/**
 * Proxies {@see NoTmpl::parentSlot}
 *
 * @return void
 * @throws \StefGodin\NoTmpl\Exception\RenderException
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
 * @throws \StefGodin\NoTmpl\Exception\RenderException
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */
function use_slot_end(): void
{
    NoTmpl::useSlotEnd();
}