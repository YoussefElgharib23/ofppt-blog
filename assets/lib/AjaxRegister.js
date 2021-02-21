import axios from 'axios';


$('#form-register').on('submit', (event) => {
    event.preventDefault()
    toggleButton('off')
    let success = true;
    let form = $('form#form-register')
    let dataUser = form.find('input[data-target=ajax-check]').map((index, ele) => {
        let value = $(ele).val()

        if ( value.trim() !== '' ) {
            return value
        }
        success = false
        return success
    });


    if ( !success ) {
        toggleButton('on')
        return
    }

    let url = window.location.origin + '/api/verify_credentials'
    axios.post(url, {
        username: dataUser[0],
        email: dataUser[1]
    })
    .then(function (response) {
        let results = response.data
        if ( results.error ) {
            showErrorMessage(results.error)
            toggleButton('on')
        }
        else {
            removeErrorMessage()
            success = true
            event.target.submit()
        }
    })
    .catch(function (error) {
        console.log(error.message)
        toggleButton('on')
        showErrorMessage('An error has occurred during the operation please try again !')
    })
})

function showErrorMessage(message) {
    let alert = $('.alert.alert-danger')
    removeErrorMessage()
    alert.slideToggle()
    setTimeout(() => {
        alert.find('#message-alert-js').text(message)
    }, 500)
}

function removeErrorMessage() {
    let alert = $('.alert.alert-danger')
    if ( alert.css('display') !== 'none' ) {
        alert.slideToggle()
        alert.find('#message-alert-js').text('')
    }
}

function toggleButton(status) {
    let button = $('#submit-btn-form-js');
    if ( status === 'off') {
        button.attr('disabled', true)
        button.html(`
            <div class="spinner-border spinner-border-sm" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        `)
    }
    else {
        button.attr('disabled', false)
        button.text('Create new account')
    }
}