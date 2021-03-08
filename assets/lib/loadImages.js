import imagesLoaded from 'imagesloaded'

imagesLoaded.makeJQueryPlugin($)

export default function loadImages()
{
    $('body').imagesLoaded().progress(function (instance, image) {
        let img = $(image.img)
        $(img).parent().find('.load-overlay').addClass('show')
        if ( image.isLoaded ) {
            setTimeout(() => {
                $(img).addClass('show')
            }, 0)
            setTimeout(() => {
                if ($(img).parent().find('.load-overlay').length) {
                    $(img).parent().find('.load-overlay').removeClass('show')
                    $(img).parent().find('.load-overlay').remove();
                }
            }, 0)
            $(img).parents('.image-container').css('min-height', '')
        }
    })
}