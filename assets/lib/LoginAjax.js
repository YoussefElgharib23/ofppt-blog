import axios from 'axios'

$('form#form-login-js').on('submit', (e) => {
    e.preventDefault()

    let form = e.target;
    let button = $(form).find('button')
    toggleButtonSubmit(button, 'off')
    let success = true

    let credentials = $(form).find('input#credentials').val(),
        password = $(form).find('input#inputPassword').val()
    credentials = credentials.trim()

    if ( credentials === '' ) {
        success = false
    }

    if ( !success ) {
        toggleButtonSubmit(button)
        return
    }
    let url = window.location.origin + '/api/verify_credentials_login'
    console.log(credentials)
    axios.post(url, {
        credentials: credentials,
        password: password
    })
    .then((response) => {
        let results = response.data
        if ( results.error ) {
            showErrorMessage(results.error)
            toggleButtonSubmit(button)
        }
        else {
            e.target.submit()
        }
    })
    .catch((error) => {
        let message = error.message;
        showErrorMessage('An error has occurred please try again !')
        toggleButtonSubmit(button)
    })
})

function toggleButtonSubmit(btn, action = 'on') {
    let button = $(btn)
    if ( action === 'off' ) {
        button.attr('disabled', true)
        button.html(`<div class="spinner-border spinner-border-sm" role="status"><span class="sr-only">loading ...</span></div>`)
        return
    }

    button.attr('disabled', false)
    button.text('Sign in')
}

function showErrorMessage(message) {
    if ( message !== '' ) {
        let alertDiv = $('#alert-danger')
        removeMessageError()

        setTimeout(() => {
            alertDiv.slideToggle()
            alertDiv.find('span#message-alert-js').text(message)
        }, 700)
    }
}

function removeMessageError() {
    let alertDiv = $('#alert-danger')
    if ( alertDiv.css('display') !== 'none' ) {
        alertDiv.slideToggle()
        alertDiv.find('span#message-alert-js').text('')
    }
}