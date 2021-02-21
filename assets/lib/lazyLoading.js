import loadImages from "./loadImages";

export default function lazyLoadImages () {
    const targets = document.querySelectorAll('img.posts-images')


    const lazyload = target => {
        const io = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if ( entry.isIntersecting ) {
                    const img = entry.target
                    const src = img.getAttribute('data-lazy')

                    img.setAttribute('src', src)

                    observer.disconnect()

                    loadImages()
                }
            })
        })

        io.observe(target)
    }

    targets.forEach(lazyload)
}