import axios from "axios";
import loadImages from "./loadImages";
import lazyLoadImages from "./lazyLoading";
import slugify from "slugify";
const truncateString = str => str.trim().length <= 20 ?  str : str.trim().substring(0, 20) + "...";
$('button#button-load-more-js').on('click', (e) => {
    e.preventDefault()

    let latestIdPost = $('span#post-id').data('latest-id');
    let button = $('button#button-load-more-js')
    $(button).attr('disabled', 'disabled')
    $(button).html(`<div class="spinner-border spinner-border-sm" role="status"><span class="sr-only">Loading...</span></div>`)

    let url = window.location.origin + '/api/load_more'

    axios.post(url, {
        latestPost: latestIdPost
    })
        .then(async (response) => {
            let morePosts = response.data.morePosts
            response.data.posts.map((post) => {
                $('div#recent-posts').append(`
                    <div class="col-lg-3 col-md-4 col-sm-6 d-flex flex-column justify-content-between mb-2" data-toggle="tooltip" data-placement="top" title="${post.minDescription}">
                    <!-- start new section -->
                        <section>
                            <div class="image-container mw-100 rounded-lg overflow-hidden position-relative" style="min-height: 118px;">
                                <a href="${window.location.origin + '/' + slugify(post.title, { lower: true, remove: /[*+~.()'"!:@]/g }) + '-' + post.id}"
                                   class="posts-images-link">
                                    <div class="load-overlay fade">
                                        <div class="spinner-border" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </div>
                                    <img alt=""
                                         class="rounded-lg posts-images fade img-fluid"
                                         data-lazy="${post.imageNameCached}"
                                         src="">
                                </a>
                            </div>
                            <div class="mt-2">
                                <a class="btn-link"
                                   href="${window.location.origin + '/' + slugify(post.title, { lower: true, remove: /[*+~.()'"!:@]/g }) + '-' + post.id}">
                                    <h6 class="font-25 m-0 text-wrap">${truncateString(post.title)}</h6>
                                </a>
                                <div>
                                    <span class="badge badge-primary">${post.category.name}</span>
                                    <span class="badge badge-dot badge-secondary">${post.formattedCreatedAt}</span>
                                </div>
                            </div>
                        </section>
                    <!-- end new section -->
                    </div>
                `)
                lazyLoadImages()
            })

            // latest post id
            $('span#post-id').data('latest-id', response.data.posts[response.data.posts.length - 1].id)

            if (!morePosts) $('div#div-button-display').remove()
        })
        .catch((error) => {
            console.log(error)
        })
        .then(() => {
            $(button).attr('disabled', false)
            $(button).html('Load more<em class="icon ni ni-chevron-down"></em>')
        })

})