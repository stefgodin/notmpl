<?php


namespace Stefmachine\NoTmpl\Render;

/**
 * Proxies {@see NoTmpl::render()}
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
 * Proxies {@see NoTmpl::embed()}
 *
 * @param string $template - The embedded template to render
 * @param array $parameters - Specified additional parameters
 * @return void
 * @throws \Stefmachine\NoTmpl\Exception\RenderException
 */
function embed(string $template, array $parameters = []): void
{
    NoTmpl::embed($template, $parameters);
}

/**
 * Proxies {@see NoTmpl::merge()}
 *
 * @param string $template - The merged template to render
 * @param array $parameters - Specified additional parameters
 * @return void
 * @throws \Stefmachine\NoTmpl\Exception\RenderException
 */
function merge(string $template, array $parameters = []): void
{
    NoTmpl::merge($template, $parameters);
}

/**
 * Proxies {@see NoTmpl::slot()}
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
 * Proxies {@see NoTmpl::parentSlot()}
 *
 * @return void
 * @throws \Stefmachine\NoTmpl\Exception\RenderException
 */
function parent_slot(): void
{
    NoTmpl::parentSlot();
}

/**
 * Proxies {@see NoTmpl::endSlot()}
 *
 * @return void
 * @throws \Stefmachine\NoTmpl\Exception\RenderException
 */
function end_slot(): void
{
    NoTmpl::endSlot();
}