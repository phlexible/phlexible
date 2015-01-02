Phlexible.Handles.add('searchbox', function() {
    return new Phlexible.gui.menuhandle.handle.Handle({
        createConfig: function (data) {
            return new Phlexible.search.SearchBox({
                bla: 1,
                width: 150
            });
        }
    });
});

Phlexible.Handles.add('searchboxseparator', function() {
    return new Phlexible.gui.menuhandle.handle.Handle({
        createConfig: function (data) {
            return '-';
        }
    });
});
