// Testing Localization system
module('Localization');

test('Basic things', function() {
    // Fill localization container
    Mibew.Localization.set({
        one: 'uno',
        'Hello {0}, {1} and {2}!': '¡Hola {0}, {1} y {2}!'
    });

    equal(
        Mibew.Localization.trans('one'),
        'uno',
        'Test simple string'
    );

    equal(
        Mibew.Localization.trans('Hello {0}, {1} and {2}!', 'Foo', 'Bar', 'Baz'),
        '¡Hola Foo, Bar y Baz!',
        'Test placeholders'
    );
});
