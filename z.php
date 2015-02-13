<?php
require __DIR__ . '/vendor/autoload.php';

/**
 * $ref で自分の definitions を参照
 */
function schema_ref_self()
{
    // URL の解決のためにパスを正規化
    $fn = strtr(realpath(__DIR__ . '/schema.json'), array("\\" => "/"));

    $schema = json_decode(file_get_contents($fn));

    // $ref を解決（ソースファイルのパスも指定）
    $refResolver = new JsonSchema\RefResolver();
    $refResolver->resolve($schema, "file://$fn");

    return $schema;
}

/**
 * $ref で別のファイルを参照する
 */
function schema_ref_other_file()
{
    // URL の解決のためにパスを正規化
    $fn = strtr(realpath(__DIR__ . '/schema-f.json'), array("\\" => "/"));

    $schema = json_decode(file_get_contents($fn));

    // $ref を解決（ソースファイルのパスも指定）
    $refResolver = new JsonSchema\RefResolver();
    $refResolver->resolve($schema, "file://$fn");

    return $schema;
}

/**
 * $ref を連想配列で解決する
 */
function schema_ref_predefined()
{
    $schema = json_decode(file_get_contents(__DIR__ . '/schema-r.json'));

    $retriever = new JsonSchema\Uri\Retrievers\PredefinedArray([
        'oreore' => new ArrayObject([
            'type' => 'string',
            'pattern' => '^oreore$',
        ]),
    ]);

    // $ref を解決
    $refResolver = new JsonSchema\RefResolver($retriever);
    $refResolver->resolve($schema);

    return $schema;
}

function varidation($schema)
{
    $input = json_decode(file_get_contents(__DIR__ . '/input.json'));

    $validator = new JsonSchema\Validator();
    $validator->check($input, $schema);

    if (!$validator->isValid())
    {
        foreach ($validator->getErrors() as $error)
        {
            print_r($error);
        }
    }
}

varidation(schema_ref_self());
varidation(schema_ref_other_file());
varidation(schema_ref_predefined());
