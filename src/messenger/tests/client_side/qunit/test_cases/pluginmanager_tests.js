// Testing PluginManager class
module('PluginManager');

test('getPlugin', function() {
    var pluginManager = new PluginManager();
    // Try to load not stored plugin
    equal(
        pluginManager.getPlugin('WrongPlugin'),
        false,
        'Test loading not stored plugin'
    )

    // Try save and load test plugin
    var testPlugin = {'testField': 'testValue'}
    pluginManager.addPlugin('TestPlugin', testPlugin);
    deepEqual(
        pluginManager.getPlugin('TestPlugin'),
        testPlugin,
        'Test loading stored plugin'
    );
});