document.addEventListener('DOMContentLoaded', function() {
    const ATTRIBUTE_TABLE_ID = 'data-table-id';
    const ATTRIBUTE_COLUMN_ID = 'data-field-id';
    const SELECTOR_TABLE = 'table.registry-records_grid__table_resizable';
    const MIN_COL_WIDTH = 30;

    /** @type {HTMLElement} */
    var currentTable = undefined;
      /** @type {Number} */
    var currentTableWidth = undefined;
      /** @type {Number} */
    var currentTableWidthMax = undefined;
    /** @type {HTMLElement} */
    var currentCol = undefined;
    /** @type {Number} */
    var currentColWidth = undefined;
        /** @type {HTMLElement} */
    var nextCol = undefined;
    /** @type {Number} */
    var nextColWidth = undefined;
    /** @type {Number} */
    var pageX = undefined;

    /**
     * @param {Function} callback
     * @param {Number} limit
     */
    function throttle(callback, limit) {
        var waiting = false;
        return function () {
            if (waiting) {
                return;
            }

            callback.apply(this, arguments);
            waiting = true;

            setTimeout(function () {
                waiting = false;
            }, limit);
        }
    }

    /**
     * @param {HTMLElement} table
     */
    function initTable(table) {
        const columns = getTableColumns(table);

        Array.prototype.forEach.call(columns, function (column) {
            const grip = createGrip(column);
            column.appendChild(grip);
            grip.addEventListener('mousedown', onMouseDown);
        });

        restoreColumnWidths(table);
        table.classList.remove('registry-records_grid__table_resizable-loading');

        /**
         * @param {MouseEvent} e
         */
        function onMouseDown(e) {
            pageX = e.pageX;
            currentTable = table;
            currentTableWidth = getTableWidth(columns);
            currentTableWidthMax = getElementWidth(table.parentElement);
            currentCol = e.target.parentElement;
            currentColWidth = currentCol.offsetWidth;

            if (currentCol.nextElementSibling) {
                nextCol = currentCol.nextElementSibling;
                nextColWidth = nextCol.offsetWidth;
            }
        }
    }

    /**
     * @param {HTMLCollection} columns
     * @return {Number}
     */
    function getTableWidth(columns)
    {
        var width = 0;
        Array.prototype.forEach.call(columns, function (column) {
            width += column.offsetWidth;
        });
        return width;
    }

    /**
     * @param {HTMLElement} table
     * @return {HTMLCollection}
     */
    function getTableColumns(table)
    {
        const tableHead = table.getElementsByTagName('thead')[0];
        if (!tableHead) {
            return undefined;
        }

        const tableHeadRow = tableHead.getElementsByTagName('tr')[0];
        if (!tableHeadRow) {
            return undefined;
        }

        return tableHeadRow.children;
    }

    /**
     * @param {HTMLElement} table
     */
    function saveColumnWidths(table) {
        Array.prototype.forEach.call(getTableColumns(table), function (column) {
            if (window['store']) {
                window['store'].set(generateColumnId(table, column), parseInt(column.offsetWidth));
            }
        });
    }

    /**
     * @param {HTMLElement} table
     */
    function restoreColumnWidths(table) {
        var tableWidth = 0;

        Array.prototype.forEach.call(getTableColumns(table), function (column) {
            if (window['store']) {
                const width = window['store'].get(generateColumnId(table, column));
                if (width != null) {
                    tableWidth += width;
                    column.style.width = width + 'px';
                }
            }
        });

        if (tableWidth > 0) {
            table.style.width = tableWidth + 'px';
        }
    }

    /**
     * @param {HTMLElement} table
     * @param {HTMLElement} column
     * @return {String}
     */
    function generateColumnId(table, column) {
        return `t-${table.getAttribute(ATTRIBUTE_TABLE_ID)}-f-${column.getAttribute(ATTRIBUTE_COLUMN_ID)}`;
    }

    /**
     * @param {MouseEvent} e
     */
    function onMouseUp(e) {
        if (currentTable) {
            saveColumnWidths(currentTable);
        }

        currentTable = undefined;
        currentTableWidth = undefined;
        currentTableWidthMax = undefined;
        currentCol = undefined;
        currentColWidth = undefined;
        nextCol = undefined;
        nextColWidth = undefined;
        pageX = undefined;
    }

    /**
     * @param {MouseEvent} e
     */
    function onMouseMove(e) {
        if (!currentCol) {
            return;
        }

        const diffX = e.pageX - pageX;
        const newColWidth = currentColWidth + diffX;
        const newTableWidth = currentTableWidth + diffX;

        if (newTableWidth >= currentTableWidthMax) {
            if (newColWidth >= MIN_COL_WIDTH) {
                currentTable.style.width = (currentTableWidth + diffX) + 'px';
                currentCol.style.width = newColWidth + 'px';
            }
        } else {
            currentTable.style.width = currentTableWidthMax + 'px';

            if (newColWidth >= MIN_COL_WIDTH) {
                currentCol.style.width = newColWidth + 'px';

                if (nextCol) {
                    nextCol.style.width = (nextColWidth - diffX) + 'px';
                }
            }
        }
    }

    /**
     * @param {Element} column
     * @return {HTMLElement}
     */
    function createGrip(column) {
        const grip = document.createElement('div');
        grip.classList.add('registry-records_grid__table_resizable-grip');
        return grip;
    }

    /**
     * @param {HTMLElement} element
     * @param {Boolean} withPadding
     * @return {Number}
     */
    function getElementWidth(element, withPadding = false)
    {
        if (withPadding) {
            return element.clientWidth;
        } else {
            const style = getComputedStyle(element);
            const padding = parseFloat(style.paddingLeft) + parseFloat(style.paddingRight);

            return element.clientWidth - padding;
        }
    }

    document.addEventListener('mouseup', onMouseUp);
    document.addEventListener('mousemove', throttle(onMouseMove, 1000 / 60));
    document.querySelectorAll(SELECTOR_TABLE).forEach(initTable);
});
