
const cerrarSesionLink = document.getElementById('cerrarSesion');

if (cerrarSesionLink) {
    cerrarSesionLink.addEventListener('click', (event) => {
        event.preventDefault(); // Evita la navegación inmediata

        Swal.fire({
            title: '¿Estás seguro de cerrar sesión?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, cerrar sesión',
            cancelButtonText: 'No, cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '../php/logout.php'; // Redirige solo si el usuario confirma
            }
        });
    });
}