<?php


namespace Stefmachine\NoTmpl\Render;

/**
 * Proxies {@see NoTmpl::render}
 *
 * @param string $template - The template to render
 * @param array $parameters - The parameters to be passed to the template
 * @return string
 * @throws \Stefmachine\NoTmpl\Exception\RenderException
 */
function render(string $template, array $parameters = []): string
{
    return NoTmpl::render($template, $parameters);
}

/**
 * Proxies {@see NoTmpl::component}
 *
 * @param string $template - The subcomponent to render
 * @param array $parameters - Specified additional parameters
 * @return void
 * @throws \Stefmachine\NoTmpl\Exception\RenderException
 */
function component(string $template, array $parameters = []): void
{
    NoTmpl::component($template, $parameters);
}

/**
 * Proxies {@see NoTmpl::endComponent}
 *
 * @return void
 * @throws \Stefmachine\NoTmpl\Exception\RenderException
 */
function end_component(): void
{
    NoTmpl::endComponent();
}

/**
 * Proxies {@see NoTmpl::slot}
 *
 * @param string $name - The slot name
 * @return void
 * @throws \Stefmachine\NoTmpl\Exception\RenderException
 */
function slot(string $name): void
{
    NoTmpl::slot($name);
}

/**
 * Proxies {@see NoTmpl::parentSlot}
 *
 * @return void
 * @throws \Stefmachine\NoTmpl\Exception\RenderException
 */
function parent_slot(): void
{
    NoTmpl::parentSlot();
}

/**
 * Proxies {@see NoTmpl::endSlot}
 *
 * @return void
 * @throws \Stefmachine\NoTmpl\Exception\RenderException
 */
function end_slot(): void
{
    NoTmpl::endSlot();
}