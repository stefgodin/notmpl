<?php


namespace Stefmachine\NoTmpl\Render;

/**
 * Proxies {@see NoTmpl::render()}
 *
 * @param string $template - The template to render
 * @param array $parameters
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
 * @param string $template
 * @param array|null $parameters
 * @return void
 * @throws \Stefmachine\NoTmpl\Exception\RenderException
 */
function embed(string $template, array|null $parameters = null): void
{
    NoTmpl::embed($template, $parameters);
}

/**
 * Proxies {@see NoTmpl::merge()}
 *
 * @param string $template - The merged template to render
 * @param array|null $parameters - Specified parameters or the parameters of the current context
 * @return void
 * @throws \Stefmachine\NoTmpl\Exception\RenderException
 */
function merge(string $template, array|null $parameters = null): void
{
    NoTmpl::merge($template, $parameters);
}

/**
 * Proxies {@see NoTmpl::block()}
 *
 * @param string $name
 * @return void
 * @throws \Stefmachine\NoTmpl\Exception\RenderException
 */
function block(string $name): void
{
    NoTmpl::block($name);
}

/**
 * Proxies {@see NoTmpl::parentBlock()}
 *
 * @return void
 * @throws \Stefmachine\NoTmpl\Exception\RenderException
 */
function parent_block(): void
{
    NoTmpl::parentBlock();
}

/**
 * Proxies {@see NoTmpl::endBlock()}
 *
 * @return void
 * @throws \Stefmachine\NoTmpl\Exception\RenderException
 */
function end_block(): void
{
    NoTmpl::endBlock();
}