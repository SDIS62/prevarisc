document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.retry-export-platau').forEach((link) => {
        link.addEventListener('click', function() {
            $.post(
                "/piece-jointe/retry-export-platau",
                { idPj: link['attributes']['data-id'].value },
                () => {
                    location.reload()
                }
            )
        })
    })
})