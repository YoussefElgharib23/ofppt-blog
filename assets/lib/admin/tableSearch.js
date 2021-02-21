// $(document).ready(() => {
//     let $input = $('input#search-input-js')
//     if ($input.val().trim() !== '') {
//         searchForTableElements()
//     }
//     if ($('table.table-data-js').length) {
//         $input.on('keyup', searchForTableElements)
//         $input.on('change', searchByStatus)
//
//         function searchByStatus() {
//
//             let value = $('select#select-status').val()
//             let trElements = $('tbody').find('tr')
//             let inputValue = $('input#search-input-js').val()
//
//             if (value === 'all') {
//
//                 trElements.each((i, ele) => {
//                     if (inputValue.trim() === '') {
//                         $(ele).css('display', '')
//                     } else if (inputValue.trim() !== '' && $(ele).hasClass('show-tr')) {
//                         $(ele).css('display', '')
//                     }
//                 })
//
//             } else {
//
//                 trElements.each((i, ele) => {
//                     $(ele).css('display', 'none')
//                 })
//                 trElements.each((i, ele) => {
//                     if ($(ele).data('status') === value && inputValue.trim() === '') {
//                         $(ele).css('display', '')
//                     } else if ($(ele).data('status') === value && inputValue.trim() !== '' && $(ele).hasClass('show-tr')) {
//                         $(ele).css('display', '')
//                     } else {
//                         $(ele).css('display', 'none')
//                     }
//                 })
//
//             }
//
//         }
//
//         function searchForTableElements() {
//             // GET THE VALUE FOR LOOKING FOR
//             let value = $('input#search-input-js').val().trim().toLowerCase()
//             // GET ALL THE ELEMENTS TR
//             let trElements = $('tbody').find('tr')
//
//             if (value !== '') {
//
//                 // SET ALL THE ELEMENT TO HIDE MODE
//                 trElements.each((i, ele) => {
//                     $(ele).css('display', 'none')
//                     $(ele).removeClass('show-tr')
//                 })
//
//                 let founded = false
//                 // CHECK IF THE VALUE iS NUMBER
//                 if (parseInt(value)) {
//
//                     // SHOW THE EXACT TR
//                     let foundedTr = $(`tr[data-id=${value}]`)
//
//                     foundedTr.css('display', '')
//                     foundedTr.addClass('show-tr')
//                     founded = true
//
//                 } else {
//
//                     trElements.each((i, ele) => {
//                         if ($(ele).data('title').trim().toLowerCase().indexOf(value) > -1) {
//                             $(ele).css('display', '')
//                             $(ele).addClass('show-tr')
//                         }
//                     })
//                     founded = true
//
//                 }
//
//                 trElements.each((i, ele) => {
//                     if ($(ele).hasClass('show-tr')) {
//
//                         founded = true
//                         return false;
//
//                     } else {
//                         founded = false
//                     }
//                 })
//
//                 if (founded === false) {
//
//                     $('#not-found').css('display', '')
//                     $('table').css('display', 'none')
//
//                 } else {
//                     $('#not-found').css('display', 'none')
//                     $('table').css('display', '')
//
//                 }
//             } else {
//
//                 // SHOW ALL THE ELEMENTS IF THERE IS NOT VALUE IN THE INPUT
//                 $('#not-found').css('display', 'none')
//                 $('table').css('display', '')
//                 trElements.each((i, ele) => {
//                     $(ele).css('display', '')
//                     $(ele).removeClass('show-tr')
//                 })
//
//             }
//
//             searchByStatus()
//         }
//     }
// })