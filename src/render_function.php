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
use StefGodin\NoTmpl\Engine\Node\ComponentNode;
use StefGodin\NoTmpl\Engine\Node\ParentSlotNode;
use StefGodin\NoTmpl\Engine\Node\RawContentNode;
use StefGodin\NoTmpl\Engine\Node\SlotNode;
use StefGodin\NoTmpl\Engine\Node\TextNode;
use StefGodin\NoTmpl\Engine\Node\UseComponentNode;
use StefGodin\NoTmpl\Engine\Node\UseSlotNode;
use StefGodin\NoTmpl\Engine\NodeEnder;
use StefGodin\NoTmpl\Engine\RenderContextStack;
use Stringable;

/**
 * Starts a component block and loads a specific file for it.
 *
 * @param string $name The component to render
 * @param array $parameters Specified additional parameters
 * @return NodeEnder
 * @noinspection PhpDocMissingThrowsInspection
 */
function component(string $name, array $parameters = []): NodeEnder
{
    $ctx = RenderContextStack::current();
    $ctx->getNodeTreeBuilder()
        ->addNode($component = new ComponentNode())
        ->capture(fn() => $ctx->getFileManager()->handle($name, $parameters))
        ->exitNode($component)
        ->addNode(new UseComponentNode($component))
        ->startCapture();
    
    return new NodeEnder(component_end(...));
}

/**
 * Ends the last open {@see component} node
 *
 * @return void
 * @noinspection PhpDocMissingThrowsInspection
 */
function component_end(): void
{
    RenderContextStack::current()->getNodeTreeBuilder()
        ->exitNode(UseComponentNode::getType());
}

/**
 * Starts the context of a slot within a component to allow replacement of the slot content on demand.
 * Slot names can be reused, but needs to be used (via {@see use_slot}) the same number times to allow proper context
 * bindings
 *
 * @param string $name The slot name
 * @param array $bindings Parameters to provide to the use-slots bindings
 * @return NodeEnder
 * @noinspection PhpDocMissingThrowsInspection
 */
function slot(string $name = ComponentNode::DEFAULT_SLOT, array $bindings = []): NodeEnder
{
    RenderContextStack::current()->getNodeTreeBuilder()
        ->addNode(new SlotNode($name, $bindings))
        ->startCapture();
    
    return new NodeEnder(slot_end(...));
}

/**
 * Ends the last open {@see slot} node
 *
 * @return void
 * @noinspection PhpDocMissingThrowsInspection
 */
function slot_end(): void
{
    RenderContextStack::current()->getNodeTreeBuilder()
        ->exitNode(SlotNode::getType());
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
 * @return NodeEnder
 * @noinspection PhpDocMissingThrowsInspection
 */
function use_slot(string $name = ComponentNode::DEFAULT_SLOT, mixed &$bindings = null): NodeEnder
{
    RenderContextStack::current()->getNodeTreeBuilder()
        ->addNode(new UseSlotNode($name, $bindings))
        ->startCapture();
    
    return new NodeEnder(use_slot_end(...));
}

/**
 * Renders the content of the parent slot within a {@see use_slot} context
 *
 * @return void
 * @noinspection PhpDocMissingThrowsInspection
 */
function parent_slot(): void
{
    RenderContextStack::current()->getNodeTreeBuilder()
        ->addNode(new ParentSlotNode());
}

/**
 * Ends the last open {@see use_slot} node and resets the bindings variable referenced to its original value
 *
 * @return void
 * @noinspection PhpDocMissingThrowsInspection
 */
function use_slot_end(): void
{
    RenderContextStack::current()->getNodeTreeBuilder()
        ->exitNode(UseSlotNode::getType());
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
    $ctx = RenderContextStack::current();
    return (function() use ($name, $ctx): Generator {
        $useComponent = $ctx->getNodeTreeBuilder()->getCurrentNode();
        if(!$useComponent instanceof UseComponentNode) {
            return;
        }
        
        foreach($useComponent->getComponent()->getSlots($name) as $i => $slot) {
            if(!$slot->isReplaced()) {
                use_slot($name, $bindings);
                yield $i => $bindings;
                use_slot_end();
            }
        }
    })();
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
    $useComponent = RenderContextStack::current()->getNodeTreeBuilder()->getCurrentNode();
    if(!$useComponent instanceof UseComponentNode) {
        return false;
    }
    
    return !empty(array_filter($useComponent->getComponent()->getSlots($name), fn(SlotNode $s) => !$s->isReplaced()));
}

/**
 * Starts the context of a text tag that will escape any content for HTML output
 *
 * @return NodeEnder
 * @noinspection PhpDocMissingThrowsInspection
 */
function text(): NodeEnder
{
    RenderContextStack::current()->getNodeTreeBuilder()
        ->addNode(new TextNode())
        ->startCapture();
    
    return new NodeEnder(text_end(...));
}

/**
 * Ends the last open {@see text} tag
 *
 * @return void
 * @noinspection PhpDocMissingThrowsInspection
 */
function text_end(): void
{
    RenderContextStack::current()->getNodeTreeBuilder()
        ->exitNode(TextNode::getType());
}

/**
 * @param mixed $value
 * @return void
 * @noinspection PhpDocMissingThrowsInspection
 */
function esc(mixed $value): void
{
    if(!is_string($value) && !is_scalar($value) && !$value instanceof Stringable) {
        $value = '';
    }
    
    RenderContextStack::current()->getNodeTreeBuilder()
        ->addNode(new TextNode())
        ->addNode(new RawContentNode($value))
        ->exitNode(TextNode::getType());
}

/**
 * @param string $text
 * @return void
 * @noinspection PhpDocMissingThrowsInspection
 */
function raw(string $text): void
{
    RenderContextStack::current()->getNodeTreeBuilder()
        ->addNode(new RawContentNode($text));
}