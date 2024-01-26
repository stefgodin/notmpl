<?php


namespace Stefmachine\NoTmpl\Render;

function render(string $template, array $parameters = []): string
{
    $context = new RenderContext(
        RenderContextStack::instance(),
        $parameters
    );
    return $context->render($template);
}

function embed(string $template, array|null $parameters = null): string
{
    return RenderContextStack::instance()->getCurrentContext()->embed($template, $parameters);
}

function extend(string $template, array|null $parameters = null): string
{
    return RenderContextStack::instance()->getCurrentContext()->extend($template, $parameters);
}

function block(string $name): string
{
    return RenderContextStack::instance()->getCurrentContext()->block($name);
}

function end_block(): string
{
    return RenderContextStack::instance()->getCurrentContext()->endBlock();
}