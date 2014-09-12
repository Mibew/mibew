// Testing Handlebars helpers
module('Handlebars helpers');

// Test "urlReplace" Handlebars helper
test('urlReplace', function() {
    var template = '{{urlReplace foo}}';
    var compiledTemplate = Handlebars.compile(template);

    equal(
        compiledTemplate({foo: 'http://example.com'}),
        '<a href="http://example.com" target="_blank">http://example.com</a>',
        'Test HTTP URL'
    );

    equal(
        compiledTemplate({foo: 'https://example.com'}),
        '<a href="https://example.com" target="_blank">https://example.com</a>',
        'Test HTTPS URL'
    );

    equal(
        compiledTemplate({foo: 'ftp://example.com'}),
        '<a href="ftp://example.com" target="_blank">ftp://example.com</a>',
        'Test FTP URL'
    );

    equal(
        compiledTemplate({foo: 'plain text'}),
        'plain text',
        'Test not a URL'
    );

    equal(
        compiledTemplate({foo: 456}),
        '456',
        'Test number argument'
    );
});

// Test "ifEven" Handlebars helper
test('ifEven', function() {
    var template = '{{#ifEven foo}}true{{else}}false{{/ifEven}}';
    var compiledTemplate = Handlebars.compile(template);

    equal(
        compiledTemplate({foo: 0}),
        'true',
        'Test even value'
    );

    equal(
        compiledTemplate({foo: 1}),
        'false',
        'Test odd value'
    );
});

// Test "ifOdd" Handlebars helper
test('ifOdd', function() {
    var template = '{{#ifOdd foo}}true{{else}}false{{/ifOdd}}';
    var compiledTemplate = Handlebars.compile(template);

    equal(
        compiledTemplate({foo: 0}),
        'false',
        'Test even value'
    );

    equal(
        compiledTemplate({foo: 1}),
        'true',
        'Test odd value'
    );
});

// Test "ifAny" Handlebars helper
test('ifAny', function() {
    var template = '{{#ifAny foo bar baz}}true{{else}}false{{/ifAny}}';
    var compiledTemplate = Handlebars.compile(template);

    equal(
        compiledTemplate({}),
        'false',
        'Test only falsy values'
    );

    equal(
        compiledTemplate({baz: true}),
        'true',
        'Test only one true value'
    );

    equal(
        compiledTemplate({foo: true, bar: 1}),
        'true',
        'Test more than one true values'
    );
});

// Test "ifEqual" Handlebars helper
test('ifEqual', function() {
    var template = '{{#ifEqual left right}}true{{else}}false{{/ifEqual}}';
    var compiledTemplate = Handlebars.compile(template);

    equal(
        compiledTemplate({left: 12, right: "foo"}),
        'false',
        'Test different values'
    );

    equal(
        compiledTemplate({left: "10", right: 10}),
        'true',
        'Test equal values with different types'
    );

    equal(
        compiledTemplate({left: "Bar", right: "Bar"}),
        'true',
        'Test equal values'
    );
});

// Test "repeat" Handlebars helper
test('repeat', function() {
    var template = '{{#repeat times}}{{foo}}{{/repeat}}';
    var compiledTemplate = Handlebars.compile(template);

    equal(
        compiledTemplate({foo: 'Foo.', times: 3}),
        'Foo.Foo.Foo.',
        'Test repeating'
    );
});

// Test "replace" Handlebars helper
test('replace', function() {
    var template = '{{#replace search replacement}}{{source}}{{/replace}}';
    var compiledTemplate = Handlebars.compile(template);

    equal(
        compiledTemplate({
            source: 'test source',
            search: 'source',
            replacement: 'target'
        }),
        'test target',
        'Test single replace'
    );

    equal(
        compiledTemplate({
            source: 'Hello\ncruel\nworld!\n',
            search: '\n',
            replacement: '<br/>'
        }),
        'Hello<br/>cruel<br/>world!<br/>',
        'Test multiple replace'
    );

    equal(
        compiledTemplate({
            source: '{{!-- comment',
            search: '{{!--',
            replacement: '<!--'
        }),
        '<!-- comment',
        'Test special regexp characters'
    );

    equal(
        compiledTemplate({
            source: '010203040506070809',
            search: 0,
            replacement: ''
        }),
        '123456789',
        'Test a number as the search value'
    );
});

// Test "cutString" Handlebars helper
test('cutString', function() {
    var template = '{{#cutString length}}{{str}}{{/cutString}}';
    var compiledTemplate = Handlebars.compile(template);

    equal(
        compiledTemplate({str: 'Hello world!', length: 40}),
        'Hello world!',
        'Test cutting of a string that is shorter than specified length'
    );

    equal(
        compiledTemplate({str: 'Hello world!', length: 5}),
        'Hello',
        'Test cutting of a string that is longer than specified length'
    );
});
