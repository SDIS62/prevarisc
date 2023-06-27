document.addEventListener('DOMContentLoaded', function() {
    const retryExportPec = document.querySelector('#retry-export-pec')

    if (!!retryExportPec) {
        retryExportPec.addEventListener('click', function () {
            $.post(
                "/platau/retry-export-pec",
                { id: retryExportPec['attributes']['data-id'].value },
                () => {
                    location.reload()
                }
            )
        })
    }

    const retryExportAvis = document.querySelector('#retry-export-avis')

    if (!!retryExportAvis) {
        retryExportAvis.addEventListener('click', function () {
            $.post(
                "/platau/retry-export-avis",
                { id: retryExportAvis['attributes']['data-id'].value },
                () => {
                    location.reload()
                }
            )
        })
    }
})