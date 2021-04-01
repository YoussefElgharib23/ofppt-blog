import React, { Component } from 'react'

export default class Post extends Component {
    render() {
        return (
            <div>
                <div class="col-lg-3 col-md-4 col-6 d-flex flex-column justify-content-between mb-2">
                    <section>
                        <div class="image-container mw-100 rounded-lg overflow-hidden position-relative" style="min-height: 118px;">
                            <a href="" class="posts-images-link">
                                <div class="load-overlay fade">
                                    <div class="spinner-border" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                                <img alt="" class="rounded-lg posts-images fade img-fluid" data-lazy="${post.imageNameCached}" src="">
                            </a>
                        </div>
                        <div class="mt-2">
                            <a class="btn-link"
                                href="">
                                <h6 class="m-0 text-wrap">Post title</h6>
                            </a>
                            <div>
                                <span class="badge badge-primary"></span>
                                <span class="badge badge-dot badge-secondary"></span>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        )
    }
}
