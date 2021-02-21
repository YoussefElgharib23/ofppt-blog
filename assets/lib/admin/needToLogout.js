if ($('#needToLogout').length) {
    window.location.replace($('#needToLogout').text());
    NioApp.Toast('For security purpose we logout you !');
}