function isEmpty(value) {
    if (typeof value === 'string') {
        return value === '' || value.trim() === '';
    } else {
        return value === null || value === undefined;
    }
}

if (!String.prototype.startsWith) {
    Object.defineProperty(String.prototype, 'startsWith', {
        enumerable: false,
        configurable: false,
        writable: false,
        value: function(searchString, position) {
            position = position || 0;
            return this.indexOf(searchString, position) === position;
        }
    });
}
