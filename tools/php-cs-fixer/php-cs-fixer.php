<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$rules = [
    '@PSR2'                                       => true,
    'return_type_declaration'                     => true,
    'array_indentation'                           => true,
    'align_multiline_comment'                     => [
        'comment_type' => 'phpdocs_like',
    ],
    'binary_operator_spaces'                      => [
        'operators' => [
            '='  => 'align_single_space_minimal',
            '+=' => 'align_single_space_minimal',
            '-=' => 'align_single_space_minimal',
            '=>' => 'align_single_space_minimal_by_scope',
        ],
    ],
    'blank_line_after_namespace'                  => true,
    'blank_line_after_opening_tag'                => true,
    'blank_line_before_statement'                 => [
        'statements' => [
            'if',
            'try',
            'return',
            'break',
            'continue',
            'do',
            'switch',
        ],
    ],
    'cast_spaces'                                 => [
        'space' => 'none',
    ],
    'class_definition'                            => true,
    'concat_space'                                => [
        'spacing' => 'one',
    ],
    'declare_equal_normalize'                     => true,
    'elseif'                                      => true,
    'encoding'                                    => true,
    'full_opening_tag'                            => true,
    'function_declaration'                        => true,
    'function_typehint_space'                     => true,
    'single_line_comment_style'                   => [
        'comment_types' => [
            'hash',
        ],
    ],
    'heredoc_to_nowdoc'                           => true,
    'include'                                     => true,
    'indentation_type'                            => true,
    'ordered_class_elements'                      => true,
    'ordered_imports'                             => [
        'sort_algorithm' => 'alpha',
    ],
    'list_syntax'                                 => [
        'syntax' => 'short',
    ],
    'lowercase_cast'                              => true,
    'constant_case'                               => true,
    'lowercase_keywords'                          => true,
    'magic_constant_casing'                       => true,
    'method_argument_space'                       => [
        'on_multiline'                     => 'ensure_fully_multiline',
        'keep_multiple_spaces_after_comma' => false,
    ],
    'visibility_required'                         => true,
    'native_function_casing'                      => true,
    'no_blank_lines_after_class_opening'          => true,
    'no_blank_lines_after_phpdoc'                 => true,
    'no_extra_blank_lines'                        => [
        'tokens' => [
            'extra',
            'throw',
            'use',
            'break',
            'parenthesis_brace_block',
            'return',
            'square_brace_block',
            'switch',
            'case',
            'default',
        ],
    ],
    'no_closing_tag'                              => true,
    'no_empty_phpdoc'                             => true,
    'no_empty_statement'                          => true,
    'no_leading_import_slash'                     => true,
    'no_leading_namespace_whitespace'             => true,
    'no_multiline_whitespace_around_double_arrow' => true,
    'multiline_whitespace_before_semicolons'      => true,
    'no_short_bool_cast'                          => true,
    'no_singleline_whitespace_before_semicolons'  => true,
    'no_spaces_after_function_name'               => true,
    'no_spaces_around_offset'                     => true,
    'no_spaces_inside_parenthesis'                => true,
    'no_trailing_comma_in_singleline'             => true,
    'no_trailing_whitespace'                      => true,
    'no_trailing_whitespace_in_comment'           => true,
    'no_unneeded_control_parentheses'             => true,
    'no_unused_imports'                           => true,
    'no_useless_return'                           => true,
    'no_whitespace_before_comma_in_array'         => true,
    'no_whitespace_in_blank_line'                 => true,
    'normalize_index_brace'                       => true,
    'not_operator_with_successor_space'           => true,
    'object_operator_without_whitespace'          => true,
    'phpdoc_align'                                => true,
    'phpdoc_indent'                               => true,
    'general_phpdoc_tag_rename'                   => true,
    'phpdoc_no_access'                            => true,
    'phpdoc_no_package'                           => true,
    'phpdoc_no_useless_inheritdoc'                => true,
    'phpdoc_scalar'                               => true,
    'phpdoc_single_line_var_spacing'              => true,
    'phpdoc_summary'                              => true,
    'phpdoc_to_comment'                           => true,
    'phpdoc_trim'                                 => true,
    'phpdoc_order'                                => true,
    'phpdoc_types'                                => true,
    'phpdoc_types_order'                          => true,
    'phpdoc_var_without_name'                     => true,
    'increment_style'                             => [
        'style' => 'post',
    ],
    'no_mixed_echo_print'                         => [
        'use' => 'echo',
    ],
    'array_syntax'                                => [
        'syntax' => 'short',
    ],
    'short_scalar_cast'                           => true,
    'simplified_null_return'                      => true,
    'single_blank_line_at_eof'                    => true,
    'single_blank_line_before_namespace'          => true,
    'single_class_element_per_statement'          => true,
    'single_import_per_statement'                 => true,
    'single_line_after_imports'                   => true,
    'single_quote'                                => true,
    'single_trait_insert_per_statement'           => true,
    'space_after_semicolon'                       => true,
    'standardize_not_equals'                      => true,
    'switch_case_semicolon_to_colon'              => true,
    'switch_case_space'                           => true,
    'ternary_operator_spaces'                     => true,
    'trailing_comma_in_multiline'                 => true,
    'trim_array_spaces'                           => true,
    'unary_operator_spaces'                       => true,
    'line_ending'                                 => true,
    'whitespace_after_comma_in_array'             => true,
    'combine_consecutive_issets'                  => true,
    'compact_nullable_typehint'                   => true,
    'control_structure_braces'                    => true,
    'control_structure_continuation_position'     => true,
    'lambda_not_used_import'                      => true,
    'no_useless_else'                             => true,
    'return_assignment'                           => true,
    'ternary_to_null_coalescing'                  => true,
    'types_spaces'                                => true,
];

$finder = Finder::create()
    ->in(
        [
            __DIR__ . '/app',
            __DIR__ . '/config',
            __DIR__ . '/routes',
            __DIR__ . '/tests',
            __DIR__ . '/database',
            __DIR__ . '/Modules',
        ]
    )
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

$config = new Config();

return $config->setFinder($finder)
    ->setRules($rules)
    ->setRiskyAllowed(true)
    ->setUsingCache(false);
