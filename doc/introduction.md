# Introduction/Overview

Welcome to the documentation for the NoTMPL rendering engine. NoTMPL is a lightweight PHP library that facilitates
template composition, allowing developers to build dynamic web applications efficiently. This page serves as an
introduction to NoTMPL, providing an overview of its main features and capabilities.

## Overview

NoTMPL provides a simple yet powerful API for template composition, making it easy to create and manage complex web
pages. With NoTMPL, developers can define reusable components, pass data between components, and compose pages using
slots.

The key concepts in NoTMPL include:

- **Components**: NoTMPL treats each file as a component by default, allowing developers to define reusable pieces of
  markup or logic.

- **Directories and Aliasing**: NoTMPL supports directory configuration, making it easier to reference component files.
  Aliasing allows for shorter and more intuitive component names.

- **Passing Values**: Developers can pass data from parent components to child components, enabling dynamic content
  rendering.

- **Slots**: NoTMPL introduces the concept of slots, which are placeholders within components where content can be
  injected. This allows for flexible and dynamic page composition.

- **Named Slots**: Slots can be named, allowing developers to specify different areas within a component for content
  injection. This enables more granular control over page layout and content.

- **Slot Default Content**: NoTMPL supports default content for slots, ensuring that components remain functional even
  when specific content is not provided.

- **Slot Value Binding**: Developers can bind values to slots, making it possible to pass data directly to slots from
  parent components.

- **Nesting Components**: NoTMPL supports nesting components within each other, allowing for hierarchical composition of
  web pages.

## Main Features

- Lightweight and easy to integrate into existing PHP projects.
- Simplified template composition through components and slots.
- Support for passing data between components.
- Named slots for fine-grained control over page layout.
- Security measures to prevent XSS attacks.
- Extensible API for customizing rendering behavior.

Whether you're building a simple website or a complex web application, NoTMPL provides the tools you need to create
dynamic and maintainable templates. In the following sections, we'll delve deeper into each feature of the NoTMPL
rendering engine, providing examples and best practices for effective template composition.

---
Interested? Read the [getting started](./getting_started.md) page.