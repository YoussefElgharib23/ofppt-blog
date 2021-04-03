import axios from "axios";
import lazyLoadImages from "./lazyLoading";
import slugify from "slugify";

const truncateString = str => str.trim().length <= 20 ? str : str.trim().substring(0, 20) + "...";

$('button#button-load-more-js').on('click', (e) => {
    e.preventDefault()

    let latestIdPost = $('span#post-id').data('latest-id');
    let button = $('button#button-load-more-js')
    $(button).attr('disabled', 'disabled')
    $(button).html(`<div class="spinner-border spinner-border-sm" role="status"><span class="sr-only">Loading...</span></div>`)

    let url = '/api/load_more'

    $.ajax(
        '/api/load_more', {
            type: 'POST',
            data: {
                latestPost: latestIdPost
            },
            success: (response) => {
                console.log(response)
                $('div#recent-posts').append(response)
                lazyLoadImages()
                $('span#post-id').data('latest-id', $('#recent-posts').find('.col-lg-3').last().data('id'))

                // need to another ajax call to get the Moreposts
                $.ajax(
                    '/api/more-posts', {
                        type: 'POST',
                        data: {
                            latestPost: latestIdPost
                        },
                        success: (response) => {
                            console.log(response)
                            if (!response.morePosts) $('div#div-button-display').remove()
                            else {
                                $(button).attr('disabled', false)
                                $(button).html('Load more<em class="icon ni ni-chevron-down"></em>')
                            }
                        }
                    }
                )
            }
        }
    )
})