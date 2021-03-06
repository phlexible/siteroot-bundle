Ext.provide('Phlexible.siteroots.grid.LanguageCheckColumn');

Phlexible.siteroots.grid.LanguageCheckColumn = function (config) {
    Ext.apply(this, config);
    if (!this.id) {
        this.id = Ext.id();
    }
    this.renderer = this.renderer.createDelegate(this);
};

Phlexible.siteroots.grid.LanguageCheckColumn.prototype = {
    init: function (grid) {
        this.grid = grid;
        this.grid.on('render', function () {
            var view = this.grid.getView();
            view.mainBody.on('mousedown', this.onMouseDown, this);
        }, this);
    },

    onMouseDown: function (e, t) {
        if (t.className && t.className.indexOf('x-grid3-cc-' + this.id) != -1) {
            e.stopEvent();

            var index = this.grid.getView().findRowIndex(t);
            var record = this.grid.store.getAt(index);
            record.set(this.dataIndex, !record.data[this.dataIndex]);

            var newValue = record.data[this.dataIndex];
            var langIso = record.data[this.languageIndex];

            if (newValue) {
                // disable others
                for (var i = this.grid.store.getCount() - 1; i >= 0; --i) {
                    if (i != index) {
                        record = this.grid.store.getAt(i);
                        if (langIso == record.data[this.languageIndex]) {
                            record.set(this.dataIndex, false);
                        }

                    }
                }
            }

        }
    },

    renderer: function (v, p, record) {
        p.css += ' x-grid3-check-col-td';
        return '<div class="x-grid3-check-col' + (v ? '-on' : '') + ' x-grid3-cc-' + this.id + '">&#160;</div>';
    }
};
