$(document).ready(() => {
    let loading = $('div#loading-page-home')
    if (loading) {
        setTimeout(() => {
            loading.addClass('fade');
            setTimeout(() => {
                let mainContent = $('div#main-content')
                mainContent.css('display', 'block');
                setTimeout(() => {
                    mainContent.addClass('show');
                    loading.remove();
                }, 200)
            }, 500)
        }, 1500)
    }
})