import axios from "axios";

$(document).ready(() => {
    $('#update-password-form-type').on('submit', (e) => {
        e.preventDefault()

        let password = $('#old-password').val(),
            newPassword = $('#new-password').val(),
            confirmPassword = $('#confirm-new-password').val(),
            errorDiv = $('#update-password-error-js'),
            token = $(`input[name=_token]`).val()

        if (password.trim() === '' || newPassword.trim() === '' || confirmPassword.trim() === '') {
            errorDiv.html('Please fill all the input before you submit !')
            errorDiv.removeClass('d-none')
            return false;
        }

        if (newPassword !== confirmPassword) {
            errorDiv.html('Your new password must match the confirmation !')
            errorDiv.removeClass('d-none')
            return false;
        }

        errorDiv.html('')
        errorDiv.addClass('d-none')

        let data = {
            oldPassword: password,
            newPassword: newPassword,
            confirmNewPassword: confirmPassword,
            token: token
        }

        let url = window.location.origin + '/api/user/update-password'
        axios.post(url, data)
            .then((response) => {
                if (response.data.message) {
                    setTimeout(() => {
                        window.location.reload()
                    }, 500)
                }
            })
            .catch(error => {
                errorDiv.html(error.response.data.error)
                errorDiv.removeClass('d-none')
            })
    })
})