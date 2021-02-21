$(document).ready(() => {

    const getValueToSpan = function (e) {
        $('span#select-value-js').text(e.params.data.id)
    }

    let $eventSelect = $('#options-select')
    $eventSelect.on("select2:select", getValueToSpan)
})