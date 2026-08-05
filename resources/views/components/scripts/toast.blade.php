<script> 

    window.GPSTracker = window.GPSTracker || {};

    GPSTracker.showToast = function (

        type,

        title,

        message

    ) {

        const toast = document.getElementById('toast');

        const icon = document.getElementById('toast-icon');

        const toastTitle = document.getElementById('toast-title');

        const toastMessage = document.getElementById('toast-message');

        if (

            !toast ||

            !icon ||

            !toastTitle ||

            !toastMessage

        ) {

            return;

        }

        toastTitle.textContent = title;

        toastMessage.textContent = message;

        if (type === 'success') {

            icon.className =
                'flex h-11 w-11 items-center justify-center rounded-full bg-green-100 text-green-700';

            icon.textContent = '✓';

        } else {

            icon.className =
                'flex h-11 w-11 items-center justify-center rounded-full bg-red-100 text-red-700';

            icon.textContent = '✕';

        }

        toast.classList.remove(

            'opacity-0',

            'translate-x-8'

        );

        toast.classList.add(

            'opacity-100',

            'translate-x-0'

        );

        clearTimeout(

            this.toastTimer

        );

        this.toastTimer = setTimeout(() => {

            toast.classList.add(

                'opacity-0',

                'translate-x-8'

            );

            toast.classList.remove(

                'opacity-100',

                'translate-x-0'

            );

        }, 3000);

    };
</script>