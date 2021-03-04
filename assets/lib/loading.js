let loading = $('div#loading-page-home')
if (loading) loading.addClass('fade')
$(document).ready(() => {
    setTimeout(() => {
        let mainContent = $('div#main-content')
        mainContent.css('display', 'block');
        setTimeout(() => {
            mainContent.addClass('show');
            loading.remove();
        }, 200)
    }, 500)
})