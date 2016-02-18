<?php

$header = <<<EOF
This file is part of the async generator runtime project.

(c) Julien Bianchi <contact@jubianchi.fr>

This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOF;

Symfony\CS\Fixer\Contrib\HeaderCommentFixer::setHeader($header);

return Symfony\CS\Config\Config::create()
    // use default SYMFONY_LEVEL and extra fixers:
    ->fixers(array(
        'psr0',
        'encoding',
        'braces',
        'elseif',
        'eof_ending',
        'function_call_space',
        'function_declaration',
        'indentation',
        'line_after_namespace',
        'linefeed',
        'lowercase_constants',
        'lowercase_keywords',
        'method_argument_space',
        'multiple_use',
        'parenthesis',
        'php_closing_tag',
        'single_line_after_imports',
        'trailing_spaces',
        'visibility',
        'array_element_no_space_before_comma',
        'array_element_white_space_after_comma',
        'blankline_after_open_tag',
        'duplicate_semicolon',
        'extra_empty_lines',
        'function_typehint_space',
        'namespace_no_leading_whitespace',
        'new_with_braces',
        'no_blank_lines_after_class_opening',
        'no_empty_lines_after_phpdocs',
        'object_operator',
        'operators_spaces',
        'phpdoc_indent',
        'phpdoc_inline_tag',
        'phpdoc_no_access',
        'phpdoc_no_empty_return',
        'phpdoc_params',
        'phpdoc_scalar',
        'phpdoc_separation',
        'phpdoc_short_description',
        'remove_leading_slash_use',
        'remove_lines_between_uses',
        'return',
        'self_accessor',
        'single_array_no_trailing_comma',
        'single_blank_line_before_namespace',
        'single_quote',
        'spaces_cast',
        'ternary_spaces',
        'trim_array_spaces',
        'unary_operators_spaces',
        'unused_use',
        'whitespacy_lines',
        'concat_with_spaces',
        'header_comment',
        'ordered_use',
        'phpdoc_order',
        'short_array_syntax'
    ))
    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()
            ->in(__DIR__ . DIRECTORY_SEPARATOR . 'src')
            ->in(__DIR__ . DIRECTORY_SEPARATOR . 'tests')
            ->in(__DIR__ . DIRECTORY_SEPARATOR . 'examples')
    )
;
