<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude(['vendor', 'build']);

$config = new PhpCsFixer\Config();

return $config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => true,
        'blank_line_after_opening_tag' => true,
        'blank_line_before_statement' => ['statements' => ['return']],
        'cast_spaces' => ['space' => 'single'],
        'compact_nullable_type_declaration' => true,
        'concat_space' => ['spacing' => 'one'],
        'declare_equal_normalize' => ['space' => 'none'],
        'declare_strict_types' => false,
        'function_typehint_space' => true,
        'include' => true,
        'lowercase_cast' => true,
        'lowercase_static_reference' => true,
        'magic_constant_casing' => true,
        'magic_method_casing' => true,
        'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
        'native_function_casing' => true,
        'no_blank_lines_after_class_opening' => true,
        'no_extra_blank_lines' => ['tokens' => ['extra']],
        'no_leading_import_slash' => true,
        'no_leading_namespace_whitespace' => true,
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_spaces_around_offset' => true,
        'no_trailing_comma_in_singleline' => true,
        'no_unused_imports' => true,
        'no_whitespace_before_comma_in_array' => true,
        'normalize_index_brace' => true,
        'object_operator_without_whitespace' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'php_unit_fqcn_annotation' => true,
        'php_unit_method_casing' => ['case' => 'snake_case'],
        'phpdoc_align' => ['align' => 'vertical'],
        'phpdoc_no_access' => true,
        'phpdoc_no_package' => true,
        'phpdoc_scalar' => true,
        'phpdoc_trim' => true,
        'phpdoc_types' => true,
        'short_scalar_cast' => true,

        'single_quote' => true,
        'space_after_semicolon' => true,
        'standardize_not_equals' => true,
        'ternary_operator_spaces' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arrays']],
        'trim_array_spaces' => true,
        'types_spaces' => ['space' => 'single'],
        'unary_operator_spaces' => true,
        'whitespace_after_comma_in_array' => true,
    ])
    ->setFinder($finder);
