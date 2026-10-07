import Swal from 'sweetalert2';

// Toast Notification
export const toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    },
});

// SweetAlert Modal Helper
export const notifySuccess = (title, text = '') => {
    return Swal.fire({
        icon: 'success',
        title: title,
        text: text,
        confirmButtonColor: '#DC2626', // Japan Red
        customClass: {
            popup: 'rounded-2xl shadow-xl font-sans',
            title: 'text-slate-800 font-bold text-xl',
            confirmButton: 'px-6 py-2.5 rounded-xl font-semibold text-sm',
        },
    });
};

export const notifyError = (title, text = '') => {
    return Swal.fire({
        icon: 'error',
        title: title,
        text: text,
        confirmButtonColor: '#DC2626',
        customClass: {
            popup: 'rounded-2xl shadow-xl font-sans',
            title: 'text-slate-800 font-bold text-xl',
            confirmButton: 'px-6 py-2.5 rounded-xl font-semibold text-sm',
        },
    });
};

export const confirmDialog = (title, text, confirmButtonText = 'Ya, Lanjutkan') => {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#64748B',
        confirmButtonText: confirmButtonText,
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-2xl shadow-2xl font-sans',
            title: 'text-slate-800 font-bold text-xl',
            confirmButton: 'px-6 py-2.5 rounded-xl font-semibold text-sm',
            cancelButton: 'px-6 py-2.5 rounded-xl font-semibold text-sm',
        },
    });
};

export default {
    toast,
    notifySuccess,
    notifyError,
    confirmDialog,
};
