<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__)
    ->exclude([
        'bootstrap/cache',
        'storage',
        'vendor',
        'node_modules',
    ])
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

$config = new Config();

$config->registerCustomFixers([
    new Gordinskiy\LineLengthChecker\Rules\LineLengthLimit()
]);

return $config
    ->setRules([
        '@PSR12' => true,

        'Gordinskiy/line_length_limit' => [
            'max_length' => 120,
        ],

        'align_multiline_comment' => true,
        'array_indentation' => true,
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'backtick_to_shell_exec' => true,
        'blank_line_after_namespace' => true,
        'blank_line_after_opening_tag' => true,
        'blank_line_before_statement' => [
            'statements' => ['return'],
        ],
        'binary_operator_spaces' => [
            'default' => 'single_space',
        ],
        'cast_spaces' => ['space' => 'none'],
        'class_attributes_separation' => [
            'elements' => [
                'case' => 'none',
                'const' => 'one',
                'method' => 'one',
                'property' => 'one',
                'trait_import' => 'none',
            ],
        ],
        'clean_namespace' => true,
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        'concat_space' => ['spacing' => 'one'],
        'constant_case' => [
            'case' => 'lower',
        ],
        'declare_equal_normalize' => true,
        'explicit_string_variable' => true,
        'fully_qualified_strict_types' => [
            'leading_backslash_in_global_namespace' => true,
            'import_symbols' => true,
        ],
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'linebreak_after_opening_tag' => true,
        'list_syntax' => ['syntax' => 'short'],
        'lowercase_cast' => true,
        'lowercase_static_reference' => true,
        'magic_constant_casing' => true,
        'magic_method_casing' => true,
        'method_argument_space' => true,
        'method_chaining_indentation' => true,
        'native_function_casing' => true,
        'native_type_declaration_casing' => true,
        'new_with_parentheses' => true,
        'no_blank_lines_after_class_opening' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_empty_phpdoc' => true,
        'no_empty_statement' => true,
        'no_extra_blank_lines' => [
            'tokens' => [
                'case',
                'continue',
                'curly_brace_block',
                'default',
                'extra',
                'return',
                'parenthesis_brace_block',
                'square_brace_block',
                'switch',
                'throw',
                'use',
            ],
        ],
        'no_leading_namespace_whitespace' => true,
        'no_mixed_echo_print' => ['use' => 'echo'],
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_short_bool_cast' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_spaces_after_function_name' => true,
        'no_spaces_around_offset' => [
            'positions' => ['inside', 'outside'],
        ],
        // 'no_superfluous_elseif' => true,
        'no_trailing_comma_in_singleline' => [
            'elements' => [
                'arguments',
                'array',
                'array_destructuring',
                'group_import',
            ],
        ],
        'no_unneeded_control_parentheses' => [
            'statements' => [
                'break',
                'clone',
                'continue',
                'echo_print',
                'return',
                'switch_case',
                'yield',
            ],
        ],
        'no_unneeded_braces' => true,
        'no_unused_imports' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'no_whitespace_before_comma_in_array' => true,
        'no_whitespace_in_blank_line' => true,
        'not_operator_with_space' => false,
        'not_operator_with_successor_space' => false,
        'normalize_index_brace' => true,
        'nullable_type_declaration' => ['syntax' => 'question_mark'],
        'object_operator_without_whitespace' => true,
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
        ],
        'php_unit_method_casing' => [
            'case' => 'camel_case',
        ],
        'php_unit_test_annotation' => [
            'style' => 'prefix',
        ],
        'phpdoc_align' => [
            'align' => 'left',
            'spacing' => 1,
        ],
        'phpdoc_indent' => true,
        'phpdoc_no_access' => true,
        'phpdoc_no_empty_return' => true,
        'phpdoc_no_package' => true,
        'phpdoc_no_useless_inheritdoc' => true,
        'phpdoc_order' => [
            'order' => [
                'param',
                'throws',
                'return',
            ],
        ],
        'phpdoc_param_order' => true,
        'phpdoc_scalar' => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_trim' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_types' => true,
        'phpdoc_var_without_name' => true,
        'return_assignment' => true,
        'return_type_declaration' => ['space_before' => 'none'],
        'short_scalar_cast' => true,
        'single_blank_line_at_eof' => true,
        'single_class_element_per_statement' => [
            'elements' => [
                'const',
                'property',
            ],
        ],
        'single_import_per_statement' => [
            'group_to_single_imports' => false,
        ],
        'single_line_after_imports' => true,
        'single_line_empty_body' => true,
        'single_line_comment_style' => [
            'comment_types' => ['asterisk', 'hash'],
        ],
        'single_line_comment_spacing' => true,
        'single_quote' => true,
        'simple_to_complex_string_variable' => true,
        'simplified_if_return' => true,
        'simplified_null_return' => true,
        'space_after_semicolon' => true,
        'standardize_not_equals' => true,
        'switch_continue_to_break' => true,
        'ternary_operator_spaces' => true,
        'trailing_comma_in_multiline' => [
            'after_heredoc' => false,
            'elements' => [
                'arguments',
                'arrays',
                'parameters',
            ],
        ],
        'trim_array_spaces' => true,
        'type_declaration_spaces' => [
            'elements' => [
                'constant',
                'function',
                'property',
            ],
        ],
        'types_spaces' => ['space' => 'none'],
        'unary_operator_spaces' => true,
        'whitespace_after_comma_in_array' => [
            'ensure_single_space' => true,
        ],
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setUsingCache(true)
    ->setCacheFile(__DIR__ . '/.php-cs-fixer.cache')
    ->setLineEnding("\n");
